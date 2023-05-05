#!/bin/bash

# Check if MySQL server is already running
if sudo systemctl is-active mysql > /dev/null 2>&1; then
    echo "MySQL is already started, here is the status"
    echo "MySQL status:"
    sudo systemctl status mysql | grep -E "Loaded|Active|Process|Main|Status"
else
    # Start MySQL server
    sudo systemctl start mysql
    echo "MySQL was off, and is now started"
    echo "MySQL status:"
    sudo systemctl status mysql | grep -E "Loaded|Active|Process|Main|Status"
fi

# Check if RegisterStep3 is already running
if pgrep -f "php regStep3-MS4.php" > /dev/null; then
    echo "###############################"
    echo "RegisterStep3 is already running"
    echo "###############################"
else
    # Start RegisterStep3 in a new terminal window
    echo "#################################"
    echo "Starting RegisterStep3"
    echo "#################################"
    gnome-terminal -- /bin/bash -c "php regStep3-MS4.php; exec bash"
    echo "#################################"
fi

# Check if LoginStep3 is already running
if pgrep -f "php logStep3-MS4.php" > /dev/null; then
    echo "###############################"
    echo "LoginStep3 is already running"
    echo "###############################"
else
    # Start LoginStep3 in a new terminal window
    echo "#################################"
    echo "Starting LoginStep3"
    echo "#################################"
    gnome-terminal -- /bin/bash -c "php logStep3-MS4.php; exec bash"
    echo "#################################"
fi

# Check if Pokemon_Check is already running
if pgrep -f "php pokemon_check.php" > /dev/null; then
    echo "###############################"
    echo "Pokemon_Check is already running"
    echo "###############################"
else
    # Start Pokemon_Check in a new terminal window
    echo "#################################"
    echo "Starting Pokemon_Check"
    echo "#################################"
    gnome-terminal -- /bin/bash -c "php pokemon_check.php; exec bash"
    echo "#################################"
fi

# Check if Pokemon_Insert is already running
if pgrep -f "php pokemon_insert.php" > /dev/null; then
    echo "###############################"
    echo "Pokemon_Check is already running"
    echo "###############################"
else
    # Start Pokemon_Insert in a new terminal window
    echo "#################################"
    echo "Starting Pokemon_Insert"
    echo "#################################"
    gnome-terminal -- /bin/bash -c "php pokemon_insert.php; exec bash"
    echo "#################################"
fi
