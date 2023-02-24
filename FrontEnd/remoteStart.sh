#!/bin/bash

if sudo systemctl is-active apache2 > /dev/null 2>&1; then
    echo "######################[Bash Script]######################"
    echo " Apache2 server is already running! No need to execute.   "
    echo "#########################################################"
    sudo systemctl status apache2
    echo "#########################################################"
else
    sudo systemctl start apache2
    
    echo "######################[Bash Script]######################"
    echo "       Apache2 server was off, and is now started!       "
    echo "#########################################################"
    sudo systemctl status apache2
    echo "#########################################################"
fi
