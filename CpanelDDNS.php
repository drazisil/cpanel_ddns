<?php

/**
 * cpanel-ddns
 *
 * @author Joseph W. Becher <jwbecher@gmail.com>
 * @package cpanel-ddns
 */
 
class CPanelDDNS() {

  public function FetchConfig() {
    return array{
      "domain" => "moo";
    };
  }

}
/**
 * This array holds error messages for user display
 */
$cpanel_ddns_error_messages = array();

/**
 *
 * Checks an IP against an ACL
 *
 * @param string $ip
 * @return boolean
 */
function cpanel_ddns_CheckClientACL($ip)
{
    if (is_array(ALLOWED_IPS)) {
        // ALLOWED_IPS is an array of IP addresses
    } else {
        // ALLOWED IPS is a single IP
        if ($ip != ALLOWED_IPS) {
            return FALSE;
        }
    }
    return TRUE;
}

/**
 * Uses the cpanel_api to query the XML API of cpanel for the DNS zone records
 *
 * @return xml $xmlZone
 */
function cpanel_ddns_FetchDNSZoneFile()
{
    require_once 'classes/cpanel_api_cpanelAPI.php';
    $cpanelAPI = new cpanel_api_cpanelAPI(CPANEL_DOMAIN, CPANEL_UN, CPANEL_PW);
    $tmpData = $cpanelAPI->SendAPICall('ZoneEdit', 'fetchzone', '&domain=' . ZONE_DOMAIN);
    $zoneXML = simplexml_load_string($tmpData)->data;
    return $zoneXML;
}

/**
 * Updates a DNS record with an IP address
 *
 * @param array $zoneRecordToUpdate
 * @param string $ipAddress
 * @return xml
 */
function cpanel_ddns_UpdateDNSZoneFile($zoneRecordToUpdate, $ipAddress)
{

    require_once 'classes/cpanel_api_cpanelAPI.php';
    $cpanelAPI = new cpanel_api_cpanelAPI(CPANEL_DOMAIN, CPANEL_UN, CPANEL_PW);
    $tmpData = $cpanelAPI->SendAPICall('ZoneEdit', 'edit_zone_record', '&domain=' . ZONE_DOMAIN
        . '&Line=' . $zoneRecordToUpdate['Line']
        . '&type=A'
        . '&address=' . $ipAddress
    );
    $zoneXML = simplexml_load_string($tmpData)->data;
    return $zoneXML;
}

/**
 * Search for a host in the DNS Zone file and return the details in an array
 *
 * @param xml $zoneXML
 * @param string $host
 * @return array
 */
function cpanel_ddns_SearchForHostInZoneFile($zoneXML, $host)
{
// Count the number of zone records
    $dns_records_count = count($zoneXML->children()); // PHP < 5.3 version

    /*
     * Loop though the zone records until we find the one that contains the record 
     * we wish to update. Also locate the SOA record if exists.
     */
    for ($i = 0; $i <= $dns_records_count; $i++) {
        // Search for the record we want to update
        if ($zoneXML->record[$i]->name == $host . '.' && $zoneXML->record[$i]->type == 'A') {
            $zone_number_to_update = $i;
        }
        // Look for the SOA record
        if ($zoneXML->record[$i]->type == 'SOA') {
            $zone_number_of_SOA_record = $i;
        }
    }

    /*
     * Check if we were able to locate an SOA record and return the serial if so
     */
    if (!is_null($zone_number_of_SOA_record)) {
        // We were able to locate an SOA record
        $SOA_record = cpanel_ddns_FetchRecordFromXMLByNumber($zoneXML, $zone_number_of_SOA_record);
//        echo ' % ' . $SOA_record['serial'] . ' % ';
    } else {
        // We were not able to locate an SOA record for this domain.
        cpanel_ddns_ErrorMessageAdd('SOA not found for this domain.');
        return FALSE;
    }

    /*
     * Were we able to locate the host record?
     */
    if (!is_null($zone_number_to_update)) {
        // We were able to locate an A record
        $zone_record = cpanel_ddns_FetchRecordFromXMLByNumber($zoneXML, $zone_number_to_update);
//        echo ' % ' . $zone_record['name'] . ' % ';
    } else {
        // We were not able to locate an A record for this host.
        cpanel_ddns_ErrorMessageAdd('A record was not found for this host.');
        return FALSE;
    }

    return $zone_record;
}

/**
 * Adds an error message to the cpanel_ddns_error_messages array
 *
 * @global array $cpanel_ddns_error_messages
 * @param string $message
 */
function cpanel_ddns_ErrorMessageAdd($message)
{
    global $cpanel_ddns_error_messages;

    $cpanel_ddns_error_messages[] = $message;
}

function cpanel_ddns_ErrorMessagesDisplay()
{
    global $cpanel_ddns_error_messages;

    foreach ($cpanel_ddns_error_messages as $errMsg) {
        echo $errMsg . "<br>\n";
    }
    die;
}

/**
 * Retrieves a single DNS record from the zone file XML
 *
 * @param xml $zoneXML
 * @param int $recordNumber
 * @return array $zone_record
 */
function cpanel_ddns_FetchRecordFromXMLByNumber($zoneXML, $recordNumber)
{
    $zone_record['type'] = (string)$zoneXML->record[$recordNumber]->type;
//    echo ' % ' . $zone_record['type'] . ' % ';
    /*
     * Check what type of record we are reading
     */
    switch ($zone_record['type']) {
        case 'SOA':

            $zone_record['serial'] = (string)$zoneXML->record[$recordNumber]->serial;
//            echo ' % ' . $zone_record['serial'] . ' % ';
            break;
        case 'A':
            /*
             * We need to obtain the following values from the current record 
             * in order to safely update it:
             * 
             * Line
             * ttl
             * address
             * 
             * The following additional values may be used later:
             * 
             * name
             * class
             * type
             * 
             */
            $zone_record['Line'] = (string)$zoneXML->record[$recordNumber]->Line;
//            echo ' % ' . $zone_record['Line'] . ' % ';

            $zone_record['ttl'] = (string)$zoneXML->record[$recordNumber]->ttl;
//            echo ' % ' . $zone_record['ttl'] . ' % ';

            $zone_record['address'] = (string)$zoneXML->record[$recordNumber]->address;
//            echo ' % ' . $zone_record['address'] . ' % ';

            $zone_record['name'] = (string)$zoneXML->record[$recordNumber]->name;
//            echo ' % ' . $zone_record['name'] . ' % ';

            $zone_record['class'] = (string)$zoneXML->record[$recordNumber]->class;
//            echo ' % ' . $zone_record['class'] . ' % ';

            break;
        default:
            echo 'moo?';
            die;
            break;
    }
    return $zone_record;
}

/**
 * An easy way to display infomation cleanly to the browser
 */
define('PHPBR', "<br>\n");
?>
