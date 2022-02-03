<?php

// $logFile = "/var/www/html/4panel/log_file.txt";
$logFilePath = __DIR__."/log_file.txt";
$logFile = fopen($logFilePath, "r");
$log_string = '';
if($logFile) {
    $log_string = fread($logFile, filesize($logFilePath));
}

?>