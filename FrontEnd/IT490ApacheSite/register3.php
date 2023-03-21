<!DOCTYPE html>
<html>
  <head>
    <title>Registration Form</title>
  </head>
  <body>
    <h1>Registration Form</h1>
    <form method="post">
      <label for="username">Username:</label>
      <input type="text" name="username" id="username" required><br>

      <label for="password">Password:</label>
      <input type="password" name="password" id="password" required><br>

      <label for="confirm">Confirm Password:</label>
      <input type="password" name="confirm" id="confirm" required><br>

      <label for="email">Email:</label>
      <input type="email" name="email" id="email" required><br>

      <label for="firstname">First Name:</label>
      <input type="text" name="firstname" id="firstname" required><br>

      <label for="lastname">Last Name:</label>
      <input type="text" name="lastname" id="lastname" required><br>

      <input type="submit" value="Submit">
    </form>

    <?php
      require_once __DIR__ . '/vendor/autoload.php';

      use PhpAmqpLib\Connection\AMQPStreamConnection;
      use PhpAmqpLib\Message\AMQPMessage;

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirm = $_POST['confirm'];
        $email = $_POST['email'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];

        // Create a connection to RabbitMQ
        $connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
        $channel = $connection->channel();

        // Declare a queue for sending messages
        $channel->queue_declare('regFE2BE', false, false, false, false);

        // Publish the message to the queue
        $messageBody = json_encode([
          'username' => $username,
          'password' => $password,
          'confirm' => $confirm,
          'email' => $email,
          'firstname' => $firstname,
          'lastname' => $lastname
        ]);

        // Define the message to send
        $message = new AMQPMessage($messageBody);

        // Publish the message to the queue
        $channel->basic_publish($message, '', 'regFE2BE');

        //Echo Msg to console
        echo "-={[Front-end] Sent message to the Back-end!}=-\n$messageBody\n";

        // Close the channel and the connection
        $channel->close();
        $connection->close();
      }
    ?>
  </body>
</html>
