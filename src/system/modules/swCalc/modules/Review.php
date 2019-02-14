<?php

namespace Slashworks\SwCalc\Modules;

use Contao\BackendTemplate;
use Contao\FrontendTemplate;
use Contao\System;
use Slashworks\SwCalc\Classes\Formatter;
use Slashworks\SwCalc\Models\Collection;
use Slashworks\SwCalc\Models\Pricing;
use Slashworks\SwCalc\Models\Product;
use Slashworks\SwCalc\Models\UnloadingPoint;
use Slashworks\SwCalc\Validators\AppValidator;
use Slashworks\SwCalc\Validators\CollectionValidator;

/**
 * Class ProductList
 *
 * @package Slashworks\SwCalc\Modules
 */
class Review extends \Module
{

    /**
     * The template name.
     *
     * @var string
     */
    protected $strTemplate = 'mod_review';

    /**
     * The collection object.
     *
     * @var Collection
     */
    protected $oCollection;

    /**
     * A product configuration.
     *
     * @var \Model\Collection
     */
    protected $oProductConfigurations;

    /**
     * All products.
     *
     * @var array
     */
    protected $aProducts = array();

    /**
     * All configurable fields.
     *
     * @var array
     */
    protected $aConfigurableFields;

    /**
     * The usp field.
     *
     * @var string
     */
    protected $sUspField;

    /**
     * A unique form id.
     *
     * @var
     */
    protected $sFormId;

    /**
     * A unique form id.
     *
     * @var
     */
    protected $changeUrl;

    /**
     * @var
     */
    protected $bHasErrors = false;


    /**
     * @return string
     */
    public function generate()
    {

        if (TL_MODE === 'BE') {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### Review ###';

            return $objTemplate->parse();
        } else {

            //validate session and call
            $App = new AppValidator();
            $App->validateAll();
        }

        $this->loadLanguageFile('tl_calc_collection');

        return parent::generate();
    }


    /**
     * Parse the template.
     */
    protected function compile()
    {
        try {
            $this->oCollection = Collection::getCurrent();

            //validate collection
            $cV = new CollectionValidator();
            if (!$cV->isValid()) {
                $this->renderValidation($cV);
            } else {
                $oPage = \PageModel::findById($this->jumpTo);
                $strUrl = \Controller::generateFrontendUrl($oPage->row());
                $this->Template->next = $strUrl;
            }

            $steps = array(
                'ueberschriftZusammenfassung'           => '<h2>Zusammenfassung</h2>',
                'tarif'                                 => $this->getTariff(),
                'lieferung'                             => $this->getShipping(),
                'bezahlung'                             => $this->getPayment(),
                'ueberschriftPreis'                     => '<h2>Preisberechnung</h2>',
                'preis'                                 => $this->getPrice(),
                'ueberschriftZusaetzlicheInformationen' => '<h2>Weitere Informationen:</h2>',
                'lieferadresse'                         => $this->getShippingAddress(),
                'abladestellen'                         => $this->getUnloadingPoints(),
                'kontakt'                               => $this->getContact(),
                'rechnungsadresse'                      => $this->getBillingAddress(),
                'besonderheiten'                        => $this->getAdditionalInformation(),
                'sonstigeHinweise'                      => $this->getFinalHints()
            );

            $sorting = explode(',', trim(\Slashworks\SwCalc\Models\Label::getLabel('review.order')));
            $properOrderedArray = array_merge(array_flip($sorting), $steps);

            // TODO change product to collection data !!!! important !!!!!
            $this->Template->blocks = $properOrderedArray;

            // Add data to global collection data array for google tag manager
            global $objPage;
            if ($objPage->collectionData) {
                $collectionData = $objPage->collectionData;
                $collectionData = array_merge($collectionData, array('step' => 60));
            } else {
                $collectionData = array('step' => 60);
            }
            $objPage->collectionData = $collectionData;

        } catch (\Exception $e) {
            System::log($e->getMessage() . '. In "' . $e->getFile() . '" on line ' . $e->getLine(), __METHOD__,
                TL_ERROR);
        }

    }


    /**
     * @return string
     */
    protected function getTariff()
    {
        $this->changeUrl = 'ajaxcontroller.html?action=getOilType';
        $tpl = new \FrontendTemplate('partial_review_block');
        $fields = array(
            'title'              => $GLOBALS['TL_LANG']['tl_calc_collection']['title'][$this->oCollection->labelGroup],
            '100&thinsp;l Preis' => Formatter::formatPriceWithCurrency($this->oCollection->totalPer100),
            'oilType'            => $GLOBALS['TL_LANG']['tl_calc_collection']['oilType'][$this->oCollection->oilType],
            'amount'             => Formatter::formatNumber($this->oCollection->amount),
            'unloadingPoints'    => $GLOBALS['TL_LANG']['tl_calc_collection']['unloadingPoint'][$this->oCollection->unloadingPoints],
        );

        $tpl->changeUrl = $this->changeUrl;
        $tpl->fields = $fields;
        $tpl->blockClass = 'tariff';

        return $tpl->parse();
    }


