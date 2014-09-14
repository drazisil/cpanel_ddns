<?php

/**
 * updateDNS
 *
 * This file will serve as the server endpoint that the client connects to.
 *
 * Will take both GET and POST requests for plain-text and authenticated updates
 *
 * @author Joseph W. Becher <jwbecher@gmail.com>
 * @package cpanel-ddns
 */
/**
 * See config.example.php for details on this file
 */
require_once 'config.php';
require_once 'functions.php';


switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // The request was sent via GET. Plain-text authentication
        $the_request = & $_GET;
        break;
    case 'POST':
        // The update was sent via POST. It likely contains a username and password
        $the_request = & $_POST;
        break;
    default:
        // Unknown request type
        die;
}

/*
 * Was a host provided as part of the update request?
 */
if (!$the_request['host']) {
    echo 'No host was provided.';
    die;
} else {
    $host_to_update = $the_request['host'];
}

// Is an IP included?
if (!$the_request['ip']) {
    // Attempt to auto-guess the IP address
    $ip_to_update = $_SERVER['REMOTE_ADDR'];
} else {
    /*
     * Sanitize the IP and check if it's valid.
     */
    $ip_to_update = filter_var($the_request['ip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    if ($ip_to_update == FALSE) {
        echo 'Invalid IP was provided.';
        die;
    }
}

/*
 * Is the requesting client allowed to perform updates?
 */
$is_client_allowed = cpanel_ddns_CheckClientACL($_SERVER['REMOTE_ADDR']);

if (!$is_client_allowed) {
    // Client is not allowed to perform updates.
    echo 'Access denied.';
    die;
}

/*
 * Does the requested domain exist?
 */
$dns_zones_XML = cpanel_ddns_FetchDNSZoneFile();
$zone_record_to_update = cpanel_ddns_SearchForHostInZoneFile($dns_zones_XML, $host_to_update);
if ($zone_record_to_update == FALSE) {
    cpanel_ddns_ErrorMessagesDisplay();
}

echo 'The requested host record for ' . $host_to_update . ' was found in the zone file in line ' . $zone_record_to_update['Line'] . '.' . PHPBR;
$updated_record = $dns_zones_XML = cpanel_ddns_UpdateDNSZoneFile($zone_record_to_update, $ip_to_update);

//echo $updated_record;

echo "The request from {$_SERVER['REMOTE_ADDR']} was recieved.";
?>
