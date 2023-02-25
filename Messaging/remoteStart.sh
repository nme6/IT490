#!/bin/bash

if sudo systemctl is-active rabbitmq-server > /dev/null 2>&1; then
	echo "#################[Bash Script]###########################"
	echo " RabbitMQ server is already running! No need to execute. "
	echo "#########################################################"
	sudo systemctl status rabbitmq-server
	echo "#########################################################"
else
	sudo systemctl start rabbitmq-server
	
	echo "#################[Bash Script]###########################"
	echo "       RabbitMQ server was off, and is now started!      "
        echo "#########################################################"
	sudo systemctl status rabbitmq-server
	echo "#########################################################"
fi
