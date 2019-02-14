<?php

namespace Slashworks\SwCalc\Modules;

use Contao\BackendTemplate;
use Contao\Controller;
use Contao\Environment;
use Contao\Input;
use Contao\Session;
use Contao\System;
use Slashworks\SwCalc\Helper\EnhancedEcommerce;
use Slashworks\SwCalc\Models\Collection;
use Slashworks\SwCalc\Models\Pricing;
use Slashworks\SwCalc\Models\Product;
use Slashworks\SwCalc\Models\ProductConfiguration;
use Slashworks\SwCalc\Models\VarioValue;
use Slashworks\SwCalc\Validators\PostalValidator;

/**
 * Class ProductList
 * @package Slashworks\SwCalc\Modules
 */
class ProductList extends \Module
{

    /**
     * The template name.
     *
     * @var string
     */
    protected $strTemplate = 'mod_calc_productlist';

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
     * @return string
     */
    public function generate()
    {

        if (TL_MODE === 'BE') {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['productlist'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        $this->sFormId = 'productlist_form_' . $this->id;

        return parent::generate();
    }


    /**
     * Parse the template.
     */
    protected function compile()
    {


        /** @var \PageModel $objPage */
        global $objPage;

        try {

            //delete old sessiondata
            $addressStepController = AddressStepController::removeSessionData();

            // Get the current collection.
            $this->oCollection = Collection::getCurrent();

            // Get all configurable fields.
            $this->aConfigurableFields = Collection::getConfigurableFields();

            if ($this->oCollection->noSelection) {
                $this->Template->message = true;
                $this->Template->messageHeadline = \Slashworks\SwCalc\Models\Label::getLabel('home.headline');
                $this->Template->messageText = \Slashworks\SwCalc\Models\Label::getLabel('home');
                $this->Template->messageImage = \Slashworks\SwCalc\Models\Label::getLabel('home.image');
            }
            else {
                $this->addBodyClasses();
                $this->generateProducts();

                $this->Template->products = $this->aProducts;
                $this->Template->formAction = Environment::get('indexFreeRequest');
                $this->Template->formId = $this->sFormId;
                $this->Template->hasBestPrice = $this->bHasBestPrice;
            }

            $this->handleFormSubmit();

            // Add data to global collection data array for google tag manager
            global $objPage;
            if ($objPage->collectionData) {
                $collectionData = $objPage->collectionData;
                $collectionData = array_merge($collectionData, array('step' => 20));
            } else {
                $collectionData = array('step' => 20);
            }
            $objPage->collectionData = $collectionData;

        } catch (\Exception $e) {
            $this->Template->error = $e->getMessage();
            System::log($e->getMessage() . '. In "' . $e->getFile() . '" on line ' . $e->getLine(), __METHOD__, TL_ERROR);
        }

    }

    /**
     *
     */
    protected function handleFormSubmit()
    {
        if (Input::post('FORM_SUBMIT') === $this->sFormId) {
            $iChosenProductId = Input::post('product');

            $oCollection = Collection::getCurrent();

            foreach ($this->aConfigurableFields as $sField) {
                $oCollection->$sField = Input::post($sField);
            }

            $oCollection->shippingPostal = Input::post('shippingPostal');
            $oCollection->labelGroup = Input::post('labelGroup');
            $oCollection->showOriginalValues = false;

            $oCollection->save();

            $oNewProduct = new Product();

            $oCollection->saveNewProductInCollection($oNewProduct);

            // Prepare Google Analytics enhanced ecommerce data.
            $oChosenConfiguration = ProductConfiguration::findBy('id', $iChosenProductId);

            $oNewProduct->list = 'Produktliste';
            $oNewProduct->id = $iChosenProductId;
            $oNewProduct->title = $oChosenConfiguration->title;
            $oNewProduct->position = Input::post('position');

            $oEnhancedEcommerce = new EnhancedEcommerce($oNewProduct);
            $oEnhancedEcommerce->generateAddProduct();

            $this->redirectToNextStep();
        }
    }


    /**
     * @throws \Exception
     */
    protected function generateProducts()
    {

        // Get all product configurations.
        $this->oProductConfigurations = ProductConfiguration::findAll();

        if ($this->oProductConfigurations === null) {
            throw new \Exception('Keine Produktkonfigurationen vorhanden');
        }

        $iGACounter = 1;

        /** @var ProductConfiguration $oProductConfiguration */
        foreach ($this->oProductConfigurations as $oProductConfiguration) {

            // Overwrite predefined configuration with user configuration if they have been set.
            if (Collection::hasConfiguration()) {
                if ($oProductConfiguration->uspField === '') {
                    // Handle Best-Price configuration -> Vario Product

                    foreach ($this->aConfigurableFields as $sFieldName) {
                        // Apply user defined configuration to product configuration.
                        $oProductConfiguration->{$sFieldName} = $this->oCollection->{$sFieldName};

                        // Flag as "vario" product if a vario value has been chosen.
                        if (VarioValue::isVarioValue($sFieldName, $oProductConfiguration->{$sFieldName})) {
                            $oProductConfiguration->labelGroup = 'vario';
                            $this->Template->hasBestPrice= true;
                        }
                    }

                } else {
                    // Handle Express / Premium configuration

                    // Apply user defined configuration to product configuration.
                    foreach ($this->aConfigurableFields as $sFieldName) {
                        // Do not apply user defined configuration for usp field. Instead, keep original configuration.
                        if ($oProductConfiguration->uspField === $sFieldName) {
                            continue;
                        }

                        //IF PREMIUM PRODUCT AND SHIPPING IS NOT EXPRESS: SET SHIPPING TO PREMIUM
                        if ($oProductConfiguration->id == 2 and $sFieldName == 'shipping') {
                            if ($this->oCollection->{$sFieldName} != 'deliveryExpress') {
                                $oProductConfiguration->{$sFieldName} = 'deliveryPremium';
                                continue;
                            }
                        }

                        // If Premium and payment method is not invoice: Set payment method to invoice.
                        if ($oProductConfiguration->id == 2 AND $sFieldName == 'payment' && Collection::getCurrent()->showOriginalValues) {
                            if ($this->oCollection->{$sFieldName} != 'invoice') {
                                $oProductConfiguration->{$sFieldName} = 'invoice';
                                continue;
                            }
                        }

                        // If Express and oilType is not economyOil: Set oilType to economyOil.
                        if ($oProductConfiguration->id == 3 AND $sFieldName == 'oilType') {
                            if ($this->oCollection->{$sFieldName} != 'economyOil') {
                                $oProductConfiguration->{$sFieldName} = 'economyOil';
                                continue;
                            }
                        }

                        $oProductConfiguration->{$sFieldName} = $this->oCollection->{$sFieldName};
                    }
                }
            }



            // Create a product with configuration.
            $oProduct = new Product($oProductConfiguration);
            $oProduct->position = $iGACounter;
            $this->aProducts[$oProductConfiguration->id] = $oProduct;

            // Prepare Google Analytics enhanced ecommerce data.
            $oProduct->list = 'Produktliste';
            $oEnhancedEcommerce = new EnhancedEcommerce($oProduct);
            $oEnhancedEcommerce->generateAddImpression();

            $iGACounter++;
        }
    }

    protected function addBodyClasses()
    {
        global $objPage;
        $sBodyClass = $objPage->cssClass;

        $sAdditionalCssClass = 'show-product-list-results';

        if ($sBodyClass) {
            $sBodyClass .= ' ' . $sAdditionalCssClass;
        } else {
            $sBodyClass = $sAdditionalCssClass;
        }

        $objPage->cssClass = $sBodyClass;
    }


    /**
     *
     */
    protected function redirectToNextStep()
    {
        /** @var \PageModel $objTarget */
        if ($this->jumpTo && ($objTarget = $this->objModel->getRelated('jumpTo')) !== null) {
            Controller::redirect($objTarget->getFrontendUrl());
        }
    }

}