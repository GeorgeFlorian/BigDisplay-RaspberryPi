<!DOCTYPE html>
<html lang="en">

<?php
include(__DIR__ . "/config/update.php");
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Chrome, Firefox OS and Opera -->
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=476mA4zprB" />
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v=476mA4zprB" />
    <link rel="shortcut icon" href="/favicon.ico?v=476mA4zprB" />
    <!-- Tab Color iOS Safari -->
    <meta name="apple-mobile-web-app-title" content="Metrici.ro" />
    <meta name="application-name" content="Metrici.ro" />
    <!-- Tab Color Android Chrome -->
    <meta name="theme-color" content="#e11422" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="index.css">
    <script defer src="general.js"></script>
    <title>Firmware Update</title>
</head>

<body>
    <a href="https://www.metrici.ro/" target="_blank" class="mini-logo"></a>
    <nav>
        <div class="nav_container">
            <a href="https://www.metrici.ro/" target="_blank" class="logo"></a>
            <ul>
                <li><a href="/index.php">Home</a></li>
                <li class="dropdown">
                    <a class="touch">Network Settings <i class="fas fa-angle-down"></i><i
                            class="fas fa-angle-up"></i></a>
                    <div class="dropdown-content">
                        <a href="/dhcp.php">DHCP IP</a>
                        <a href="/static.php">Static IP</a>
                    </div>
                </li>
                <!-- <li><a href="/files">Import/Export Files</a></li> -->
                <li><a href="/update.php" class="active">Update</a></li>
            </ul>
        </div>
    </nav>

    <section>
        <div class="top_container update_container">
            <div class="mid_container">
                <div class="title">
                    <h1>Setup - <span class="setting">Update</span> Firmware</h1>
                </div>
                <div class="update_text_container">
                    Please upload the provided <span style="color:#ea5a64">Metrici_MP_Display.zip</span> archive and
                    then <span style="color:#ea5a64">Metrici_MP_Display</span> file.<br><br>
                    Notes:<br>
                    (1): First upload <span style="color:#ea5a64">Metrici_MP_Display.zip</span> and then <span
                        style="color:#ea5a64">Metrici_MP_Display</span>.<br>
                    (2): Not uploading the files in the above mentioned order will stop the display from working
                    correctly.<br>
                    (3): Your settings are safe. Updating the device won't change the configuration.<br>
                    (4): You will be asked for a code <span style="color:#96ede5">only</span> when uploading <span
                        style="color:#ea5a64">Metrici_MP_Display.zip</span>.<br>
                    (5): The code will be provided by Metrici alongside <span
                        style="color:#ea5a64">Metrici_MP_Display.zip</span>.<br>
                    (6): After pressing the <span style="color:#96ede5">Update</span> button you will be prompted with a
                    success or failure message.
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div>
                        <label class="button file_input_label" id="special_label" for="files">Select files</label>
                        <input type="file" id="files" name="uploaded_file">
                        <input class="button upload_button" type="submit" name="update_button" value="Update">
                    </div>
                    <div id=input_code>
                        <div class="input_row">
                            <input type="text" class="input_text" id="special_input"
                                placeholder="Type here the provided code" id="file_code" name="file_code" value=""
                                minlength="32" title="Enter the provided code" />
                            <label class="label_" for="file_code">Enter provided code</label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <footer>
        <span class="version"><a href="https://www.metrici.ro/products/metrici-display" target="_blank"
                title="Go to Display documentation">Display Controller</a> - Version: 1.4</span>
        <span class="copyright"><a href="https://www.metrici.ro/" target="_blank"
                title="Go to Metrici Website">Metrici</a> © 2020 -
            All Rights Reserved.</span>
    </footer>

    <script>
        document.getElementById('files').onchange = function () {
            var file_name = this.value;
            file_name = file_name.replace(/.*[\/\\]/, '');
            if (file_name == "Metrici_MP_Display.zip") {
                document.getElementById('input_code').style.display = "block";
                document.getElementById('input_code').style.textAlign = "left";
                document.getElementById('input_code').style.height = "80px";
                document.getElementById('special_input').required = true;
                document.getElementById('special_label').style.fontSize = "14px";
            } else {
                document.getElementById('input_code').style.display = "none";
                document.getElementById('special_input').required = false;
            }
        };

        $('#files').on("change", function () {
            console.log("change fire");
            var i = $(this).prev('label').clone();
            var file = $('#files')[0].files[0].name;
            console.log(file);
            $(this).prev('label').text(file);
        });
    </script>
</body>

</html>