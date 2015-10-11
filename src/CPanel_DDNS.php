<?php
namespace drazisil\CPanel_DDNS;

/**
 * Class CPanel_DDNS
 * @package Drazisil\CPanel_DDNS
 */
class CPanel_DDNS
{
    /**
     * @var string
     */
    var $cpanel_server;
    /**
     * @var string
     */
    var $cpanel_username;
    /**
     * @var string
     */
    var $cpanel_password;

    /**
     * Contains un-parsed xml
     * @var string
     */
    var $cpanel_results;

    /**
     * Uses the cpanel_api to query the XML API of cpanel for the DNS zone records
     *
     * @return \SimpleXMLElement[] $xmlZone
     */
    function fetchCPanelZoneFile()
    {
        {
            $tmpData = $this->getFromCPanel('ZoneEdit', 'fetchzone', '&domain=' . ZONE_DOMAIN);
            $zoneXML = simplexml_load_string($tmpData)->data;
            return $zoneXML;
        }
    }

    /**
     * @param $module
     * @param $function
     * @param $params
     * @return bool
     */
    function getFromCPanel($module, $function, $params)
    {
        $additionalHeaders = '';
        $process = curl_init($this->cpanel_server . '/xml-api/cpanel?cpanel_xmlapi_module=' . $module . '&cpanel_xmlapi_func=' . $function . $params);
        curl_setopt($process, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', $additionalHeaders));
        curl_setopt($process, CURLOPT_USERPWD, CPANEL_UN . ":" . CPANEL_PW);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        $apiXML = curl_exec($process);

        // Check if any error occurred
        if (curl_errno($process)) {
            //TODO: Handle errors cleanly
            print_r(curl_getinfo($process));
            return false;
        }
        $this->cpanel_results = $apiXML;
        return true;
    }
} 
