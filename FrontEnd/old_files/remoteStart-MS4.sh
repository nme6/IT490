#!/bin/bash

if sudo systemctl is-active apache2 > /dev/null 2>&1; then
    echo "######################[Bash Script]######################"
    echo " Apache2 server is already running! No need to execute.   "
    echo "#########################################################"
    sudo systemctl status apache2 | grep -E "Loaded|Active|Docs|Main|Tasks|Memory|CPU"
    echo "#########################################################"
else
    sudo systemctl start apache2
    
    echo "######################[Bash Script]######################"
    echo "       Apache2 server was off, and is now started!       "
    echo "#########################################################"
    sudo systemctl status apache2 | grep -E "Loaded|Active|Docs|Main|Tasks|Memory|CPU"
    echo "#########################################################"
fi

# ========== Registration Step 5 ========== #
if pgrep -f "regStep5-MS4.php" > /dev/null;
then
	echo "File [regStep5] is running"
else
	echo "File [regStep5] is not running, starting it now"
	gnome-terminal -- php IT490ApacheSite/regStep5-MS4.php
fi

# ========== Login Step 5 ========== #
if pgrep -f "logStep5-MS4.php" > /dev/null;
then    
        echo "File [logStep5] is running"
else    
        echo "File [logStep5] is not running, starting it now"
        gnome-terminal -- php IT490ApacheSite/logStep5-MS4.php
fi
