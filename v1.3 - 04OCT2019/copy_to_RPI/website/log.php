<?php

// $logFile = "/var/www/html/4panel/log_file.txt";
$logFilePath = "config/log_file.txt";
$logFile = fopen($logFilePath, "r");
$log_string = '';
if($logFile) {
    $log_string = fread($logFile, filesize($logFilePath));
}

?>