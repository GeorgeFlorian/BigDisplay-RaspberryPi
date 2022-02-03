<?php

$external_ip = json_decode(file_get_contents('https://api.ipify.org?format=json'))->ip;

$ip_data = @json_decode(file_get_contents(
    "http://www.geoplugin.net/json.gp?ip=" . $external_ip
));

$countryCode = $ip_data->geoplugin_countryCode;

$dhcpcd_array = [];
$wpa_array = [];
// retrieve the form data by using the element's name attributes value as key
// If you press on "Save and Reboot" button
if (isset($_POST['saveDHCP'])) {
    if ($_POST['connectionType'] == "WiFi") {
        // input given settings
        $dhcpcd_array[0] = "interface wlan0";
        $dhcpcd_array[1] = "hostname MetriciDisplayWiFi";
        $dhcpcd_array[2] = "clientid";
        $dhcpcd_array[3] = "interface eth0";
        $dhcpcd_array[4] = "noipv4";
        $dhcpcd_array[5] = "noipv6";

        $wpa_array[0] = "ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev";
        $wpa_array[1] = "update_config=1";
        $wpa_array[2] = 'country=' . $countryCode;
        $wpa_array[3] = "network={";
        $wpa_array[4] = "";
        $wpa_array[5] = "";
        $wpa_array[6] = "}";

        if (!empty($_POST['networkName'])) {
            $networkName = $_POST['networkName'];
            $wpa_array[4] = "ssid=" . '"' . $networkName . '"';
        }

        if (!empty($_POST['networkPassword'])) {
            $networkPassword = $_POST['networkPassword'];
            $wpa_array[5] = "psk=" . '"' . $networkPassword . '"';
        }

        $wpa_lockFile = fopen(__DIR__."/wpa_conf.lock", 'a');
        if (flock($wpa_lockFile, LOCK_EX)) {
            $wpaFile = fopen(__DIR__."/wpa_conf.txt", "w");
            // for ($i = 0; $i < 7; $i++) {
            //     fwrite($wpaFile, $wpa_array[$i] . PHP_EOL);
            // }
            foreach($wpa_array as $i) {
                fwrite($wpaFile, $i.PHP_EOL);
            }
            fclose($wpaFile);
            flock($wpa_lockFile, LOCK_UN);
        }
        fclose($wpa_lockFile);

        $dhcpcd_lockFile = fopen(__DIR__."/dhcpcd_conf.lock", 'a');
        if (flock($dhcpcd_lockFile, LOCK_EX)) {
            $dhcdpcdFile = fopen(__DIR__."/dhcpcd_conf.txt", "w");
            // for ($i = 0; $i < 6; $i++) {
            //     fwrite($dhcdpcdFile, $dhcpcd_array[$i] . PHP_EOL);
            // }
            foreach ($dhcpcd_array as $var) {
                fwrite($dhcdpcdFile, $var.PHP_EOL);
            }
            fclose($dhcdpcdFile);
            flock($dhcpcd_lockFile, LOCK_UN);
        }
        fclose($dhcpcd_lockFile);

        echo "<script type='text/javascript'>alert('Submitted successfully!')</script>";
        // echo '<meta http-equiv="refresh" content="0; url=/index.php">';
        echo '<meta http-equiv="refresh" content="0; url=/index.php">';
    } else if ($_POST['connectionType'] == "Ethernet") {
        $wpa_array[0] = "ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev";
        $wpa_array[1] = "update_config=1";
        $dhcpcd_array[0] = "interface wlan0";
        $dhcpcd_array[1] = "noipv4";
        $dhcpcd_array[2] = "noipv6";
        $dhcpcd_array[3] = "interface eth0";
        $dhcpcd_array[4] = "hostname MetriciDisplayEth";
        $dhcpcd_array[5] = "clientid";
        // static IP fallback in case DHCP doesn't work
        // hostname: MetriciDisplay
        $dhcpcd_array[6] = "profile static_eth0";
        $dhcpcd_array[7] = "static ip_address=192.168.1.70/24";
        $dhcpcd_array[8] = "static routers=192.168.1.1";
        $dhcpcd_array[9] = "static domain_name_servers=8.8.8.8";
        $dhcpcd_array[10] = "interface eth0";
        $dhcpcd_array[11] = "fallback static_eth0";

        $wpa_lockFile = fopen(__DIR__."/wpa_conf.lock", 'a');
        if (flock($wpa_lockFile, LOCK_EX)) {
            $wpaFile = fopen(__DIR__."/wpa_conf.txt", "w");
            fwrite($wpaFile, $wpa_array[0] . PHP_EOL);
            fwrite($wpaFile, $wpa_array[1] . PHP_EOL);
            fclose($wpaFile);
            flock($wpa_lockFile, LOCK_UN);
        }
        fclose($wpa_lockFile);

        $dhcpcd_lockFile = fopen(__DIR__."/dhcpcd_conf.lock", 'a');
        if (flock($dhcpcd_lockFile, LOCK_EX)) {
            $dhcdpcdFile = fopen(__DIR__."/dhcpcd_conf.txt", "w");
            // for ($i = 0; $i < 6; $i++) {
            //     fwrite($dhcdpcdFile, $dhcpcd_array[$i] . PHP_EOL);
            // }            
            foreach ($dhcpcd_array as $var) {
                fwrite($dhcdpcdFile, $var.PHP_EOL);
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