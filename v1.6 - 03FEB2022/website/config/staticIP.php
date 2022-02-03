<?php

function mask2cidr($mask){
    $long = ip2long($mask);
    $base = ip2long('255.255.255.255');
    return 32-log(($long ^ $base)+1,2);
  
    /* xor-ing will give you the inverse mask,
        log base 2 of that +1 will return the number
        of bits that are off in the mask and subtracting
        from 32 gets you the cidr notation */         
}

$external_ip = json_decode(file_get_contents('https://api.ipify.org?format=json'))->ip;

$ip_data = @json_decode(file_get_contents( 
    "http://www.geoplugin.net/json.gp?ip=" . $external_ip)); 

$countryCode = $ip_data->geoplugin_countryCode;

// print_r(ip_info("Visitor", "Location"));

$dhcpcd_array = [];
$wpa_array = [];
// retrieve the form data by using the element's name attributes value as key
// If you press on "Save and Reboot" button
if(isset( $_POST['saveStatic'])) {
    if($_POST['connectionType'] == "WiFi") {
        $dhcpcd_array[0] = "interface wlan0";
        $dhcpcd_array[1] = "hostname MetriciDisplayWiFiS";
        $dhcpcd_array[2] = "";
        $dhcpcd_array[3] = "";
        $dhcpcd_array[4] = "";
        $dhcpcd_array[5] = ""; // empty line
        $dhcpcd_array[6] = "profile static_eth0";
        $dhcpcd_array[7] = "hostname MetriciDisplayFallback";
        $dhcpcd_array[8] = "static ip_address=192.168.1.70/24";
        $dhcpcd_array[9] = "static routers=192.168.1.1";
        $dhcpcd_array[10] = "static domain_name_servers=8.8.8.8";
        $dhcpcd_array[11] = ""; // empty line
        $dhcpcd_array[12] = "interface eth0";
        $dhcpcd_array[13] = "fallback static_eth0";

        $wpa_array[0] = "ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev";
        $wpa_array[1] = "update_config=1";
        $wpa_array[2] = ""; // empty line
        $wpa_array[3] = 'country='.$countryCode;
        $wpa_array[4] = ""; // empty line
        $wpa_array[5] = "network={";
        $wpa_array[6] = "";
        $wpa_array[7] = "";
        $wpa_array[8] = "}";

        if(!empty($_POST['networkName'])) {
            $networkName = trim($_POST['networkName']);
            $wpa_array[6] = "ssid=".'"'.$networkName.'"';
        }

        if(!empty($_POST['networkPassword'])) {
            $networkPassword = trim($_POST['networkPassword']);
            $wpa_array[7] = "psk=".'"'.$networkPassword.'"';
        }       

        if(!empty($_POST['ipAddress'])) {
            $ipAddress = trim($_POST['ipAddress']);
            $dhcpcd_array[2] = "static ip_address=".$ipAddress;
        }
    
        if(!empty($_POST['gateway'])) {
            $gateway = trim($_POST['gateway']);
            $dhcpcd_array[3] = "static routers=".$gateway;
        }
    
        if(!empty($_POST['dns'])) {
            $dns = trim($_POST['dns']);
            $dhcpcd_array[4] = "static domain_name_servers=".$dns;
        }
    
        if(!empty($_POST['subnet'])) {
            $subnet = trim($_POST['subnet']);
            $dhcpcd_array[2]=$dhcpcd_array[2].'/'.mask2cidr($subnet);
        }

        $wpa_lockFile = fopen(__DIR__."/wpa_conf.lock", 'a');
        if(flock($wpa_lockFile, LOCK_EX)) {
            $wpaFile = fopen(__DIR__."/wpa_conf.txt", "w");
            for($i = 0; $i < count($wpa_array); $i++) {
                fwrite($wpaFile,$wpa_array[$i].PHP_EOL);
            }
            fclose($wpaFile);
            flock($wpa_lockFile, LOCK_UN);
        }
        fclose($wpa_lockFile);

        $dhcpcd_lockFile = fopen(__DIR__."/dhcpcd_conf.lock", 'a');
        if(flock($dhcpcd_lockFile, LOCK_EX)) {
            $dhcdpcdFile = fopen(__DIR__."/dhcpcd_conf.txt", "w");
            for($i = 0; $i < count($dhcpcd_array); $i++) {
                fwrite($dhcdpcdFile,$dhcpcd_array[$i].PHP_EOL);
            }
            fclose($dhcdpcdFile);
            flock($dhcpcd_lockFile, LOCK_UN);
        }
        fclose($dhcpcd_lockFile);

        echo "<script type='text/javascript'>alert('Submitted successfully!')</script>";
        echo '<meta http-equiv="refresh" content="0; url=/index.php">';
    } else if($_POST['connectionType'] == "Ethernet") {
        $dhcpcd_array[0] = "interface wlan0";
        $dhcpcd_array[1] = "noipv4";
        $dhcpcd_array[2] = "noipv6";
        $dhcpcd_array[3] = ""; // empty line
        $dhcpcd_array[4] = "interface eth0";
        $dhcpcd_array[5] = "hostname MetriciDisplayEthS";
        
        $wpa_array[0] = "ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev";
        $wpa_array[1] = "update_config=1";
    
        if(!empty($_POST['ipAddress'])) {
            $ipAddress = trim($_POST['ipAddress']);
            $dhcpcd_array[6] = "static ip_address=".$ipAddress;
        }
    
        if(!empty($_POST['gateway'])) {
            $gateway = trim($_POST['gateway']);
            $dhcpcd_array[7] = "static routers=".$gateway;
        }
    
        if(!empty($_POST['dns'])) {
            $dns = trim($_POST['dns']);
            $dhcpcd_array[8] = "static domain_name_servers=".$dns;
        }
    
        if(!empty($_POST['subnet'])) {
            $subnet = trim($_POST['subnet']);
            $dhcpcd_array[6]=$dhcpcd_array[6].'/'.mask2cidr($subnet);
        }

        $wpa_lockFile = fopen(__DIR__."/wpa_conf.lock", 'a');
        if(flock($wpa_lockFile, LOCK_EX)) {
            $wpaFile = fopen(__DIR__."/wpa_conf.txt", "w");            
            fwrite($wpaFile,$wpa_array[0].PHP_EOL);
            fwrite($wpaFile,$wpa_array[1].PHP_EOL);
            fclose($wpaFile);
            flock($wpa_lockFile, LOCK_UN);
        }
        fclose($wpa_lockFile);

        $dhcpcd_lockFile = fopen(__DIR__."/dhcpcd_conf.lock", 'a');
        if(flock($dhcpcd_lockFile, LOCK_EX)) {
            $dhcdpcdFile = fopen(__DIR__."/dhcpcd_conf.txt", "w");
            for($i = 0; $i < count($dhcpcd_array); $i++) {
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

// echo $dhcpcd_array[0], $dhcpcd_array[1], $dhcpcd_array[2], $dhcpcd_array[3], $dhcpcd_array[4], $dhcpcd_array[5];
?>
