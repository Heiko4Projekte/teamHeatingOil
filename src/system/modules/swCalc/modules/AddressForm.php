<?php

namespace Slashworks\SwCalc\Modules;

use Contao\BackendTemplate;
use Contao\Controller;
use Contao\FormSelectMenu;
use Contao\Input;
use Contao\Module;
use Contao\PageModel;
use Contao\Widget;
use Haste\Form\Form;
use Slashworks\SwCalc\Classes\FormGroups;
use Slashworks\SwCalc\Helper\GeoCoder;
use Slashworks\SwCalc\Models\Collection;
use Slashworks\SwCalc\Models\Pricing;
use Slashworks\SwCalc\Models\Product;
use Slashworks\SwCalc\Validators\PhoneOrMobile;
use Slashworks\SwCalc\Validators\PostalValidator;

class AddressForm extends Module
{

    /**
     * The template name.
     *
     * @var string
     */
    protected $strTemplate = 'mod_calc_addressform';

    /**
     * The haste form object.
     *
     * @var Form
     */
    protected $oForm;

    /**
     * @var string
     */
    protected $sPostal;

    /**
     * Holds global error messages, grouped by category.
     *
     * @var array
     */
    protected $aGlobalErrors = array();


    /**
     * @return string
     */
    public function generate()
    {
        if (TL_MODE === 'BE') {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['addressform'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }
        return parent::generate();
    }


    /**
     * Parse the template.
     */
    protected function compile()
    {
        $this->hl = 'h1';
        $this->headline = $GLOBALS['TL_LANG']['addressForm']['headlineDefault'];

        // Create a new haste form.
        $this->oForm = new Form('address_form', 'POST', function($oHaste) {
            return Input::post('FORM_SUBMIT') === $oHaste->getFormId();
        });

        // Bind the form to the current collection.
        $this->oForm->bindModel(Collection::getCurrent());

        // Disable html5 form validation.
        $this->oForm->setNoValidate(true);

        // Add all fields.
        $this->addFields();

        // Add custom validator for shipping postal field.
        $this->oForm->addValidator('shippingPostal', new PostalValidator());
        // Add custom validator for billing postal field.
        //$this->oForm->addValidator('billingPostal', new PostalValidator());

        // Get postal for template usage.
        if (Collection::getCurrent()) {
            $this->sPostal = Collection::getCurrent()->shippingPostal;
        }

        // Validate form if it has been submitted.
        if ($this->oForm->isSubmitted()) {
            $this->validateForm();
        }

        // Generate form string with custom template 'form_address'.
        $this->Template->form = $this->oForm->generate('form_address');
        $this->Template->globalErrors = $this->aGlobalErrors;

        if ($this->sPostal) {
            $aGeoData = GeoCoder::getGeoDataByPostal($this->sPostal);

            if ($aGeoData !== null) {
                $this->Template->postalBounds = json_encode(GeoCoder::getBoundsByGeoData($aGeoData), true);
            }
        }
    }


    /**
     * Add all necessary fields.
     */
    protected function addFields()
    {
        // Add fields of group "shipping".
        $this->oForm = FormGroups::addWrapperStart($this->oForm, 'shipping');
        $this->oForm = FormGroups::addFormGroupFields($this->oForm, 'shipping', 'Lieferadresse');
        $this->oForm = FormGroups::addWrapperStop($this->oForm, 'shipping');

        // Add fields of group "shippingEqualsBillingAddress".
        $this->oForm = FormGroups::addWrapperStart($this->oForm, 'shippingAddressEqualsBillingAddress');
        $this->oForm = FormGroups::addFormGroupFields($this->oForm, 'shippingAddressEqualsBillingAddress', '');
        $this->oForm = FormGroups::addWrapperStop($this->oForm, 'shippingAddressEqualsBillingAddress');

        // Add fields of group "billing":
        $this->oForm = FormGroups::addWrapperStart($this->oForm, 'billing');
        $this->oForm = FormGroups::addFormGroupFields($this->oForm, 'billing', 'Rechnungsadresse');
        $this->oForm = FormGroups::addWrapperStop($this->oForm, 'billing');

        // Add birthday field if invoice is set
        if (Collection::getCurrent()->getPayment() === 'invoice') {
            $this->oForm = FormGroups::addWrapperStart($this->oForm, 'birthday');
            $this->oForm->addFormField('birthday', $GLOBALS['TL_DCA']['tl_calc_collection']['fields']['birthday']);
            $this->oForm = FormGroups::addWrapperStop($this->oForm, 'birthday');
        }

        // Add fields of group "contact".
        $this->oForm = FormGroups::addWrapperStart($this->oForm, 'contact');
        $this->oForm = FormGroups::addFormGroupFields($this->oForm, 'contact', 'Kontakt');
        $this->oForm = FormGroups::addWrapperStop($this->oForm, 'contact');

        // Add fields of group "additionalInformation".
        $this->oForm = FormGroups::addWrapperStart($this->oForm, 'additionalInformation');
        $this->oForm = FormGroups::addFormGroupFields($this->oForm, 'additionalInformation', 'Besonderheiten der Lieferung');
        $this->oForm = FormGroups::addWrapperStop($this->oForm, 'additionalInformation');

        // Add a custom validator to the mobile number field to check phone and mobile combination.
        $this->oForm->addValidator('mobile', new PhoneOrMobile());

        $this->addSubmitField();

        $oPricing = Pricing::findByPostal();

        $oCollection = Collection::getCurrent();
        // Order is important: first check antifreeze, then specialTankTruck, then hose.
        if (!$oPricing->antifreeze or $oCollection->unloadingPoints > 1) {
            $this->oForm->removeFormField('antifreeze');
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
    }


    /**
     * Add submit field.
     */
    protected function addSubmitField()
    {
        $this->oForm->addFormField('submit_form', array
        (
            'label' => 'Weiter {{svg::arrow-right}}',
            'inputType' => 'submit',
            'eval' => array
            (
                'class'   => '',
                //'onclick' => 'checkout(this); return !ga.loaded;',
            )
        ));
    }


    /**
     * Validate form submission.
     */
    protected function validateForm()
    {
        // If shipping address = billing address, remove mandatory eval from all fields of the billing address group.
        if (Input::post('shippingAddressEqualsBillingAddress')) {
            foreach ($this->oForm->getWidgets() as $oWidget) {
                if ($oWidget->selectedGroup !== 'billing') {
                    continue;
                }

                $oWidget->mandatory = false;
            }
        }

        if ($this->oForm->validate()) {
            // Manually save notes to collection.
            // TODO: Find out why this does not happen automatically as with other fields.
            $oCollection = Collection::getCurrent();
            $oCollection->notes = $this->oForm->fetch('notes');
            $oCollection->save();

            Collection::updateCollection();

            $this->redirectToNextStep();
        }

        /**
         * Set postal to entered value.
         * This only applies on errors. Otherwise the redirect will occur.
         */
        $this->sPostal = $this->oForm->fetch('shippingPostal');

        // Group errors.
        /** @var Widget $oWidget */
        foreach ($this->oForm->getWidgets() as $oWidget) {
            if ($oWidget->hasErrors()) {
                $this->aGlobalErrors[$oWidget->selectedGroup][] = array
                (
                    'label' => $oWidget->label,
                    'errorMessage' => $oWidget->getErrorAsString()
                );
            }
        }

    }


    /**
     * Redirect to the next step, defined as jumpTo in the module itself.
     *
     * @throws \Exception
     */
    protected function redirectToNextStep()
    {
        if (!$this->jumpTo) {
            throw new \Exception('No redirect page for addressform module defined');
        }

        $oJumpToPage = PageModel::findById($this->jumpTo);
        if ($oJumpToPage === null) {
            throw new \Exception('No redirect page found.');
        }

        Controller::redirect($oJumpToPage->getFrontendUrl());
    }

}