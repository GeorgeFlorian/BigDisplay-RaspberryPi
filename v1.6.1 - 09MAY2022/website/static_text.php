<?php
/*
 Change URL to point to http://localhost/static_text.php
 Writes static display text to /config/static_text.txt from interface
 Gets text from url by calling /code/get_text_from_url.sh
 Writes text to /code/url_response.txt
 Displays text from /code/url_response.txt
*/

$static_text_array = new SplFixedArray(3);

function getFileContent () {
    $text_file_path = __DIR__."/config/static_text.txt";
    $text_lock_path = __DIR__."/config/static_text.lock";
    $text_lock_file = fopen($text_lock_path, 'a');

    $arrayInputs = new SplFixedArray(3);

    if(flock($text_lock_file, LOCK_SH)) {
        $text_file = fopen($text_file_path, "r");
        $arrayInputs = explode(PHP_EOL, fread($text_file, filesize($text_file_path)));
        $static_text_array = explode("#", fread($text_file, filesize($text_file_path)));
        fclose($text_file);
        flock($text_lock_file, LOCK_UN);    
    } else {
        echo "Could not open file to read".PHP_EOL;
    }
    fclose($text_lock_file);
    echo $arrayInputs[0]; // #Rlocalhost/#Gconfig/#Bstatic_text.php
}

getFileContent();
?>