<?php
function returnAPIfromEnv($api_name){
    $env = parse_ini_file('../../.env');
    return $env[$api_name];
}

?>