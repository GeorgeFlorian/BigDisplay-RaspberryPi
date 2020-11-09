<!DOCTYPE html>
<html>    

<?php
    include('inputs.php');
    include('log.php');
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

    <!-- <link rel = "stylesheet" type = "text/css" href = "master.css"> -->
    <link rel = "stylesheet" type = "text/css" href = "newMaster.css">
    <title>Home Page</title>
    <script type="text/javascript" src="jquery-1.12.4.min.js"></script>
    <!-- <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script> -->

</head>

<body>
<div class = "logo_container">
    <img url="images/logo.png">
    <span class = "version">Controller<br>Version: 1.3</span>
</div>

<div class = "top_container">
    <div class = "left_container">
        <div class = "box_head"> <div class = "title"> <h1>Current Display Settings</h1> </div> </div>            
        <div class = "text_box_STA">            
            <p>URL: <span><?php if(isset($arrayInputs[0])) echo $arrayInputs[0]; ?></span></p>
            <p>URL Refresh Interval: <span><?php if(isset($arrayInputs[1])) echo $arrayInputs[1]; ?></span>(s)</p>
            <p>Brightness: <span><?php if(isset($arrayInputs[2])) echo $arrayInputs[2]; ?></span>(&#37;)</p>
        </div> <!-- class = "text_box_STA" -->
    </div> <!-- class = "left_container" -->

    <div class = "right_container">
        <div class = "box_head"> <div class = "title"> <h1>Change Settings</h1> </div> </div>        
        <form method="post" action="">
            <div class ="input_row">
                <input class="input_text" id="getURL" type="url" inputmode="url" placeholder="Enter URL" name="getURL" value="" title = "URL" />
                <label class="label_" for="getURL">URL Link</label>
            </div>
            <div class ="input_row">
                <input  class="input_text" id="getURR" inputmode="numeric"
                    title="Enter a number of seconds between 1 and 99"
                    type="text"
                    pattern="([0-9]{1,2})"
                    oninvalid="this.setCustomValidity('Please enter a number between 1 and 99')"
                    onchange="try{setCustomValidity('')}catch(e){}"
                    oninput="setCustomValidity(' ')"
                    maxlength="2"
                    placeholder="Type here the URL Refresh Interval in seconds" name="getURR" value="">
                <label class="label_" for="getURR">URL Refresh Interval</label>
            </div>
            <div class ="input_row">
                <input  class="input_text" id="getBrightness" inputmode="numeric" type="text" maxlength="2" pattern="([0-9]{1,2})"
                    oninvalid="this.setCustomValidity('Please enter a number between 2 and 99')"
                    onchange="try{setCustomValidity('')}catch(e){}"
                    oninput="setCustomValidity(' ')"
                    placeholder="Type here brightness (2-99)" name="getBrightness" value="" title="Enter a Brightness value between 2 and 99">
                <label class="label_" for="getBrightness">Brightness</label>
            </div>
            <input class="button" type="submit" name="save_values" value="Save All Values" />
        </form>

        <form method="post" action= "/network_config.php">
            <input class="button" id="advanced_settings" name="ip_settings" type="submit" value="Go to Network Configuration" />
        </form>
        <form method="post" action= "/index.php">
            <input class="button" id="restart_device" name="restart_device" type="submit" value="Restart Display" />
        </form>
        
    </div> <!-- class = "right_container" -->
</div> <!-- class = "top_container" -->

<div class = "bottom_container">
    <div class = "box_head"> <div class = "title"> <h1>Logs</h1> </div> </div>
    <div class = "text_box"><?php echo $log_string;?></div>
</div>

<div class = "update_div">
        <form method="post" action="/update_page.php">
            <input class = "update_button" type="submit" name="goUpdate" value="Go to Update Page" />
        </form>
    </div>

<script>
$(document).ready(function() {
    $('#getURR').change(function() {
        var n = $('#getURR').val();
        if (n < 1)
            $('#getURR').val(1);
        if (n > 99)
            $('#getURR').val(99);
    });
    $('#getBrightness').change(function() {
        var m = $('#getBrightness').val();
        if (m < 2)
            $('#getBrightness').val(2);            
        if (m > 99)
            $('#getBrightness').val(99);
    });
});

setInterval(() => {
    $('.text_box').load("index.php" +  ' .text_box');    
}, 1000);

</script>

</body>
</html>
