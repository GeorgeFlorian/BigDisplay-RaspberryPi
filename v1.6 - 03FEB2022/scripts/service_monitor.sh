# vi /var/www/html/service_monitor.sh

#!/bin/bash

serv=DisplayM

sstat=dead

systemctl status $serv | grep -i 'running\|dead' | awk '{print $3}' | sed 's/[()]//g' | while read output;

do

echo $output

if [ "$output" == "$sstat" ]; then

    sudo systemctl start $serv

    echo "$serv service is now UP !" | echo "$serv service was DOWN. Restarting now on $(hostname)"

    else

    echo "$serv service is running"

    fi

done
