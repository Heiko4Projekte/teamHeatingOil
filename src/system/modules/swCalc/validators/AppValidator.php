<?php

namespace Slashworks\SwCalc\Validators;

use Contao\Config;
use Contao\Controller;
use Contao\Environment;
use Contao\Frontend;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\PageModel;
use Contao\PageRegular;
use Contao\System;
use Haste\Http\Response\Response;
use Slashworks\SwCalc\Models\Collection;
use Slashworks\SwCalc\Models\Pricing;
use Slashworks\SwCalc\Models\Product;

class AppValidator
{

    /**
     * @var bool
     */
    protected $bValid = false;

    /**
     * @var string
     */
    protected $sRedirectTo;

    /**
     * @var int
     */
    protected $iConfiguration = 1;

    protected $aConfiguration = [];


    /**
     * AppValidator constructor
     */
    public function __construct()
    {
        $this->bValid = false;
    }


    /**
     *
     */
    public function validateAll()
    {

        $this->validateActiveState();
        $this->validateCollection();

    }


    /**
     *
     */
    public function validateActiveState()
    {
        $db = \Database::getInstance();
        $this->aConfigration = $db->execute('SELECT * from tl_calc_configuration WHERE id = ' . $this->iConfiguration)->fetchAssoc();

        // Show
        if (!$this->aConfigration['active'] && (!BE_USER_LOGGED_IN && sha1(session_id() . (!Config::get('disableIpCheck') ? Environment::get('ip') : '') . 'BE_USER_AUTH') != Input::cookie('BE_USER_AUTH'))) {

            // Generate link to shop-deactivated page.
            $oPage = PageModel::findByPk($this->aConfigration['redirectTo']);
            $sClosedLink = $oPage->getAbsoluteUrl();

            // Set headers.
            header('HTTP/1.1 503 Service Unavailable');
            header('Content-type: text/html; charset=utf-8');

            $htaccessUsername = 'team';
            $htaccessPassword = 'werder17%';

            $context = stream_context_create(array
            (
                'http' => array
                (
                    'header' => "Authorization: Basic " . base64_encode("$htaccessUsername:$htaccessPassword")
                )
            ));

            // Render dummy template with content from shop-deactivated page.
            $oTemplate = new FrontendTemplate('mod_shop_deactivated');
            $oTemplate->content = file_get_contents($sClosedLink, false, $context);
            echo $oTemplate->parse();

            exit;
        }
    }


    /**
     *
     */
    public function validateCollection()
    {

        $oCollection = Collection::getCurrent();

        if ($oCollection->noSelection || PostalValidator::checkPostalError($oCollection->postal)) {
            \Controller::redirect(Environment::get('base'));
        }

    }


    /**
     *
     */
    public function validateGetParamsAndInit()
    {
        $getPostal = \Input::get('postal');

        if ($getPostal) {
            $oCollection = Collection::getCurrent();
            $oCollection->noSelection = false;

            $oCollection->postal = $getPostal;
            $oCollection->save();

            \Controller::redirect(Environment::get('base'));
            $objProduct = new Product();

            // If an invalid postal is given, set the fallback postal.
            // TODO: Show the page 'außerhalb des liefergebiets'.
//            if (!PostalValidator::checkPostalError($getPostal)) {
//                $oCollection->postal = $getPostal;
//            } else {
//                $oCollection->postal = $GLOBALS['TL_CALC']['fallbackPostal'];
//            }
//
//            $oCollection->save();
        }
    }


    /**
     * Check if the checkout steps should be visible or not.
     *
     * @return bool
     */
    public function validateCheckoutStepVisibility()
    {
        // No product has been selected
        if (Collection::getCurrent()->noSelection) {
            return false;
        }

        // Postal is not in pricing table.
        if (!Pricing::hasPostal(Collection::getCurrent()->postal)) {
            return false;
        }

        return true;
    }

}