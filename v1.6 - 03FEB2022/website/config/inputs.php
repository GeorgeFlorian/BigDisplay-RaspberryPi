<?php

function writeConfigToPage() {
    $arrayInputs = new SplFixedArray(3);

    $settings_file_path = __DIR__."/configuration.txt";
    $settings_lock_path = __DIR__."/configuration.lock";
    $settings_lock_file = fopen($settings_lock_path, 'a');

    // Read settings from file to show them on the page
    if(flock($settings_lock_file, LOCK_SH)) {
        $settings_file = fopen($settings_file_path, "r");
        $arrayInputs = explode(PHP_EOL, fread($settings_file, filesize($settings_file_path)));
        fclose($settings_file);
        flock($settings_lock_file, LOCK_UN);    
    } else {
        echo "Could not open file to read".PHP_EOL;
    }
    fclose($settings_lock_file);

    echo "<p>URL: <span>".$arrayInputs[0]."</span></p>";
    echo "<p>URL Refresh Interval (s): <span>".$arrayInputs[1]."</span></p>";
    echo "<p>Brightness (&#37;): <span>".$arrayInputs[2]."</span></p>";
    
}

function handleConfigForm() {
    $arrayInputs = new SplFixedArray(3);
    $arrayInputs[0] = "URL not entered";
    $arrayInputs[1] = "3";
    $arrayInputs[2] = "50";

    $settings_file_path = __DIR__."/configuration.txt";
    $settings_lock_path = __DIR__."/configuration.lock";
    $settings_lock_file = fopen($settings_lock_path, 'a');

    // retrieve the form data by using the element's name attributes value as key
    // If you press on "Save Values" button
    if(isset( $_POST['save_settings'])) {
        if(!empty($_POST['getURL'])) {
            $arrayInputs[0] = trim($_POST['getURL']);
        }
        if(!empty($_POST['getURR'])) {
            $arrayInputs[1] = trim($_POST['getURR']);
        }
        if(!empty($_POST['getBrightness'])) {
            $arrayInputs[2] = trim($_POST['getBrightness']);
        }

        // Write POSTed settings to file
        $settings_lock_file = fopen($settings_lock_path, 'a');
        if(flock($settings_lock_file, LOCK_EX)) {
            $settings_file = fopen($settings_file_path, "w");
            foreach ($arrayInputs as $key => $value) {
                if(!empty($value))
                    fwrite($settings_file,$value.PHP_EOL);
            }
            fclose($settings_file);
            flock($settings_lock_file, LOCK_UN);
        } else {
            echo "Could not open file to write".PHP_EOL;
        }
        fclose($settings_lock_file);
    }    
}

function getHTMLColor($color) {
    if($color == "R") {
        return "style=\"color:red;\"";
    }
    else if($color == "O") {        
        return "style=\"color:orange;\"";
    }
    else if($color == "Y") {        
        return "style=\"color:yellow;\"";
    }
    else if($color == "G") {        
        return "style=\"color:green;\"";
    }
    else if($color == "B") {        
        return "style=\"color:blue;\"";
    }
    else if($color == "I") {        
        return "style=\"color:indigo;\"";
    }
    else if($color == "V") {        
        return "style=\"color:violet;\"";
    }
    else if($color == "W") {        
        return "style=\"color:white;\"";
    }
    return "Invalid";

}

function displayLinesOnPage() {
    $static_text_array = new SplFixedArray(3);

    $text_file_path = __DIR__."/static_text.txt";
    $text_lock_path = __DIR__."/static_text.lock";
    $text_lock_file = fopen($text_lock_path, 'a');

    // Read settings from file to show them on the page
    if(flock($text_lock_file, LOCK_SH)) {
        $text_file = fopen($text_file_path, "r");
        $static_text_array = explode("#", fread($text_file, filesize($text_file_path)));
        fclose($text_file);
        flock($text_lock_file, LOCK_UN);    
    } else {
        echo "Could not open file to read".PHP_EOL;
    }
    fclose($text_lock_file);
    
    foreach ($static_text_array as $key => $value) {
        if(!empty($value))
            echo "<span ".getHTMLColor(substr($value, 0, 1)).">".substr($value, 1)."</span>";
    }
}

function getColor($color) {
    if($color == "Red") {
        return "R";
    }
    else if($color == "Orange") {
        return "O";
    }
    else if($color == "Yellow") {
        return "Y";
    }
    else if($color == "Green") {
        return "G";
    }
    else if($color == "Blue") {
        return "B";
    }
    else if($color == "Indigo") {
        return "I";
    }
    else if($color == "Violet") {
        return "V";
    }
    else if($color == "White") {
        return "W";
    }
    return "Invalid";
}

function staticText() {
    $arrayText = "";

    $text_file_path = __DIR__."/static_text.txt";
    $text_lock_path = __DIR__."/static_text.lock";
    $text_lock_file = fopen($text_lock_path, 'a');
    $write = false;


    if(isset( $_POST['save_text'])) {
        if(!empty($_POST['getLine1'])) {
            if(!empty($_POST['line_1_color'])) {
                $arrayText = $arrayText."#".getColor($_POST['line_1_color']).trim($_POST['getLine1']);
                // echo $arrayText;
                $write = true;
            }
            else {
                echo "<script type='text/javascript'>alert('Please select color for first line.')</script>";
                $write = false;
            }
        }
        if(!empty($_POST['getLine2'])) {
            if(!empty($_POST['line_2_color'])) {            
                $arrayText = $arrayText."#".getColor($_POST['line_2_color']).trim($_POST['getLine2']);
                // echo $arrayText;
                $write = true;
            }
            else {
                echo "<script type='text/javascript'>alert('Please select color for second line.')</script>";
                $write = false;
            }
        }
        if(!empty($_POST['getLine3'])) {
            if(!empty($_POST['line_3_color'])) {
                $arrayText = $arrayText."#".getColor($_POST['line_3_color']).trim($_POST['getLine3']);
                // echo $arrayText;
                $write = true;
            }
            else {
                echo "<script type='text/javascript'>alert('Please select color for thrid line.')</script>";
                $write = false;
            }
        }

        // Write POSTed settings to file
        if($write) {
            $text_lock_file = fopen($text_lock_path, 'a');
            if(flock($text_lock_file, LOCK_EX)) {
                $text_file = fopen($text_file_path, "w");
                if(!empty($arrayText))
                    fwrite($text_file, $arrayText.PHP_EOL);
                fclose($text_file);
                flock($text_lock_file, LOCK_UN);
            } else {
                echo "Could not open file to write".PHP_EOL;
            }
            fclose($text_lock_file);
        }
    }
}

handleConfigForm();
staticText();


if(isset($_POST['restart_device'])) {
    shell_exec("sudo /var/www/html/scripts/restart_device.sh");
    header("Location: /");
    die();
}

?>