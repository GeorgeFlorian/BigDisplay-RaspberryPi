<!DOCTYPE html>
<html>

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
        $dhcpcd_array[1] = "hostname MetriciDisplayWiFi";
        $dhcpcd_array[2] = "clientid MetriciDisplayWiFi";
        $dhcpcd_array[3] = "";
        $dhcpcd_array[4] = "";
        $dhcpcd_array[5] = "";
        $dhcpcd_array[6] = "interface eth0";
        $dhcpcd_array[7] = "noipv4";
        $dhcpcd_array[8] = "noipv6";
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

        if(!empty($_POST['ipAddress'])) {
            $ipAddress = $_POST['ipAddress'];
            $dhcpcd_array[3] = "static ip_address=".$ipAddress;
        }
    
        if(!empty($_POST['gateway'])) {
            $gateway = $_POST['gateway'];
            $dhcpcd_array[4] = "static routers=".$gateway;
        }
    
        if(!empty($_POST['dns'])) {
            $dns = $_POST['dns'];
            $dhcpcd_array[5] = "static domain_name_servers=".$dns;
        }
    
        if(!empty($_POST['subnet'])) {
            $subnet = $_POST['subnet'];
            $dhcpcd_array[3]=$dhcpcd_array[3].'/'.mask2cidr($subnet);
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
            for($i = 0; $i<9; $i++) {
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
        $dhcpcd_array[3] = "interface eth0";
        $dhcpcd_array[4] = "hostname MetriciDisplayEth";
        $dhcpcd_array[5] = "clientid MetriciDisplayEth";
        $wpa_array[0] = "ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev";
        $wpa_array[1] = "update_config=1";
    
        if(!empty($_POST['ipAddress'])) {
            $ipAddress = $_POST['ipAddress'];
            $dhcpcd_array[6] = "static ip_address=".$ipAddress;
        }
    
        if(!empty($_POST['gateway'])) {
            $gateway = $_POST['gateway'];
            $dhcpcd_array[7] = "static routers=".$gateway;
        }
    
        if(!empty($_POST['dns'])) {
            $dns = $_POST['dns'];
            $dhcpcd_array[8] = "static domain_name_servers=".$dns;
        }
    
        if(!empty($_POST['subnet'])) {
            $subnet = $_POST['subnet'];
            // $dhcpcd_array[3] = $subnet;
            $dhcpcd_array[6]=$dhcpcd_array[6].'/'.mask2cidr($subnet);
        }

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
            for($i = 0; $i<9; $i++) {
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
    <title>Static IP</title>
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
        <form method="POST" name="simple_form" onsubmit="return ValidateIPaddress()">
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
                    <input type="password" class="input_text" placeholder="Type here the Password" id="networkPassword" name="networkPassword" value="" minlength="8" pattern=".{8,63}" title="Enter between 8 and 63 characters" required />
                    <label class="label_" for="networkPassword">Password</label>
                </div>
            </div>
            <div id = "staticIPfields">
                <div class ="input_row">
                    <input type="text" class="input_text" placeholder="Type here IP Address" id="ipAddress" name="ipAddress" value="" required
                    onchange="ValidateIPaddressOnChange(this, 'ipaddress')" />
                    <label class="label_" for="ipAddress">IP Address</label>
                </div>        
                <div class="input_row">
                    <input type="text" class="input_text" placeholder="Type here Gateway" id="gateway" name="gateway" value="" required
                    onchange="ValidateIPaddressOnChange(this, 'gateway')" />
                    <label class="label_" for="gateway">Gateway</label>
                </div>
                <div class ="input_row">
                    <input type="text" class="input_text" placeholder="Type here DNS" id="dns" name="dns" value="" required
                    onchange="ValidateIPaddressOnChange(this, 'dns')" />
                    <label class="label_" for="dns">DNS</label>
                </div>
                <div class ="input_row">
                    <input type="text" class="input_text" placeholder="Type here Subnet Mask" id="subnet" name="subnet" value="" required
                    onchange="ValidateIPaddressOnChange(this, 'subnet')" />
                    <label class="label_" for="subnet">Subnet Mask</label>
                </div>
            </div>
            <input class="button" type="submit" name="saveStatic" value="Save Values" />
        </form>
    </div>    
</body>

<script>
   function WiFi() {
        document.getElementById('dhcpFields').style.display = "block";
        document.getElementById('staticIPfields').style.display = "block";        
        document.getElementById('networkName').required=true;
        document.getElementById('networkPassword').required=true;
    }
    function Ethernet() {
        document.getElementById('dhcpFields').style.display = "none";
        document.getElementById('staticIPfields').style.display = "block";
        document.getElementById('networkName').required=false;
        document.getElementById('networkPassword').required=false;
    }

    function ValidateIPaddressOnChange(input, type) 
    {
        var ipformat = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
        var strtype = "";
        switch(type){
            case "ipaddress": strtype = "IP Address"; break;
            case "gateway": strtype = "Gateway"; break;
            case "subnet":  strtype = "Subnet Mask"; break;
            case "dns": strtype = "DNS"; break;
        }

        if(!input.value.match(ipformat)) {
            document.getElementById(input.name).className =
                document.getElementById(input.name).className.replace
                ( /(?:^|\s)correct(?!\S)/g , '' );
            document.getElementById(input.name).className += " wrong";
            input.focus();
            alert(strtype + " is invalid!");
        }
        else if(input.value != null){
            document.getElementById(input.name).className =
                document.getElementById(input.name).className.replace
                ( /(?:^|\s)wrong(?!\S)/g , '' );
            document.getElementById(input.name).className += " correct";
        }
    }

    function ValidateIPaddress()
    {
        var ipformat = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
        var ipaddr = document.forms["simple_form"]["ipAddress"];
        var gateway = document.forms["simple_form"]["gateway"];
        var subnet = document.forms["simple_form"]["subnet"];
        var dns = document.forms["simple_form"]["dns"];
        var counter = 0;

        if(ipaddr.value.match(ipformat)) {
            ipaddr.focus();
        } else {
            alert("You have entered an invalid IP Address!");
            ipaddr.focus();
            return (false);
        }
        if(gateway.value.match(ipformat)) {
            gateway.focus();
        } else {
            window.alert("You have entered an invalid GATEWAY Address!");
            gateway.focus();
            return (false);
        }            
        if(subnet.value.match(ipformat)) {
            subnet.focus();
        } else {
            window.alert("You have entered an invalid SUBNET Address!");
            subnet.focus();
            return (false);
        }            
        if(dns.value.match(ipformat)) {
            dns.focus();
        } else {
            window.alert("You have entered an invalid DNS Address!");
            dns.focus();
            return (false);
        }
    }
</script>
    
</html>