    /**
     * @return string
     */
    protected function getShipping()
    {
        $this->changeUrl = 'ajaxcontroller.html?action=getShipping';
        $tpl = new \FrontendTemplate('partial_review_block');
        $fields = array(
            'shipping' => $GLOBALS['TL_LANG']['tl_calc_collection']['shipping'][$this->oCollection->shipping],
        );

        $tpl->headline = 'Lieferung';
        $tpl->changeUrl = $this->changeUrl;
        $tpl->fields = $fields;
        $tpl->blockClass = 'shipping';

        return $tpl->parse();
    }


    /**
     * @return string
     */
    protected function getPayment()
    {
        $this->changeUrl = 'ajaxcontroller.html?action=getPayment';
        $tpl = new \FrontendTemplate('partial_review_block');
        $fields = array(
            'payment' => $GLOBALS['TL_LANG']['tl_calc_collection']['payment'][$this->oCollection->payment],
        );

        $tpl->headline = 'Bezahlung';
        $tpl->changeUrl = $this->changeUrl;
        $tpl->fields = $fields;
        $tpl->blockClass = 'payment';

        return $tpl->parse();
    }


    /**
     * @return string
     */
    protected function getPrice()
    {
        $tpl = new \FrontendTemplate('partial_review_block_price');
        $oPricing = Pricing::findByPostal();

        $fields = array(
            'totalPer100'       => Formatter::formatPriceWithCurrency($this->oCollection->totalPer100),
            'subTotalPerAmount' => Formatter::formatPriceWithCurrency($this->oCollection->subTotalPerAmount),
            'subTotal'          => Formatter::formatPriceWithCurrency($this->oCollection->subTotal),
            'adr'               => Formatter::formatPriceWithCurrency($this->oCollection->adr),
            'adrFlat'           => Formatter::formatPriceWithCurrency($oPricing->getPriceForAdr()),
            'vat'               => Formatter::formatPriceWithCurrency($this->oCollection->vat),
            'total'             => Formatter::formatPriceWithCurrency($this->oCollection->total),
        );

        // Include separate field for antifreeze.
        if ($this->oCollection->hasAntifreeze()) {
            $aAntiFreezeField = array('antifreeze' => Formatter::formatPriceWithCurrency($this->oCollection->antifreezeSurcharge));
            array_insert($fields, 3, $aAntiFreezeField);
            $tpl->antifreeze = $this->oCollection->getAntifreeze();
        }

        $tpl->headline = 'Preis';
        $tpl->amount = Formatter::formatNumber($this->oCollection->getAmount());
        $tpl->oilType = $GLOBALS['TL_LANG']['tl_calc_collection']['oilType'][$this->oCollection->getOilType()];
        $tpl->fields = $fields;
        $tpl->blockClass = 'price';
        $tpl->addCompleteButton = true;
        $tpl->jumpTo = $this->jumpTo;
        $tpl->detailsUrl = 'ajaxcontroller.html?action=getPriceDetails';

        return $tpl->parse();
    }


    /**
     * @return string
     */
    protected function getShippingAddress()
    {
        $this->changeUrl = 'ajaxcontroller.html?action=getShippingAddress';
        $tpl = new \FrontendTemplate('partial_review_block_tableless');
        $fields = array(
            'company' => $this->oCollection->shippingCompany,
            'name'    => $GLOBALS['TL_LANG']['tl_calc_collection']['salutation'][$this->oCollection->shippingSalutation] . ' ' . $this->oCollection->shippingFirstname . ' ' . $this->oCollection->shippingLastname,
            'street'  => $this->oCollection->shippingStreet,
            'city'    => $this->oCollection->shippingPostal . ' ' . $this->oCollection->shippingCity,
        );

        if ($this->oCollection->partialAmount) {
            $fields['partialAmount'] = '<strong>Teilmenge: ' . Formatter::formatNumber($this->oCollection->partialAmount) . '&thinsp;l ' . $GLOBALS['TL_LANG']['tl_calc_collection']['oilType'][$this->oCollection->getOilType()] . '</strong>';
        }

        if ($this->oCollection->antifreeze) {
            $fields['antifreeze'] = $this->oCollection->antifreeze . '&thinsp;&times;&thinsp;' . $GLOBALS['tl_calc_collection']['review']['antifreeze'];
        }

        $tpl->headline = 'Lieferadresse';
        $tpl->changeUrl = $this->changeUrl;
        $tpl->fields = $fields;
        $tpl->blockClass = 'shipping-address';

        return $tpl->parse();
    }

