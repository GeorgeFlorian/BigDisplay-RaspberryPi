#!/bin/bash

delete_firmware=/var/www/html/code/Metrici_MP_Display
move_firmware=/var/www/html/uploads/Metrici_MP_Display

if [ -f "$delete_firmware" ]; then
    rm "$delete_firmware"
fi

if [ -f "$move_firmware" ]; then
    mv "$move_firmware" /var/www/html/code/
    if [ $? -eq 0 ]; then
        echo "0"
    else
        echo "1"
    fi
else
    echo "1"
fi


