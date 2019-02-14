<?php

namespace Slashworks\SwCalc\Modules;

use Contao\Controller;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\Module;
use Contao\Widget;
use Haste\Form\Form;
use Slashworks\SwCalc\Classes\Formatter;
use Slashworks\SwCalc\Classes\FormGroups;
use Slashworks\SwCalc\Helper\JavascriptHelper;
use Slashworks\SwCalc\Helper\UnloadingPointsFormCreator;
use Slashworks\SwCalc\Models\Pricing;
use Slashworks\SwCalc\Models\Product;
use Slashworks\SwCalc\Models\UnloadingPoint;
use Slashworks\SwCalc\Validators\AmountValidator;
use Slashworks\SwCalc\Validators\PaymentValidator;
use Slashworks\SwCalc\Validators\PhoneOrMobile;
use Slashworks\SwCalc\Validators\PostalValidator;
use Slashworks\SwCalc\Validators\ShippingAddressValidator;
use Slashworks\SwCalc\Validators\BillingAddressValidator;
use Slashworks\SwCalc\Validators\AdditionalInformationValidator;
use Slashworks\SwCalc\Validators\OilTypeValidator;
use Slashworks\SwCalc\Validators\ShippingValidator;
use Slashworks\SwCalc\Models\Collection;
use Slashworks\SwCalc\Models\Label;


class AjaxController extends Module
{

    /**
     * Ajax action
     *
     * @var string
     */
    protected $strAction;

    /**
     * Ajax action
     *
     * @var string
     */
    protected $debug = 0;
    /**
     * Ajax action
     *
     * @var string
     */
    protected $data = array();

    /**
     * @var Form
     */
    protected $oForm;

    /**
     * @var
     */
    protected $sFormTemplate;

    /**
     * @var string
     */
    protected $strTemplate = 'mod_ajaxcontroller';

    /**
     * @var string
     */
    protected $sCustomFormTemplate = 'form';

    /**
     * @var string
     */
    protected $aGlobalErrors = [];

    /**
     * @return string
     */
    public function generate()
    {

        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### AJAXCONTROLLER ###';

            return $objTemplate->parse();
        }

