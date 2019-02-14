<?php

namespace Slashworks\SwCalc\Models;

/**
 * Class GeoInformation
 * @package Slashworks\SwCalc\Models
 */
class GeoInformation
{

    /**
     * Get the postal code for an ip address.
     *
     * @param string $sIp
     * @return string
     */
    public function getPostalByIp()
    {
        $sIp = $_SERVER['REMOTE_ADDR'];
        $oDetails = json_decode(file_get_contents('http://ipinfo.io/' . $sIp . '/json'));

//        $oDetails = json_decode('{"postal": "57078"}');

        if ($oDetails->postal === null) {
            return '';
        }

        return $oDetails->postal;
    }

}