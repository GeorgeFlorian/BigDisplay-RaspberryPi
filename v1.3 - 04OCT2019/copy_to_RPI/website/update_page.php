<!DOCTYPE html>
<html>

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
                        if ($response == "Metrici_128x64_Display v1.3") {
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

<head>
    <meta charset = "utf-8">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
    <!-- iOS App Icon-->
    <link rel="apple-touch-icon" sizes="180x180" href="images/apple-touch-icon.png?v=476mA4zprB">
    <!-- Chrome, Firefox OS and Opera -->
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png?v=476mA4zprB">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png?v=476mA4zprB">
    <link rel="manifest" href="images/site.webmanifest?v=476mA4zprB">
    <link rel="mask-icon" href="images/safari-pinned-tab.svg?v=476mA4zprB" color="#e11422">
    <link rel="shortcut icon" href="images/favicon.ico?v=476mA4zprB">
    <!-- Tab Color iOS Safari -->
    <meta name="apple-mobile-web-app-title" content="Metrici.ro">
    <meta name="application-name" content="Metrici.ro">
    <!-- Tile Color Microsoft Windows Shortcut -->
    <meta name="msapplication-TileColor" content="#b91d47">
    <!-- Tab Color Android Chrome -->
    <meta name="theme-color" content="#e11422">

    <script type="text/javascript" src="jquery-1.12.4.min.js"></script>
    <link rel = "stylesheet" type = "text/css" href = "newMaster.css">   
    <title>Firmware Update</title>
</head>

<body>
    <div class="logo_container">
        <img url="images/logo.png">
        <button class="back_button" onclick="location.href = 'index.php';">Go Back</button>
        <span class = "version">Controller<br>Version: 1.3</span>
    </div>
    <div class="center_box">
        <div class = "box_head">
            <div class = "title"><h1>Setup - Update Firmware</h1></div>
        </div>
        <div class="upload_firmware_container">
            Please upload the provided <span style="color:#e11422">Metrici_MP_Display.zip</span> archive and then <span style="color:#e11422">Metrici_MP_Display</span> file.<br><br>
            <div style="text-align:left; margin-left:10px;">
                Notes:<br>
                (1): First upload <span style="color:#e11422">Metrici_MP_Display.zip</span> and then <span style="color:#e11422">Metrici_MP_Display</span>.<br>
                (2): Not uploading the files in the above mentioned order will stop the display from working correctly.<br>
                (3): Your settings are safe. Updating the device won't change the configuration.<br>
                (4): You will be asked for a code <span style="color:#1489e1">only</span> when uploading <span style="color:#e11422">Metrici_MP_Display.zip</span>.<br>
                (5): The code will be provided by Metrici alongside <span style="color:#e11422">Metrici_MP_Display.zip</span>.<br>
                (6): After pressing the <span style="color:#1489e1">Update</span> button you will be prompted with a success or failure message.
            </div>
            <form method="POST" enctype="multipart/form-data">
                <label class="file_input_label" id = "special_label" for="files">Select files</label>
                <input type="file" id="files" name="uploaded_file">
                <input class="upload_button" type="submit" name="update_button" value="Update">
                <div id=input_code>
                    <div class="input_row" >
                        <input type="text" class="input_text" id="special_input" placeholder="Type here the provided code" id="file_code" name="file_code" value="" minlength="32" title="Enter the provided code" />
                        <label class="label_" for="file_code">Enter provided code</label>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>  
<script>

document.getElementById('files').onchange = function () {
    var file_name = this.value;
    file_name = file_name.replace(/.*[\/\\]/, '');
    if (file_name == "Metrici_MP_Display.zip") {
        document.getElementById('input_code').style.display = "block";
        document.getElementById('input_code').style.textAlign = "left";
        document.getElementById('input_code').style.height = "80px";
        document.getElementById('special_input').required=true;
        document.getElementById('special_label').style.fontSize = "14px";
    } else {
        document.getElementById('input_code').style.display = "none";
        document.getElementById('special_input').required=false;
    }
};

$('#files').on("change",function() {
    console.log("change fire");
    var i = $(this).prev('label').clone();
    var file = $('#files')[0].files[0].name;
    console.log(file);
    $(this).prev('label').text(file);        
});
</script>
</html>