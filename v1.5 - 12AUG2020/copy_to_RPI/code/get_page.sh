#!/bin/bash

line=$(head -n 1 /var/www/html/website/config/configuration.txt)
file=/var/www/html/code/url_response.txt
if [ -f "$file" ]; then
    wget -q -i "$line" -O $file --timeout=2
else
    echo > $file
    chown pi:pi $file 
fi
