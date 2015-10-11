<?php

/**
 * Description of cpanel_api_cpanelAPI
 *
 * @author Joseph W. Becher <jwbecher@gmail.com>
 * @package cpanel-api
 */
class cpanel_api_cpanelAPI
{

    var $cpanel_server = '';
    var $cpanel_username = '';
    var $cpanel_password = '';

    function cpanel_api_cpanelAPI($server, $user, $pass)
    {
        $this->cpanel_server = $server;
        $this->cpanel_username = $user;
        $this->cpanel_password = $pass;
    }

    function SendAPICall($module, $function, $params)
    {

        $additionalHeaders = '';
        $process = curl_init($this->cpanel_server . '/xml-api/cpanel?cpanel_xmlapi_module=' . $module . '&cpanel_xmlapi_func=' . $function . $params);
        curl_setopt($process, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', $additionalHeaders));
        curl_setopt($process, CURLOPT_USERPWD, CPANEL_UN . ":" . CPANEL_PW);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        $apiXML = curl_exec($process);

// Check if any error occured
        if (curl_errno($process)) {
            //TODO: Handle errors cleanly
            print_r(curl_getinfo($process));
            die;
        }
        return $apiXML;
    }


}

?>
