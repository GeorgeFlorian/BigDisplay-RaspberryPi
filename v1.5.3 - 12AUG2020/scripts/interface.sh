#!/bin/bash

current_zip=/var/www/html/website/Metrici_MP_Display.zip
uploaded_zip=/var/www/html/uploads/Metrici_MP_Display.zip

if [ -f "$current_zip" ]; then
    rm "$current_zip"
fi

if [ -f "$uploaded_zip" ]; then
    mv "$uploaded_zip" /var/www/html/website/
    if [ $? -eq 0 ]; then
        unzip -o "$current_zip"
        if [ $? -eq 0 ]; then
            rm "$current_zip"
            chown -R www-data:www-data /var/www/html/
            echo "0"
        else
            echo "1"
        fi
    fi
else
    echo "1"
fi