        return parent::generate();
    }

    /**
     * @throws \ErrorException
     */
    public function compile()
    {
        if (TL_MODE == 'FE') {
            $this->strAction = \Input::get('action');

            $this->loadLanguageFile('tl_calc_collection');

            if ($this->strAction) {
                $this->switchAction();
            } else {
                throw new \ErrorException('Keine Ajax Action gesetzt');
            }
        }

    }

    /**
     * Generate templates and forms depending on selected action.
     */
    public function switchAction()
    {
        switch ($this->strAction) {

            //getter
            case 'getOilType':
                return $this->getOilTypeTemplate();
                break;
            case 'setUnloadingPoints':
                return $this->setUnloadingPoints();
                break;
            case 'editUnloadingPoints':
                return $this->editUnloadingPoints();
                break;
            case 'getShipping':
                return $this->getShippingTemplate();
                break;
            case 'getPayment':
                return $this->getPaymentTemplate();
                break;
            case 'setBirthday':
                return $this->setBirthday();
                break;
            case 'setzmz':
                return $this->setzmz();
                break;
            case 'getShippingAddress':
                return $this->getShippingAddressTemplate();
                break;
            case 'getBillingAddress':
                return $this->getBillingAddresseTemplate();
                break;
            case 'getAdditionalInformation':
                return $this->getAdditionalInformationTemplate();
                break;
            case 'getContact':
                return $this->getContactTemplate();
                break;
            case 'getPriceDetails':
                return $this->getPriceDetailsTemplate();
                break;
        }
    }

    // ###########################
    //  getter
    // ###########################

    /**
     * Generate form for amount, unloading points and oil type selection.
     */
    protected function getOilTypeTemplate()
    {
        $this->generateForm();
        $this->sFormTemplate = 'oil-type-template';

        $oCollection = Collection::getCurrent();

        // Only add amount field if there is 1 unloading point
        if ($oCollection->unloadingPoints == 1) {
            $this->oForm->addFormField('amount', array
            (
                'label'     => $GLOBALS['TL_LANG']['tl_calc_collection']['amount'],
                'inputType' => 'text',
                'eval'      => array
                (
                    'class'        => 'widget-amount',
                    'autocomplete' => 'new-password',
                ),
            ));
            // Add custom validator for amount field.
            $this->oForm->addValidator('amount', new AmountValidator());
        }

        //add unloadingPoints
        $this->oForm->addFormField('unloadingPoints', array
        (
            'label'     => $GLOBALS['TL_LANG']['tl_calc_collection']['unloadingPoints'],
            'inputType' => 'select',
            'reference' => &$GLOBALS['TL_LANG']['tl_calc_collection']['unloadingPoint'],
            'options'   => $GLOBALS['TL_CALC']['unloadingPointValues'],
            'eval'      => array
            (
                'class' => 'widget-unloadingPoints',
            ),
        ));

        $this->oForm->addFormField('oilType', array
        (
            'inputType' => 'radio',
            'options' => $GLOBALS['TL_CALC']['oilTypeValues'],
            'eval' => array
            (
                'template' => 'widget_radioExplanation',
                'addPriceToLabel' => true
            )
        ));

        // $this->oForm->addSubmitFormField('submit', 'weiter');
        $this->oForm->addFormField('submit', array
        (
            'label' => 'Weiter {{svg::arrow-right}}',
            'inputType' => 'submit',
            'eval' => array
            (
                'class' => 'button-mega'
            )
        ));

        if ($this->oForm->validate()) {
            Collection::updateCollection();

            $v = new OilTypeValidator();

            if (!$v->isValid()) {
                $this->oForm = $v->getData();
            } else {
                $this->data['modal'] = false;
            }

        } else {
            $this->data['modal'] = true;
        }

        $this->run();
    }

    /**
     * Save data from oil type form.
     */
    protected function setUnloadingPoints()
    {
        $v = new OilTypeValidator();
        $this->oForm = $v->getData();
        $oCollection = Collection::getCurrent();

        if ($this->oForm->validate()) {
            // Delete all old entries
            UnloadingPoint::deleteAllAddressesByPidAndParentTable($oCollection->id, 'tl_calc_collection');

            $aUnloadingPoints = array();
            // Get entered partial amount for main address.
            $iMainPartialAmount = $this->oForm->fetch('partialAmount');
            $iCombinedAmount = $iMainPartialAmount;

            $oCollection->partialAmount = $iMainPartialAmount;
            $oCollection->save();

            $iOriginalAntifreezeAmount = $oCollection->getAntifreeze();

            /** @var Widget $oWidget */
            foreach ($this->oForm->getWidgets() as $oWidget) {
                // Skip all widgets that have no order attribute, e. g. headlines and explanations.
                if (!$oWidget->order) {
                    continue;
                }

                $aUnloadingPoints[$oWidget->order][] = $oWidget;
            }

            foreach ($aUnloadingPoints as $iOrder => $aUnloadingPoint) {
                $oUnloadingPoint = new UnloadingPoint();
                $oUnloadingPoint->pid = $oCollection->id;
                $oUnloadingPoint->tstamp = time();
                $oUnloadingPoint->ptable = 'tl_calc_collection';
                $oUnloadingPoint->unloadingpointorder = $iOrder;

                /** @var Widget $oWidget */
                foreach ($aUnloadingPoint as $oWidget) {
                    // Store values from form submission in unloadingPoint
                    $oUnloadingPoint->{$oWidget->originalField} = $this->oForm->fetch($oWidget->name);

                    if ($oWidget->originalField === 'partialAmount') {
                        $iCombinedAmount += (int)$this->oForm->fetch($oWidget->name);
                    }
                }

                $oUnloadingPoint->save();

            }

            $iNewAntifreezeAmount = $oCollection->getAntifreeze();

            /**
             * Update collection if combined amount differs from originally entered amount
             * OR
             * antifreeze amount has changed
             */
            if ($iCombinedAmount != $oCollection->getAmount() || $iNewAntifreezeAmount != $iOriginalAntifreezeAmount) {
                $oCollection->amount = $iCombinedAmount;
                Collection::updateCollection(true);
            }

            if (!$v->isValid()) {
                $this->oForm = $v->getData();
            } else {
                $this->data['modal'] = false;
            }
        } else {
            $this->data['modal'] = true;
        }

        $this->run();
    }

    /**
     * Generate form for unloading points.
     */
    protected function editUnloadingPoints()
    {
        $oUnloadingPointsForm = new UnloadingPointsFormCreator();
        $this->oForm = $oUnloadingPointsForm->create(false);
        $this->oForm->setFormActionFromUri('ajaxcontroller.html?action=editUnloadingPoints');
        $this->oForm->setNoValidate(false);

        $oCollection = Collection::getCurrent();

        $this->sFormTemplate = 'shipping-address-template';

        if ($this->oForm->validate()) {
            // Delete all old entries
            UnloadingPoint::deleteAllAddressesByPidAndParentTable($oCollection->id, 'tl_calc_collection');

            $aUnloadingPoints = array();

            // Get entered partial amount for main address.
            $iMainPartialAmount = ($oCollection->partialAmount) ? $oCollection->partialAmount : 0;
            $iCombinedAmount = $iMainPartialAmount;

            $oCollection->partialAmount = $iMainPartialAmount;
            $oCollection->save();

            $iOriginalAntifreezeAmount = $oCollection->getAntifreeze();

            /** @var Widget $oWidget */
            foreach ($this->oForm->getWidgets() as $oWidget) {
                // Skip all widgets that have no order attribute, e. g. headlines and explanations.
                if (!$oWidget->order) {
                    continue;
                }

                $aUnloadingPoints[$oWidget->order][] = $oWidget;
            }

            foreach ($aUnloadingPoints as $iOrder => $aUnloadingPoint) {
                $oUnloadingPoint = new UnloadingPoint();
                $oUnloadingPoint->pid = $oCollection->id;
                $oUnloadingPoint->tstamp = time();
                $oUnloadingPoint->ptable = 'tl_calc_collection';
                $oUnloadingPoint->unloadingpointorder = $iOrder;

                /** @var Widget $oWidget */
                foreach ($aUnloadingPoint as $oWidget) {
                    // Store values from form submission in unloadingPoint
                    $oUnloadingPoint->{$oWidget->originalField} = $this->oForm->fetch($oWidget->name);

                    if ($oWidget->originalField === 'partialAmount') {
                        $iCombinedAmount += (int)$this->oForm->fetch($oWidget->name);
                    }
                }

                $oUnloadingPoint->save();

            }

            $iNewAntifreezeAmount = $oCollection->getAntifreeze();

            /**
             * Update collection if combined amount differs from originally entered amount
             * OR
             * antifreeze amount has changed
             * OR
             * the new amount is zero.
             */
            if ($iCombinedAmount != $oCollection->getAmount() || $iNewAntifreezeAmount != $iOriginalAntifreezeAmount || $iNewAntifreezeAmount == 1 && $iOrder == 1) {
                $oCollection->amount = $iCombinedAmount;
                Collection::updateCollection(true);
            }

            $this->data['modal'] = false;
        } else {
            $this->data['modal'] = true;
        }

        $this->run();
    }

    /**
     * Generate form for shipping method.
     */
    protected function getShippingTemplate()
    {

        $this->generateForm();
        $this->sFormTemplate = 'shipping-template';
        $this->generateSelectableOptions($GLOBALS['TL_CALC']['shippingValues'], 'shipping');
        $this->oForm->bindModel(Collection::getCurrent());
        $this->oForm->getWidget('shipping')->shippingDefinition = true;

        if ($this->oForm->validate()) {
            // check changes
            /** @var Collection $collection */
            Collection::updateCollection();

            $v = new ShippingValidator();

            if (!$v->isValid()) {
                $this->oForm = $v->getData();
            } else {
                $this->data['modal'] = false;
            }
        }

        $this->run();
    }

    /**
     * Generate form for payment method.
     */
    protected function getPaymentTemplate()
    {
        $collection = Collection::getCurrent();

        $oPaymentForm = new PaymentForm();
        $this->oForm = $oPaymentForm->create();
        $this->oForm->setFormActionFromUri('ajaxcontroller.html?action=getPayment');
        $this->sFormTemplate = 'payment-template';


        if ($this->oForm->validate()) {
            Collection::updateCollection();
            $v = new PaymentValidator();

            if (!$v->isValid()) {
                $this->oForm = $v->getData();
                $this->data['modal'] = true;
            } else {
                $this->data['modal'] = false;
            }

        } else {
            $this->data['modal'] = true;
        }

        $this->run();
    }

    /**
     * Validate form with birthday data.
     */
    protected function setBirthday()
    {

        $v = new PaymentValidator();
        $this->oForm = $v->getData();

        if ($this->oForm->validate()) {
            $collection = Collection::getCurrent();
            $collection->birthday = $this->oForm->fetch('birthday');
            $collection->save();
            $this->data['modal'] = false;
        } else {
            $this->data['modal'] = true;
        }

        $this->run();
    }

    /**
     * Validate zmz data.
     *
     * @throws \Exception
     */
    protected function setzmz()
    {
        $v = new PaymentValidator();
        $this->oForm = $v->getData();
        $oCollection = Collection::getCurrent();

        // If shipping address == billing address, remove mandatory eval from all fields of the shipping address group.
        if (Input::post('shippingAddressEqualsBillingAddress')) {
            foreach ($this->oForm->getWidgets() as $oWidget) {

                if (is_array($oWidget->group)) {
                    if (in_array('shipping', $oWidget->group)) {
                        $oWidget->mandatory = false;

                    }
                }
            }
        }

        if ($this->oForm->validate()) {

            if (Input::post('shippingAddressEqualsBillingAddress')) {
                //set shipping fields equals billind fields
                $oCollection->shippingFirstname = Input::post('billingFirstname');
                $oCollection->shippingLastname = Input::post('billingLastname');
                $oCollection->shippingStreet = Input::post('billingStreet');
                $oCollection->shippingCity = Input::post('billingCity');

                if ($oCollection->shippingPostal != Input::post('billingPostal')) {
                    $oCollection->shippingPostal = Input::post('billingPostal');
                    $oCollection->postal = Input::post('billingPostal');

                    $oCollection->labelGroup = 'vario';
                    $oCollection->save();

                    $oProduct = new \Slashworks\SwCalc\Models\Product();
                    $oCollection->saveNewProductInCollection($oProduct);
                } else {
                    $oCollection->save();
                }
            } else {
                $oCollection->save();
            }

            $this->data['modal'] = false;
        } else {
            $this->data['modal'] = true;

            // Group errors.
            /** @var Widget $oWidget */
            foreach ($this->oForm->getWidgets() as $oWidget) {
                if ($oWidget->hasErrors()) {

                    $this->aGlobalErrors[$oWidget->selectedGroup][] = array
                    (
                        'label'        => $oWidget->label,
                        'errorMessage' => $oWidget->getErrorAsString(),
                    );
                }
            }
        }

        $this->run();
    }

    /**
     * Generate form for shipping address data.
     */
    protected function getShippingAddressTemplate()
    {
        $collection = Collection::getCurrent();

        $this->generateForm('shipping', 'Lieferanschrift ändern');
        $this->sFormTemplate = 'shipping-address-template';
        $this->oForm->setNoValidate(true);

        // Add custom validator for shippingPostal field.
        $this->oForm->addValidator('shippingPostal', new PostalValidator());

        if ($collection->unloadingPoints > 1) {
            // Remove submit field from automatically generated form to place partialAmount before it.
            $this->oForm->removeFormField('submit');

            $this->oForm->addFormField('partialAmount', array(
                'label'     => '1. Teilmenge',
                'inputType' => 'text',
                'value'     => ($collection->partialAmount) ? $collection->partialAmount : '',
                'eval'      => array
                (
                    'rgxp'   => 'natural',
                    'minval' => Pricing::getMinAmount(),
                    'mandatory' => true
                ),
            ));

            $this->oForm->addFormField('antifreeze', $GLOBALS['TL_DCA']['tl_calc_collection']['fields']['antifreeze']);

            // Add submit field after partialAmount field has been added.
            $this->oForm->addFormField('submit', array
            (
                'label'     => 'Änderungen übernehmen {{svg::arrow-right}}',
                'inputType' => 'submit',
                'eval'      => array
                (
                    'class' => 'button-mega',
                ),
            ));
        }

        if ($this->oForm->validate()) {

            // check changes
            /** @var Collection $collection */
            Collection::updateCollection();
            $v = new ShippingAddressValidator();

            if (!$v->isValid()) {
                $this->oForm = $v->getData();

            } else {
                $this->data['modal'] = false;
            }
        }

        // Group errors.
        /** @var Widget $oWidget */
        foreach ($this->oForm->getWidgets() as $oWidget) {
            if ($oWidget->hasErrors()) {

                $this->aGlobalErrors[$oWidget->selectedGroup][] = array
                (
                    'label'        => $oWidget->label,
                    'errorMessage' => $oWidget->getErrorAsString(),
                );
            }
        }

        $this->run();

    }

    /**
     * Generate form for billing address data.
     */
    protected function getBillingAddresseTemplate()
    {

        $this->generateForm('billing', 'Rechnungsadresse ändern');
        $this->sFormTemplate = 'billing-address-template';
        $this->oForm->setNoValidate(true);

        if ($this->oForm->validate()) {
            $collection = Collection::getCurrent();
            $collection->shippingAddressEqualsBillingAddress = 0;
            Collection::getCurrent()->save();

            $v = new BillingAddressValidator();

            if (!$v->isValid()) {
                $this->oForm = $v->getData();
            } else {
                $this->data['modal'] = false;
            }
        }

        // Group errors.
        /** @var Widget $oWidget */
        foreach ($this->oForm->getWidgets() as $oWidget) {
            if ($oWidget->hasErrors()) {

                $this->aGlobalErrors[$oWidget->selectedGroup][] = array
                (
                    'label'        => $oWidget->label,
                    'errorMessage' => $oWidget->getErrorAsString(),
                );
            }
        }

        $this->run();
    }

    /**
     * Generate form for contact data.
     */
    protected function getContactTemplate()
    {
        $this->generateForm('contact', 'Kontakt ändern');
        $this->sFormTemplate = 'contact-template';

        // Add a custom validator to the mobile number field to check phone and mobile combination.
        $this->oForm->addValidator('mobile', new PhoneOrMobile());

        if ($this->oForm->validate()) {
            $collection = Collection::getCurrent();
            $collection->save();

            $this->data['modal'] = false;
        } else {
            $this->data['modal'] = true;
        }

        // Group errors.
        /** @var Widget $oWidget */
        foreach ($this->oForm->getWidgets() as $oWidget) {
            if ($oWidget->hasErrors()) {

                $this->aGlobalErrors[$oWidget->selectedGroup][] = array
                (
                    'label'        => $oWidget->label,
                    'errorMessage' => $oWidget->getErrorAsString(),
                );
            }
        }

        $this->run();
    }

    /**
     * Generate form for additional information.
     *
     * @throws \Exception
     */
    protected function getAdditionalInformationTemplate()
    {
        $oCollection = Collection::getCurrent();
        $this->generateForm('additionalInformationReview',
            Label::getLabel('checkout.step3.changeAdditionalInformation'));
        $this->sFormTemplate = 'additional-information-template';

        $oPricing = Pricing::findByPostal();

        if ($oCollection->unloadingPoints > 1) {
            // Remove antifreeze field from additional information form if more than 1 unloading point has been set.
            $this->oForm->removeFormField('antifreeze');
        } else {
            // Order is important: first check antifreeze, then specialTankTruck, then hose.
            if (!$oPricing->antifreeze) {
                $this->oForm->removeFormField('antifreeze');
            }
        }

        /**
         * Remove the non-default values from the tanker select when there is no value specified in the database.
         */
        if (!$oPricing->specialTankTruckSmall) {
            /** @var FormSelectMenu $oTankerWidget */
            $oTankerWidget = $this->oForm->getWidget('tanker');
            $aOptions = $oTankerWidget->options;
            foreach ($aOptions as $i => $aOption) {
                if ($aOption['value'] !== 'default') {
                    unset($aOptions[$i]);
                }
            }
            $oTankerWidget->__set('options', $aOptions);
        }

        if ($this->oForm->validate()) {

            // Manually save notes.
            $oCollection->notes = $this->oForm->fetch('notes');
            $oCollection->save();

            /** @var Collection $collection */
            Collection::updateCollection();

            $v = new AdditionalInformationValidator();

            if (!$v->isValid()) {
                $this->oForm = $v->getData();
            } else {
                // Update product in collection to regenerate price with new values.
                $oCollection = Collection::getCurrent();
                $oCollection->saveNewProductInCollection(new Product());
                $oCollection->save();

                $this->data['modal'] = false;
            }
        }

        $this->run();

    }

    /**
     * Generate form for price details.
     *
     * @throws \Exception
     */
    protected function getPriceDetailsTemplate()
    {
        $oCollection = Collection::getCurrent();
        $oTemplate = new FrontendTemplate('partial_review_price_details');
        $oPricing = Pricing::findByPostal();

        $fields = array(
            'totalPer100'       => Formatter::formatPriceWithCurrency($oCollection->totalPer100),
            'subTotalPer100'    => Formatter::formatPriceWithCurrency($oCollection->subTotalPer100),
            'subTotalPerAmount' => Formatter::formatPriceWithCurrency($oCollection->subTotalPerAmount),
            'subTotal'          => Formatter::formatPriceWithCurrency($oCollection->subTotal),
            'adr'               => Formatter::formatPriceWithCurrency($oCollection->adr),
            'adrFlat'           => Formatter::formatPriceWithCurrency($oPricing->getPriceForAdr()),
            'vat'               => Formatter::formatPriceWithCurrency($oCollection->vat),
            'total'             => Formatter::formatPriceWithCurrency($oCollection->total),
        );

        // Include separate field for antifreeze.
        if ($oCollection->hasAntifreeze()) {
            $fAntiFreezePrice = Formatter::formatPriceWithCurrency($oCollection->antifreezeSurcharge);
            $aAntiFreezeField = array('antifreeze' => $fAntiFreezePrice);
            array_insert($fields, 3, $aAntiFreezeField);
            $oTemplate->antifreezeAmount = $oCollection->getAntifreeze();

            // Prevent division by zero.
            if ($oCollection->antifreeze > 0) {
                $oTemplate->antifreezePerUnit = Formatter::formatPriceWithCurrency($oCollection->antifreezeSurcharge / $oCollection->antifreeze);
            }
        }

        $oTemplate->amount = Formatter::formatNumber($oCollection->getAmount());
        $oTemplate->oilType = $GLOBALS['TL_LANG']['tl_calc_collection']['oilType'][$oCollection->getOilType()];
        foreach ($fields as $sFieldName => $sFieldValue) {
            $oTemplate->{$sFieldName} = $sFieldValue;
        }
        $oTemplate->fields = $fields;

        $this->data['modal'] = true;
        $this->data['content'] = $oTemplate->parse();

        echo json_encode($this->data);
        exit;
    }

    // ###########################
    //  general Logic
    // ###########################

    /**
     * Generate a form.
     *
     * @param string $name
     * @param string $headline
     */
    protected function generateForm($name = 'form', $headline = '')
    {
        $this->oForm = new Form($name, 'POST', function ($oHaste) {
            return Input::post('FORM_SUBMIT') === $oHaste->getFormId();
        });

        $this->oForm->bindModel(Collection::getCurrent());

        if ($name != 'form') {
            $this->addFormGroups($name, $headline);
        }

        $this->data['modal'] = true;
    }

    /**
     * Add a form group by its name.
     *
     * @param string $name
     * @param string $headline
     */
    protected function addFormGroups($name, $headline = '')
    {
        $this->oForm = FormGroups::addWrapperStart($this->oForm, $name . '_fieldset');
        $this->oForm = FormGroups::addFormGroupFields($this->oForm, $name, $headline);
        $this->oForm = FormGroups::addWrapperStop($this->oForm, $name . '_fieldset');

        $this->oForm->addFormField('submit', array
        (
            'label'     => 'Änderungen übernehmen {{svg::arrow-right}}',
            'inputType' => 'submit',
            'eval'      => array
            (
                'class' => 'button-mega',
            ),
        ));
    }

    /**
     * Generate forms and wrapper template.
     */
    protected function replaceFormInserttags()
    {
        $Content = \Controller::replaceInsertTags($this->oForm->generate($this->sCustomFormTemplate));
        $tpl = new \FrontendTemplate('modalbox_wrapper');
        $tpl->formTemplate = $this->sFormTemplate;
        $tpl->headline = Label::getLabel('checkout.changeValues');
        $tpl->form = str_replace('{{request_token}}', REQUEST_TOKEN, $Content);
        $tpl->globalErrors = $this->aGlobalErrors;

        // Add javascript for showing errors in form fields, e. g. amount and postal field.
        $oJavascriptHelper = new JavascriptHelper();
        $tpl->scripts = $oJavascriptHelper->getTooltipScript();

        $sParsedForm = $tpl->parse();

        $this->data['content'] = $sParsedForm . $this->getAutocompleteScript();
//        $this->data['content'] = $sParsedForm;
    }

    protected function getAutocompleteScript()
    {
        $sAutocompleteScripts = '';

        $aAutocompleteFields = array();

        /** @var \Contao\Widget $oWidget */
        foreach ($this->oForm->getWidgets() as $oWidget) {
            if ($oWidget->autocompleterValue) {
                $aAutocompleteFields[$oWidget->order][$oWidget->autocompleterKey] = $oWidget->autocompleterValue;
            }
        }

        $sAutocompleteScripts = '';
        foreach ($aAutocompleteFields as $aAutocompleteField) {
            $sAutocompleteScripts .= '<script>new AutoCompleter(' . json_encode($aAutocompleteField) . ');</script>';
        }

        return $sAutocompleteScripts;
    }

    /**
     * Generate selectable radio fields, including prices.
     *
     * @param $group
     * @param $name
     */
    protected function generateSelectableOptions($group, $name)
    {

        foreach ($group as $item) {
            $options[$item] = $item;
        }

        //add checkbox
        $this->oForm->addFormField($name, array
        (
            'inputType' => 'radio',
            'options'   => $options,
            'eval'      => array(
                'template' => 'widget_radioExplanation',
                'addPriceToLabel' => true
            ),
        ));

        $this->oForm->addFormField('submit', array
        (
            'label'     => 'Änderungen übernehmen {{svg::arrow-right}}',
            'inputType' => 'submit',
            'eval'      => array
            (
                'class' => 'button-mega',
            ),
        ));

    }

    /**
     * Run all methods and get json data for javascript processing.
     */
    protected function run()
    {
        $this->replaceFormInserttags();

        if ($this->debug) {
            print_r($this->data);
        } else {
            echo json_encode($this->data);
        }
        exit;
    }

}