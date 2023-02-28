#!/bin/bash

# Check if MySQL server is already running
if sudo systemctl is-active mysql > /dev/null 2>&1; then
  	echo "###############################"
	echo "MySQL server is already running"
	echo "###############################"
	sudo systemctl status mysql
	echo "###############################"
else
  # Start MySQL server
  sudo systemctl start mysql
  echo "#################################"
  echo "MySQL was off, and is now started"
  echo "#################################"
  sudo systemctl status mysql
  echo "#################################"
fi
