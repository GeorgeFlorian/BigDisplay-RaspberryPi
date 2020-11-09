<!DOCTYPE html>
<html>

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
    <title>Network Setup</title>
    <script type="text/javascript" src="jquery-1.12.4.min.js"></script>
    <!-- <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script> -->

</head>

<body>
    <div class="logo_container">
        <button class="back_button" onclick="location.href = 'index.php';">Go Back</button>
        <img url="images/logo.png">
        <span class = "version">Display<br>Version: 1.3</span>
    </div>

    <div class="center_box">
        <div class = "box_head">
            <div class = "title"><h1>Setup - Network</h1></div>
        </div>
        <button class="button" onclick="goDHCP()">DHCP IP</button>
        <button class="button" onclick="goStatic()">Static IP</button>
    </div>    
</body>  

<script>
    function goBack() {
        window.history.back();
    }
    function goDHCP() {
        window.location.replace("/dhcp_ip.php");
    }
    function goStatic() {
        window.location.replace("/static_ip.php");
    }
</script>
</html>