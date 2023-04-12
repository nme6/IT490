<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class FibonacciRpcClient
{
    private $connection; // holds the AMQP connection instance
    private $channel; // holds the channel instance for the connection
    private $callback_queue; // holds the name of the callback queue
    private $response; // holds the response received from the RPC server
    private $corr_id; // holds the correlation ID for the RPC request

    public function __construct()
    {
        // establish the AMQP connection
        $this->connection = new AMQPStreamConnection(
            'localhost',
            5672,
            'guest',
            'guest'
        );

        // create a channel on the connection
        $this->channel = $this->connection->channel();

        // create a temporary queue for receiving the response from the server
        list($this->callback_queue, ,) = $this->channel->queue_declare(
            "",
            false,
            false,
            true,
            false
        );

        // register a callback function for handling the response received on the callback queue
        $this->channel->basic_consume(
            $this->callback_queue,
            '',
            false,
            true,
            false,
            false,
            array(
                $this,
                'onResponse'
            )
        );
    }

    public function onResponse($rep)
    {
        // check if the correlation ID of the response matches that of the request
        if ($rep->get('correlation_id') == $this->corr_id) {
            $this->response = $rep->body; // store the response in the $response property
        }
    }

    public function call($n)
    {
        $this->response = null; // reset the response to null
        $this->corr_id = uniqid(); // generate a unique correlation ID for the request

        // create a new AMQP message with the request payload and correlation ID
        $msg = new AMQPMessage(
            (string) $n,
            array(
                'correlation_id' => $this->corr_id,
                'reply_to' => $this->callback_queue
            )
        );

        // publish the message to the 'rpc_queue' exchange
        $this->channel->basic_publish($msg, '', 'rpc_queue');

        // wait for a response to be received on the callback queue
        while (!$this->response) {
            $this->channel->wait();
        }

        // return the response as an integer
        return intval($this->response);
    }
}

// create a new instance of the FibonacciRpcClient class
$fibonacci_rpc = new FibonacciRpcClient();

// call the Fibonacci sequence function on the server with an input of 30
$response = $fibonacci_rpc->call(30);

// print the response received from the server
echo ' [.] Got ', $response, "\n";
?>

