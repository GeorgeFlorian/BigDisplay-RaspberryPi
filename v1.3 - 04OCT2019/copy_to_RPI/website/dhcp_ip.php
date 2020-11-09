<!DOCTYPE html>
<html>

<?php

$external_ip = json_decode(file_get_contents('https://api.ipify.org?format=json'))->ip;

$ip_data = @json_decode(file_get_contents( 
    "http://www.geoplugin.net/json.gp?ip=" . $external_ip)); 

$countryCode = $ip_data->geoplugin_countryCode;

$dhcpcd_array = [];
$wpa_array = [];
// retrieve the form data by using the element's name attributes value as key
// If you press on "Save and Reboot" button
if(isset($_POST['saveDHCP'])) {
    if($_POST['connectionType'] == "WiFi") {
        $dhcpcd_array[0] = "interface wlan0";
        $dhcpcd_array[1] = "hostname MetriciDisplayWiFi";
        $dhcpcd_array[2] = "clientid MetriciDisplayWiFi";
        $dhcpcd_array[3] = "interface eth0";
        $dhcpcd_array[4] = "noipv4";
        $dhcpcd_array[5] = "noipv6";
        $wpa_array[0] = "ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev";
        $wpa_array[1] = "update_config=1";
        $wpa_array[2] = 'country='.$countryCode;
        $wpa_array[3] = "network={";
        $wpa_array[4] = "";
        $wpa_array[5] = "";
        $wpa_array[6] = "}";

        if(!empty($_POST['networkName'])) {
            $networkName = $_POST['networkName'];
            $wpa_array[4] = "ssid=".'"'.$networkName.'"';
        }

        if(!empty($_POST['networkPassword'])) {
            $networkPassword = $_POST['networkPassword'];
            $wpa_array[5] = "psk=".'"'.$networkPassword.'"';
        }

        $wpa_lockFile = fopen("config/wpa_conf.lock", 'a');
        if(flock($wpa_lockFile, LOCK_EX)) {
            $wpaFile = fopen("config/wpa_conf.txt", "w");
            for($i = 0; $i <7; $i++) {
                fwrite($wpaFile,$wpa_array[$i].PHP_EOL);
            }
            fclose($wpaFile);
            flock($wpa_lockFile, LOCK_UN);
        }
        fclose($wpa_lockFile);

        $dhcpcd_lockFile = fopen("config/dhcpcd_conf.lock", 'a');
        if(flock($dhcpcd_lockFile, LOCK_EX)) {
            $dhcdpcdFile = fopen("config/dhcpcd_conf.txt", "w");
            for($i = 0; $i<6; $i++) {
                fwrite($dhcdpcdFile,$dhcpcd_array[$i].PHP_EOL);
            }
            fclose($dhcdpcdFile);
            flock($dhcpcd_lockFile, LOCK_UN);
        }
        fclose($dhcpcd_lockFile);

        echo "<script type='text/javascript'>alert('Submitted successfully!')</script>";
        echo '<meta http-equiv="refresh" content="0; url=/index.php">';
    } else if($_POST['connectionType'] == "Ethernet") {
        $wpa_array[0] = "ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev";
        $wpa_array[1] = "update_config=1";
        $dhcpcd_array[0] = "interface wlan0";
        $dhcpcd_array[1] = "noipv4";
        $dhcpcd_array[2] = "noipv6";
        $dhcpcd_array[3] = "interface eth0";
        $dhcpcd_array[4] = "hostname MetriciDisplayEth";
        $dhcpcd_array[5] = "clientid MetriciDisplayEth";

        $wpa_lockFile = fopen("config/wpa_conf.lock", 'a');
        if(flock($wpa_lockFile, LOCK_EX)) {
            $wpaFile = fopen("config/wpa_conf.txt", "w");            
            fwrite($wpaFile,$wpa_array[0].PHP_EOL);
            fwrite($wpaFile,$wpa_array[1].PHP_EOL);
            fclose($wpaFile);
            flock($wpa_lockFile, LOCK_UN);
        }
        fclose($wpa_lockFile);

        $dhcpcd_lockFile = fopen("config/dhcpcd_conf.lock", 'a');
        if(flock($dhcpcd_lockFile, LOCK_EX)) {
            $dhcdpcdFile = fopen("config/dhcpcd_conf.txt", "w");
            for($i = 0; $i<6; $i++) {
                fwrite($dhcdpcdFile,$dhcpcd_array[$i].PHP_EOL);
            }
            fclose($dhcdpcdFile);
            flock($dhcpcd_lockFile, LOCK_UN);
        }
        fclose($dhcpcd_lockFile);

        echo "<script type='text/javascript'>alert('Submitted successfully!')</script>";
        echo '<meta http-equiv="refresh" content="0; url=/index.php">';
    }
}

// echo $wpa_array[0], $wpa_array[1], $wpa_array[2], $wpa_array[3], $wpa_array[4], $wpa_array[5];
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

    <link rel = "stylesheet" type = "text/css" href = "newMaster.css">   
    <title>DHCP IP</title>
    <script type="text/javascript" src="jquery-1.12.4.min.js"></script>
</head>
    
<body>
    <div class="logo_container">
        <img url="images/logo.png">
        <button class="back_button" onclick="location.href = 'network_config.php';">Go Back</button>
        <span class = "version">Display<br>Version: 1.3</span>
    </div>
    <div class="center_box">
        <div class = "box_head">
            <div class = "title"><h1>Setup - Enter Values</h1></div>
        </div>
        <form method="POST">
            <div class ="input_radio">
                <span>Please select the type of connection: </span><br><br>
                <input id="wifi" type="radio" name="connectionType" value="WiFi" required onclick="WiFi()" /><label for="wifi">WiFi</label>
                <input id="eth" type="radio" name="connectionType" value="Ethernet" onclick="Ethernet()"/><label for="eth">Ethernet</label>
            </div>
            <div id = "dhcpFields">
                <div class ="input_row">
                    <input type="text" class="input_text" placeholder="Type here the Network Name (SSID)" id="networkName" name="networkName" value="" pattern=".{5,30}" title="Enter between 5 and 30 characters" required />
                    <label class="label_" for="networkName">Network Name (SSID)</label>
                </div>        
                <div class="input_row">
                    <input type="password" class="input_text" placeholder="Type here here Password" id="networkPassword" name="networkPassword" value="" minlength="8" pattern=".{8,63}" title="Enter between 8 and 63 characters" />
                    <label class="label_" for="networkPassword">Password</label>
                </div>                
            </div>
            <input class="button" type="submit" name="saveDHCP" value="Save Values" />
        </form>
    </div>    
</body>

<script>
    function goBack() {
        window.history.back();
    }
    function WiFi() {
        document.getElementById('dhcpFields').style.display = "block";
        document.getElementById('networkName').required=true;
    }
    function Ethernet() {
        document.getElementById('dhcpFields').style.display = "none";
        document.getElementById('networkName').required=false;
    }
</script>
    
</html>
