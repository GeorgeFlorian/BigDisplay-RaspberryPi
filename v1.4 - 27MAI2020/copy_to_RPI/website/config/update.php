<?php

//check if folder is writable
// var_dump(is_writable('/var/www/html/uploads/'));
// var_dump(is_writable('/var/www/html/tmp_file_upload/'));

if (isset($_POST['update_button']) && $_POST['update_button'] == 'Update') {
    // print_r($_FILES);
    if (isset($_FILES['uploaded_file']) &&
        $_FILES['uploaded_file']['error'] === UPLOAD_ERR_OK &&
        ($_FILES['uploaded_file']['type'] == "application/octet-stream" || $_FILES['uploaded_file']['type'] == "application/zip"))
    {
        // get details of the uploaded file
        $fileTmpPath = $_FILES['uploaded_file']['tmp_name'];
        $fileName = $_FILES['uploaded_file']['name'];
        $fileSize = $_FILES['uploaded_file']['size'];
        $uploadFileDir = "/var/www/html/uploads/";
        if(move_uploaded_file($fileTmpPath, $uploadFileDir.$fileName)) {
            if($fileName == "Metrici_MP_Display") {
                $make_exe = "chmod +x $uploadFileDir"."$fileName";
                exec($make_exe, $full, $status);
                if($status != 0) {
                    unlink($uploadFileDir.$fileName);
                    $message = "ERROR_CODE:1 - Something went wrong, try again."; // Could not execute chmod +x
                    echo "<script type='text/javascript'>alert('$message');</script>";
                } else {
                    $run_exe = "$uploadFileDir"."$fileName -v";
                    $response = exec($run_exe, $full, $status);
                    if($status != 0) {
                        unlink($uploadFileDir.$fileName);
                        $message = "ERROR_CODE:2 - Something went wrong, try again."; // Could not run program
                        echo "<script type='text/javascript'>alert('$message');</script>";
                    } else {
                        if ($response == "Metrici_128x64_Display v1.4") {
                            $foo = exec("/var/www/html/scripts/firmware.sh");
                            if($foo == "0") {
                                shell_exec("sudo /var/www/html/scripts/restart_device.sh");
                                $message = "File successfully uploaded. Firmware was updated !";
                                echo "<script type='text/javascript'>alert('$message');</script>";
                            } else {
                                $message = "Could not update firmware. Try again.";
                                echo "<script type='text/javascript'>alert('$message');</script>";
                            }
                        } else {
                            unlink($uploadFileDir.$fileName);
                            $message = "Invalid file. Please upload a valid Metrici_MP_Display file !";
                            echo "<script type='text/javascript'>alert('$message');</script>";
                        }
                    }
                }

                
            } else if($fileName == "Metrici_MP_Display.zip") {
                $md5_code = md5(file_get_contents($uploadFileDir.$fileName, FALSE, NULL, 0, 950));
                if(isset($_POST['file_code']) && !empty($_POST['file_code'])) {
                    $entered_code = $_POST['file_code'];
                    $entered_code = strtolower($entered_code);
                    if($entered_code == $md5_code) {
                        $foo = exec("/var/www/html/scripts/interface.sh");
                        if($foo == "0") {
                            $message = "File successfully uploaded. Webserver was updated !";
                            echo "<script type='text/javascript'>alert('$message');</script>";
                        } else {
                            $message = "Invalid file. Please upload a valid Metrici_MP_Display.zip !";
                            echo "<script type='text/javascript'>alert('$message');</script>";
                        }
                    } else {
                        unlink($uploadFileDir.$fileName);
                        $message = "Invalid code. Please enter a valid code !";
                        echo "<script type='text/javascript'>alert('$message');</script>";
                    }
                } else {
                    $message = "No code entered ! Please enter the provided code !";
                    echo "<script type='text/javascript'>alert('$message');</script>";
                }
            } else { // if file name doesn't correspond to anything
                unlink($uploadFileDir.$fileName);
                $message = "Invalid file. Please upload only the provided files !";
                echo "<script type='text/javascript'>alert('$message');</script>";
            }
        } else { // if move_uploaded_file failed - Check permissions
            $message = "ERROR_CODE:3 - Something went wrong, try again.";
            echo "<script type='text/javascript'>alert('$message');</script>";
        }
    } else {
        $message = "Could not upload file. Try again.";
        echo "<script type='text/javascript'>alert('$message');</script>";
    }
}

?>