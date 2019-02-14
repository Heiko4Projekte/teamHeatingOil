<?php

namespace Slashworks\SwCalc\Backend;

use Contao\Date;
use Slashworks\SwCalc\Models\Pricing;
use Slashworks\SwCalc\Modules\Order;

class Collection extends \Backend
{

    /**
     *
     */
    public function buttonCallback($row, $href, $label, $title, $icon, $attributes){

        $href .= "&collection=".$row['sessionID'];
        if($row['type'] === 'order')
        {
            return '<a title="Bestätigung erneut an '.$row['email'].' senden" href="'.$this->addToUrl($href).'" title="'
                .specialchars
                ($title).'"
'
                .$attributes.'>'
                .\Image::getHtml($icon, $label, 'data-state="' . ($row['invisible'] ? 0 : 1) . '"').'</a> ';
        }
        else{
            return "<span style='opacity:0.4;cursor: not-allowed'>".\Image::getHtml($icon, $label, 'data-state="' . ($row['invisible']
                        ? 0
                        : 1) . '</span>"');
        }

        return false;
    }

    /**
     *
     */
    public function salesMailButtonCallback($row, $href, $label, $title, $icon, $attributes){

        $oCollection = \Slashworks\SwCalc\Models\Collection::getCurrent();
        $oPricing = Pricing::findByPostal($oCollection->shippingPostal);
        $contactPerson = $oPricing->contactPerson;

        $href .= "&collection=".$row['sessionID'];
        if($row['type'] === 'order')
        {
            return '<a title="Bestätigung erneut an den Vertrieb('.$contactPerson.') senden" href="'.$this->addToUrl($href).'" title="'
                .specialchars
                ($title).'"
'
                .$attributes.'>'
                .\Image::getHtml($icon, $label, 'data-state="' . ($row['invisible'] ? 0 : 1) . '"').'</a> ';
        }
        else{
            return "<span style='opacity:0.4;cursor: not-allowed'>".\Image::getHtml($icon, $label, 'data-state="' . ($row['invisible']
                        ? 0
                        : 1) . '</span>"');
        }

        return false;
    }

    public function labelCallback($row, $label, $dc, $args){

        $general = $row['shippingFirstname'] .' '. $row['shippingLastname'] .'<br>';
        $general .= $row['shippingStreet'] .', ';
        $general .= $row['shippingPostal'] .' '. $row['shippingCity'];

        $date = Date::parse('d.M Y',$row['tstamp']) .' <br> '. $row['orderId'] .'<br>';

        $product = $row['amount'] .' <br> '. $GLOBALS['TL_LANG']['tl_calc_collection']['oilType'][$row['oilType']] .'<br>';
        $product .= $GLOBALS['TL_LANG']['tl_calc_collection']['payment'][$row['payment']] .'<br>'
        .$GLOBALS['TL_LANG']['tl_calc_collection']['shipping'][$row['shipping']];

        $contact = $row['email'] .' <br> '. $row['phone']  .' <br> '. $row['mobile'];

        $args[0] = $general;
        $args[1] = $date;
        $args[2] = $product;
        $args[3] = $contact;

        unset($args[4]);

        return $args;
    }

    /**
     *
     */
    public function sendMailConfirmation($dc){

        $sessionID = \Input::get('collection');
        $oCollection = \Slashworks\SwCalc\Models\Collection::findBySessionId($sessionID);

        Order::sendMailConfirmation($oCollection);
        \Message::addInfo('Eine Bestätigungs E-Mail an '.$oCollection->shippingFirstname.' '
            .$oCollection->shippingLastname .' ('
            .$oCollection->email .') wurde erneut versendet');
        \Controller::redirect(\Controller::getReferer());
    }

    /**
     *
     */
    public function sendSalesMailConfirmation($dc){

        $sessionID = \Input::get('collection');
        $oCollection = \Slashworks\SwCalc\Models\Collection::findBySessionId($sessionID);

        $oPricing = Pricing::findByPostal($oCollection->shippingPostal);
        $contactPerson = $oPricing->contactPerson;


        Order::sendMailConfirmation($oCollection,$contactPerson);
        \Message::addInfo('Eine Bestätigungs E-Mail an ('. $contactPerson .') wurde erneut versendet');
        \Controller::redirect(\Controller::getReferer());
    }
}
