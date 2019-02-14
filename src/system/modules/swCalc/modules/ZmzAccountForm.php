<?php

namespace Slashworks\SwCalc\Modules;

use Contao\BackendTemplate;
use Contao\Controller;
use Contao\Environment;
use Contao\Input;
use Contao\Module;
use Contao\Session;
use Haste\Form\Form;
use Slashworks\SwCalc\Helper\zmzFormCreator;
use Slashworks\SwCalc\Models\Collection;
use Slashworks\SwCalc\Models\Label;

class ZmzAccountForm extends Module
{

    /**
     * The template name.
     *
     * @var string
     */
    protected $strTemplate = 'mod_calc_zmzaccountform';

    /**
     * The haste form object.
     *
     * @var Form
     */
    protected $oForm;

    /**
     * Holds global error messages, grouped by category.
     *
     * @var array
     */
    protected $aGlobalErrors = array();

    /**
     * @var Collection
     */
    protected $oCollection;

    public function generate()
    {
        if (TL_MODE === 'BE') {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['zmzaccountform'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        global $objPage;
        $objPage->customPageViewUrl = Environment::get('path') . '/waermekonto.html';
        $objPage->title = Label::getLabel('payment.zmzAccount.title');

        return parent::generate();
    }


    public function compile()
    {
        $this->hl = 'h1';
        $this->headline = $GLOBALS['TL_LANG']['zmzAccountForm']['headlineDefault'];

        $oZmzForm = new zmzFormCreator();
        $this->oForm = $oZmzForm->create();
        $this->oCollection = Collection::getCurrent();

        if ($this->oForm->isSubmitted()) {
            $this->validateForm();
        }

        $this->Template->form = $this->oForm->generate();
        $this->Template->globalErrors = $this->aGlobalErrors;
    }


    protected function validateForm()
    {

        // If shipping address != billing address, remove mandatory eval from all fields of the billing address group.
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
                $this->oCollection->shippingFirstname = Input::post('billingFirstname');
                $this->oCollection->shippingLastname = Input::post('billingLastname');
                $this->oCollection->shippingStreet = Input::post('billingStreet');
                $this->oCollection->shippingCity = Input::post('billingCity');

                if ($this->oCollection->shippingPostal != Input::post('billingPostal')) {
                    $this->oCollection->shippingPostal = Input::post('billingPostal');
                    $this->oCollection->postal = Input::post('billingPostal');

                    $this->oCollection->save();

                    $oProduct = new \Slashworks\SwCalc\Models\Product();
                    $this->oCollection->saveNewProductInCollection($oProduct);
                } else {
                    $this->oCollection->save();
                }
            } else {
                $this->oCollection->save();
            }

            Session::getInstance()->set('skipZmzAccountForm', 1);
            Controller::reload();
        }

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


    public function getFormObject()
    {

        return $this->oForm;

    }
}