<!DOCTYPE html>
<html lang="en">

<?php
include(__DIR__ . "/config/dhcpIP.php");
include(__DIR__ . "/config/version.php");
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Chrome, Firefox OS and Opera -->
    <link rel="shortcut icon" href="/favicon.ico?v=476mA4zprB" />
    <!-- Tab Color iOS Safari -->
    <meta name="apple-mobile-web-app-title" content="Metrici.ro" />
    <meta name="application-name" content="Metrici.ro" />
    <!-- Tab Color Android Chrome -->
    <meta name="theme-color" content="#e11422" />
    <!-- CSS -->
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/icomoon-style.css">
    <!-- JavaScript -->
    <script defer src="./javascript/jquery-3.6.0.min.js"></script>
    <script defer src="./javascript/main.js"></script>
    <title>DHCP IP</title>
</head>

<body>
    <a href="https://www.metrici.ro/" target="_blank" class="mini-logo"></a>
    <nav>
        <div class="nav_container">
            <a href="https://www.metrici.ro/" target="_blank" class="logo"></a>
            <ul>
                <li><a href="/index.php">Home</a></li>
                <li><a href="/static_display.php">Static Text</a></li>
                <li class="dropdown">
                    <a class="touch active">
                        Network Settings
                        <span class="icon-circle-down"></span>
                        <span class="icon-circle-up"></span>
                    </a>
                    <div class="dropdown-content">
                        <a href="/dhcp.php" class="active">DHCP IP</a>
                        <a href="/static_ip.php">Static IP</a>
                    </div>
                </li>
                <!-- <li><a href="/files">Import/Export Files</a></li> -->
                <li><a href="/update.php">Update</a></li>
            </ul>
        </div>
    </nav>

    <section>
        <div class="top_container">
            <div class="mid_container">
                <div class="title">
                    <h1>Setup - Change <span class="setting">DHCP IP</span> Settings</h1>
                </div>
                <div class="inner_container">
                    <form method="post" action="">
                        <div class="input_radio">
                            <span>Please select the type of connection: </span><br><br>
                            <input id="wifi" type="radio" name="connectionType" value="WiFi" required
                                onclick="DhcpWiFi()" /><label for="wifi">WiFi</label>
                            <input id="eth" type="radio" name="connectionType" value="Ethernet"
                                onclick="DhcpEthernet()" /><label for="eth">Ethernet</label>
                        </div>
                        <div id="dhcpFields">
                            <div class="input_row">
                                <input type="text" class="input_text" placeholder="Type here the Network Name (SSID)"
                                    id="networkName" name="networkName" value="" pattern=".{5,30}"
                                    title="Enter between 5 and 30 characters" required />
                                <label class="label_" for="networkName">Network Name (SSID)</label>
                            </div>
                            <div class="input_row">
                                <input type="password" class="input_text" placeholder="Type here here Password"
                                    id="networkPassword" name="networkPassword" value="" minlength="8" pattern=".{8,63}"
                                    title="Enter between 8 and 63 characters" />
                                <label class="label_" for="networkPassword">Password</label>
                            </div>
                        </div>
                        <input class="button" type="submit" name="saveDHCP" value="Save Values" />
                    </form>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <form method="post" action="">
            <input class="button" type="submit" name="restart_device" value="Restart Device" />
        </form>
        <span class="version">
            <a href="https://www.metrici.ro/products/metrici-display" target="_blank"
                title="Go to Display documentation">Display Controller</a> 
                - Version: <?php echo $version; ?>
        </span>
        <span class="copyright">
            <a href="https://www.metrici.ro/" target="_blank"
                title="Go to Metrici Website">Metrici</a> © 2021 -
            All Rights Reserved.
        </span>
    </footer>
</body>

</html>