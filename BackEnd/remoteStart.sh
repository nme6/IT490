#!/bin/bash

php --version


# =================REGISTRATION STEP 2================= #
if pgrep -f "regStep2-MS4.php" > /dev/null;
then
	echo "File [regStep2-MS4] is running"
else
	echo "File [regStep2-MS4] is not running, starting it now"
	gnome-terminal -- php regStep2-MS4.php
	#./regStep2.sh
fi


# =================REGISTRATION STEP 4================= #
if pgrep -f "regStep4-MS4.php" > /dev/null;
then
        echo "File [regStep4-MS4] is running"
else
        echo "File [regStep4-MS4] is not running, starting it now"
        gnome-terminal -- php regStep4-MS4.php
        #./regStep4.sh
fi


# =================LOGIN STEP 2================= #
if pgrep -f "logStep2-MS4.php" > /dev/null;
then
        echo "File [logStep2-MS4] is running"
else
        echo "File [logStep2-MS4] is not running, starting it now"
        gnome-terminal -- php logStep2-MS4.php
        #./logStep2.sh
fi


# =================LOGIN STEP 4================= #
if pgrep -f "logStep4-MS4.php" > /dev/null;
then
        echo "File [logStep4-MS4] is running"
else
        echo "File [logStep4-MS4] is not running, starting it now"
        gnome-terminal -- php logStep4-MS4.php
        #./logStep4.sh
fi



# php /home/ellis/IT490/IT490/BackEnd/regStep2.php &
# php /home/ellis/IT490/IT490/BackEnd/regStep4.php &

#./regStep2.sh
#./regStep4.sh