    protected function getUnloadingPoints()
    {
        //delete old unloadingpoints
        if ($this->oCollection->unloadingPoints == 1) {
            // Delete needless unloading point entry.
            UnloadingPoint::deleteAllAddressesByPidAndParentTable($this->oCollection->id, 'tl_calc_collection');

            return;
        }

        $oUnloadingPoints = UnloadingPoint::findByPid($this->oCollection->id);

        if (null === $oUnloadingPoints) {
            return '';
        }

        $tpl = new FrontendTemplate('partial_review_block_unloadingpoints');
        $this->changeUrl = 'ajaxcontroller.html?action=editUnloadingPoints';
        $fields = array();

        foreach ($oUnloadingPoints as $oUnloadingPoint) {
            $aNewField = array();
            $aNewField['headline'] = 'Haushalt ' . $oUnloadingPoint->unloadingpointorder;
            $aNewField['oilType'] = $GLOBALS['TL_LANG']['tl_calc_collection']['oilType'][Collection::getCurrent()->oilType];
            $aNewField['unloadingPoint'] = $oUnloadingPoint;

            $fields[] = $aNewField;
        }

        $tpl->headline = 'Zusätzliche Entladestellen';
        $tpl->changeUrl = $this->changeUrl;
        $tpl->fields = $fields;
        $tpl->blockClass = 'unloading-points';

        return $tpl->parse();
    }


    /**
     * @return string
     */
    protected function getContact()
    {
        $this->changeUrl = 'ajaxcontroller.html?action=getContact';
        $tpl = new \FrontendTemplate('partial_review_block');
        $fields = array(
            'email'  => $this->oCollection->email,
            'tel'    => $this->oCollection->phone,
            'mobile' => $this->oCollection->mobile,
        );

        $tpl->headline = 'Kontakt';
        $tpl->changeUrl = $this->changeUrl;
        $tpl->fields = $fields;
        $tpl->blockClass = 'contact';

        return $tpl->parse();
    }


    /**
     * @return string
     */
    protected function getBillingAddress()
    {
        $this->changeUrl = 'ajaxcontroller.html?action=getBillingAddress';
        $tpl = new \FrontendTemplate('partial_review_block_tableless');

        if (!$this->oCollection->shippingAddressEqualsBillingAddress) {
            $fields = array(
                'company' => $this->oCollection->billingCompany,
                'name'    => $GLOBALS['TL_LANG']['tl_calc_collection']['salutation'][$this->oCollection->billingSalutation] . ' ' . $this->oCollection->billingFirstname . ' ' . $this->oCollection->billingLastname,
                'street'  => $this->oCollection->billingStreet,
                'city'    => $this->oCollection->billingPostal . ' ' . $this->oCollection->billingCity,
            );
        } else {
            $fields = array(
                'billingAddress' => 'Die Rechnungsadresse entspricht der Lieferadresse',
            );
        }

        $tpl->headline = 'Rechnungsadresse';
        $tpl->changeUrl = $this->changeUrl;
        $tpl->fields = $fields;
        $tpl->blockClass = 'billing-address';

        return $tpl->parse();
    }


    /**
     * @return string
     */
    protected function getAdditionalInformation()
    {
        $this->changeUrl = 'ajaxcontroller.html?action=getAdditionalInformation';
        $tpl = new \FrontendTemplate('partial_review_block');
        $fields = array(
            'hose'       => $GLOBALS['TL_LANG']['tl_calc_collection']['hose'][$this->oCollection->hose],
            'tanker'     => $GLOBALS['TL_LANG']['tl_calc_collection']['tanker'][$this->oCollection->tanker],
            'antifreeze' => $GLOBALS['TL_LANG']['tl_calc_collection']['antifreezeOptions'][$this->oCollection->antifreeze],
            'notes'      => ($this->oCollection->notes) ? $this->oCollection->notes : 'Keine Angaben',
        );

        $oPricing = Pricing::findByPostal();

        // Remove tanker information if no price is defined.
        if (!$oPricing->specialTankTruckSmall) {
            unset($fields['tanker']);
        }

        // Remove antifreeze information if no price is defined.
        if (!$oPricing->antifreeze || $this->oCollection->unloadingPoints > 1) {
            unset($fields['antifreeze']);
        }

        $tpl->headline = 'Besonderheiten der Lieferung';
        $tpl->changeUrl = $this->changeUrl;
        $tpl->fields = $fields;
        $tpl->blockClass = 'special-features';

        return $tpl->parse();
    }


    /**
     * @return string
     */
    protected function getFinalHints()
    {
        $tpl = new \FrontendTemplate('partial_review_block');
        $fields = array();

        if ($this->oCollection->acceptAgb) {
            $fields['acceptAgb'] = 'Sie haben den Allgemeinen Geschäftsbedingungen zugestimmt.';
        }
        $tpl->fields = $fields;
        $tpl->blockClass = 'final-hints';
        $tpl->addCompleteButton = true;
        $tpl->jumpTo = $this->jumpTo;

        return $tpl->parse();
    }


    /**
     * @param $cV
     */
    protected function renderValidation($cV)
    {

        if (is_array($cV->getData())) {
            foreach ($cV->getData() as $group => $field) {
                switch ($group) {
                    case 'unloadingPoints':
                        $this->getTariff();
                        break;
                    case 'billing':
                        $this->getBillingAddress();
                        break;

                    case 'shipping':
                        $this->getShippingAddress();
                        break;

                    case 'zmzAccount':
                        $this->getPayment();
                        break;

                    case 'invoice':
                        $this->getPayment();
                        break;
                }
                break;
            }

            $this->bHasErrors = true;
            $this->Template->warnings = $this->changeUrl;
        }

    }

}
