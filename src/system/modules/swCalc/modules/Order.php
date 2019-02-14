<?php

namespace Slashworks\SwCalc\Modules;

use BusinessDays\Calculator;
use Contao\BackendTemplate;
use Contao\System;
use Slashworks\SwCalc\Models\Collection;
use Slashworks\SwCalc\Models\Pricing;
use Slashworks\SwCalc\Models\Product;
use Slashworks\SwCalc\Models\UnloadingPoint;
use Slashworks\SwCalc\Validators\AppValidator;
use Slashworks\SwCalc\Models\Label;
use Slashworks\SwCalc\Models\Configuration;

/**
 * Class ProductList
 *
 * @package Slashworks\SwCalc\Modules
 */
class Order extends \Module
{

    /**
     * The template name.
     *
     * @var string
     */
    protected $strTemplate = 'mod_order';


    /**
     * @return string
     */
    public function generate()
    {
        if (TL_MODE === 'BE') {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### Order ###';

            return $objTemplate->parse();
        }

//        if(!$_SERVER["REMOTE_ADDR"]=='217.236.38.150'){
        $appValidator = new AppValidator();
        $appValidator->validateCollection();

//        }

        return parent::generate();
    }


    /**
     * Parse the template.
     */
    protected function compile()
    {

        try {
            $oPricingRow = \Slashworks\SwCalc\Models\Pricing::findByPostal();

            $oCollection = Collection::getCurrent();
            $oCollection->type = 'order';

            if (!$oCollection->orderId) {
                $oCollection->orderId = Collection::getOrderId();
            }

            $oCollection->save();

            $team = \Slashworks\SwCalc\Models\Label::getTeamMember($oPricingRow);
            $this->Template->message = str_replace(['##orderId##', '##team##'], [$oCollection->orderId, $team],
                Label::getLabel('checkout.step4.order'));

            $this->Template->additionalText = Label::getLabel('checkout.step4.order.additionalText');

            $sCtaLink = Label::getLabel('checkout.step4.order.cta.link');
            $sCtaText = Label::getLabel('checkout.step4.order.cta.text');

            if (empty($sCtaText)) {
                $sCtaText = $sCtaLink;
            }

            $this->Template->ctaLink = $sCtaLink;
            $this->Template->ctaText = $sCtaText;


            // adwords tracking
            $oConfiguration = Configuration::getActive();

            if ($oConfiguration->ga_conversion_tracking_order) {
                global $objPage;
                $gtDataLayer = $objPage->collectionData;

                $aKeyValues = array();
                $aKeyValues[] = "'step': '70'";
                $aKeyValues[] = "'value': '" . $gtDataLayer['amount'] . "'";
                if (is_array($gtDataLayer)) {
                    foreach ($gtDataLayer as $key => $value) {
                        $aKeyValues[] = "'" . $key . "': '" . $value . "'";
                    }
                    $aKeyValues[] = "'transaction_id': '" . $gtDataLayer['orderId'] . "'";
                    $sKeyValues = implode(',', $aKeyValues);
                    $GLOBALS['TL_HEAD'][] = "<script>dataLayer = [{" . $sKeyValues . "}]</script>";
                }
            }

            self::sendMailConfirmation($oCollection);

//          if(!$_SERVER["REMOTE_ADDR"]=='87.168.45.63') {
            $this->cleanupSession();
//            }


        } catch (\Exception $e) {
            System::log($e->getMessage() . '. In "' . $e->getFile() . '" on line ' . $e->getLine(), __METHOD__,
                TL_ERROR);
        }

    }


    /**
     * @param Collection $oCollection
     * @param bool       $recipient
     * @param bool       $bIsTeamMail
     *
     * @throws \Exception
     */
    public static function sendMailConfirmation($oCollection, $recipient = false, $bIsTeamMail = false)
    {

        self::loadLanguageFile('tl_calc_collection');
        $oConfiguration = Configuration::getActive();

        $oMail = new \Email();
        //$oMail->from = "online-bestellung@tnikolaus.de";
        //$oMail->fromName = "Nikolaus energie GmbH";
        //$oMail->subject = "Neue Bestellung: " . $oCollection->orderId;

        //$oTemplate = new \FrontendTemplate('confirmation');

        $oMail->from = $oConfiguration->emailFrom;
        $oMail->fromName = $oConfiguration->emailFromName;
        $oMail->subject = self::parseSubject($oConfiguration->emailSubject, $oCollection);

        $oTemplate = new \FrontendTemplate($oConfiguration->emailTemplate);

        $oData = $oCollection;

        if ((int)$oCollection->unloadingPoints > 1) {
            $oData->unloadingPointsData = UnloadingPoint::findByPid($oData->id);
        }

        $oPricing = Pricing::findByPostal($oCollection->postal);
        // Only show antifreeze in additional information block if only the main address has ordered antifreeze.
        if ($oPricing->antifreeze && $oCollection->unloadingPoints == 1) {
            $oTemplate->showAntifreezeInAdditionalInformation = true;
        }

        if ($oCollection->partialAmount) {
            $oTemplate->mainPartialAmount = $oCollection->partialAmount;
        }

        $oData->adrFlat = $oPricing->getPriceForAdr();

        $oTemplate->antifreezeAmount = $oCollection->antifreeze + UnloadingPoint::getAntifreezeAmountsByPid($oCollection->id);
        $oTemplate->deliveryDate = $oCollection->shippingDate;

        $oTemplate->data = $oData;
        $oTemplate->isTeamMail = $bIsTeamMail;

        $oMail->html = $oTemplate->parse();

        $oPricing = Pricing::findByPostal($oCollection->shippingPostal);

        if ($recipient) {
            $oMail->sendTo($recipient);
        } else {
            self::sendMailConfirmation($oCollection, $oPricing->contactPerson, true);
//            self::sendMailConfirmation($oCollection,'marei.kausen@team.de', true);
            self::sendMailConfirmation($oCollection, $oConfiguration->emailAdditionalRecipients, true);
            $oMail->sendTo($oCollection->email);
        }

    }

    protected function cleanupSession()
    {
        session_regenerate_id(true);
    }

    /**
     * Replace custom placeholders in the email subject string.
     */
    protected function parseSubject($sSubject, $oCollection)
    {
        // Wegen des hohen Aufkommens, sollen Expressbestellungen extra ausgezeichnet werden
        if ($oCollection->shipping == 'deliveryExpress') {
            $sSubject = "Express! " . $sSubject;
        }

        if (strpos($sSubject, '##orderId##') !== false) {
            $sSubject = str_replace('##orderId##', $oCollection->orderId, $sSubject);;
        }

        return $sSubject;
    }


}