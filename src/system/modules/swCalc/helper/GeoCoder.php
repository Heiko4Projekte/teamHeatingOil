<?php

namespace Slashworks\SwCalc\Helper;

class GeoCoder
{

    public static function getGeoDataByPostal($sPostal)
    {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $sPostal . ',Germany&key=' . $GLOBALS['TL_CALC']['googleApiKey'];
        $responseRaw = file_get_contents($url);
        $response = json_decode($responseRaw, true);

        if ($response['status'] === 'OK') {
            return $response;
        }

        return null;
    }

    public static function getBoundsByGeoData($aGeoData)
    {
        $aBounds = array();

        if ($aGeoData['status'] === 'OK') {
            $aBounds = array
            (
                'north' => $aGeoData['results'][0]['geometry']['bounds']['northeast']['lat'],
                'east' => $aGeoData['results'][0]['geometry']['bounds']['northeast']['lng'],
                'south' => $aGeoData['results'][0]['geometry']['bounds']['southwest']['lat'],
                'west' => $aGeoData['results'][0]['geometry']['bounds']['southwest']['lng']
            );
        }

        return $aBounds;
    }

    public static function getCityByGeoData($aGeoData)
    {
        $sCity = '';

        if ($aGeoData['status'] === 'OK') {
            $sCity = $aGeoData['results'][0]['address_components'][1]['long_name'];
        }

        return $sCity;
    }

}