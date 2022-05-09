<?php

function getCountryCode() {
    $external_ip = json_decode(file_get_contents('https://api.ipify.org?format=json'))->ip;
    
    $ip_data = @json_decode(file_get_contents( 
        "http://www.geoplugin.net/json.gp?ip=" . $external_ip));
    if(empty($ip_data))
        return "RO";
    return ($ip_data->geoplugin_countryCode);
}

?>