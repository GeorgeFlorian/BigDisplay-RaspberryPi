<?php

$arrayInputs = new SplFixedArray(3);
$arrayInputs[0] = "URL not entered";
$arrayInputs[1] = "3";
$arrayInputs[2] = "50";

$configFilePath = __DIR__."/configuration.txt";
$lockFilePath = __DIR__."/configuration.lock";
$lockFile = fopen($lockFilePath, 'a');

if(flock($lockFile, LOCK_SH)) {
    $currentSettingsFile = fopen($configFilePath, "r");
    $arrayInputs = explode(PHP_EOL, fread($currentSettingsFile, filesize($configFilePath)));
    fclose($currentSettingsFile);
    flock($lockFile, LOCK_UN);
} else {
    echo "Could not open file to read".PHP_EOL;
}
fclose($lockFile);

// retrieve the form data by using the element's name attributes value as key
// If you press on "Save Values" button
if(isset( $_POST['save_values'])) {
    if(!empty($_POST['getURL'])) {
        $arrayInputs[0] = $_POST['getURL'];
    }
    if(!empty($_POST['getURR'])) {
        $arrayInputs[1] = $_POST['getURR'];
    }
    if(!empty($_POST['getBrightness'])) {
        $arrayInputs[2] = $_POST['getBrightness'];
    }

    $lockFile = fopen($lockFilePath, 'a');
    if(flock($lockFile, LOCK_EX)) {
        $currentSettingsFile = fopen($configFilePath, "w");
        foreach ($arrayInputs as $key => $value) {
            if($value != '')
                fwrite($currentSettingsFile,$value.PHP_EOL);
        }
        fclose($currentSettingsFile);
        flock($lockFile, LOCK_UN);
    } else {
        echo "Could not open file to write".PHP_EOL;
    }
    fclose($lockFile);
}

if(isset($_POST['restart_device'])) {
    shell_exec("sudo /var/www/html/scripts/restart_device.sh");
    header("Location: /");
    die();
}

?>