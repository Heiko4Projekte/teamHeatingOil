<?php

namespace Slashworks\SwCalc\Helper;

use Contao\Input;
use Haste\Form\Form;
use Slashworks\SwCalc\Classes\Formatter;
use Slashworks\SwCalc\Classes\FormGroups;
use Slashworks\SwCalc\Models\Collection;

class zmzFormCreator{

    /**
     * The haste form object.
     *
     * @var Form
     */
    protected $oForm;

    /**
     * @var Collection
     */
    protected $oCollection;


    /**
     * @return Form
     */
    public function create()
    {
        $this->oCollection = Collection::getCurrent();
        $this->prefillNonCollectionFields();

        $this->oForm = new Form('zmzaccount_form', 'POST', function ($oHaste) {
            return Input::post('FORM_SUBMIT') === $oHaste->getFormId();
        });

        $this->oForm->bindModel($this->oCollection);
        $this->oForm->setNoValidate(true);
        $this->addFields();

        return $this->oForm;

    }


    /**
     *
     */
    protected function prefillNonCollectionFields()
    {
        $this->oCollection->yearlyAmount = $this->oCollection->getAmount();
        $this->oCollection->yearlyPrice = Formatter::formatPrice($this->oCollection->getTotal());
        // Initial price for sepa equals half the total value.
        $this->oCollection->sepaInitialPrice = Formatter::formatPrice($this->oCollection->getTotal() / 2);
        $this->oCollection->sepaMonthlyPrice = Formatter::formatPrice($this->oCollection->getTotal() / 12);
    }


    /**
     *
     */
    protected function addFields()
    {
        $this->oForm = FormGroups::addWrapperStart($this->oForm, 'zmzMaster');
        $this->oForm = FormGroups::addFormGroupFields($this->oForm, 'zmzMaster', 'Hauptdaten');
        $this->oForm = FormGroups::addWrapperStop($this->oForm, 'zmzMaster');

        $this->oForm = FormGroups::addWrapperStart($this->oForm, 'zmzChild');
        $this->oForm = FormGroups::addFormGroupFields($this->oForm, 'zmzChild', 'Ehepartner/Mitbewohner/Lebenspartner');
        $this->oForm = FormGroups::addWrapperStop($this->oForm, 'zmzChild');

        $this->oForm = FormGroups::addWrapperStart($this->oForm, 'zmzBillingAddress');
        $this->oForm = FormGroups::addFormGroupFields($this->oForm, 'zmzBillingAddress', 'Rechnungsadresse');
        $this->oForm = FormGroups::addWrapperStop($this->oForm, 'zmzBillingAddress');

        $this->oForm = FormGroups::addWrapperStart($this->oForm, 'shippingAddressEqualsBillingAddress');
        $this->oForm = FormGroups::addFormGroupFields($this->oForm, 'shippingAddressEqualsBillingAddress', '');
        $this->oForm = FormGroups::addWrapperStop($this->oForm, 'shippingAddressEqualsBillingAddress');

        $this->addShippingFields();
        $this->addYearlyFields();
        $this->addSepaDescriptionFields();

        $this->oForm = FormGroups::addHeadline($this->oForm,'sepaHeadline','Konto Details');
        $this->oForm = FormGroups::addWrapperStart($this->oForm, 'sepa');
        $this->oForm = FormGroups::addFormGroupFields($this->oForm, 'sepa', '');
        $this->oForm = FormGroups::addWrapperStop($this->oForm, 'sepa');

        $this->addSepaInstructionFields();

        $this->oForm = FormGroups::addWrapperStart($this->oForm, 'acceptAgb');
        $this->oForm = FormGroups::addFormGroupFields($this->oForm, 'acceptAgb', '');
        $this->oForm = FormGroups::addWrapperStop($this->oForm, 'acceptAgb');

        $this->oForm = FormGroups::addWrapperStart($this->oForm, 'sendNews');
        $this->oForm = FormGroups::addFormGroupFields($this->oForm, 'sendNews', '');
        $this->oForm = FormGroups::addWrapperStop($this->oForm, 'sendNews');


        $this->addSubmitField();
    }

    /**
     * Add submit field.
     */
    protected function addSubmitField()
    {
        $this->oForm->addFormField('submit', array
        (
            'label' => 'Weiter {{svg::chevron-right}}',
            'inputType' => 'submit',
            'eval' => array
            (
                'class' => ''
            )
        ));
    }

    protected function addShippingFields()
    {
        $this->oForm = FormGroups::addWrapperStart($this->oForm, 'shippingAddress');

        $this->oForm = FormGroups::addHeadline($this->oForm,'shippingHeadline','Lieferadresse');
        $this->oForm->addFormField('shippingSalutation', $GLOBALS['TL_DCA']['tl_calc_collection']['fields']['shippingSalutation']);
        $this->oForm->addFormField('shippingFirstname', $GLOBALS['TL_DCA']['tl_calc_collection']['fields']['shippingFirstname']);
        $this->oForm->addFormField('shippingLastname', $GLOBALS['TL_DCA']['tl_calc_collection']['fields']['shippingLastname']);
        $this->oForm->addFormField('shippingStreet', $GLOBALS['TL_DCA']['tl_calc_collection']['fields']['shippingStreet']);
        $this->oForm->addFormField('shippingPostal', $GLOBALS['TL_DCA']['tl_calc_collection']['fields']['shippingPostal']);
        $this->oForm->addFormField('shippingCity', $GLOBALS['TL_DCA']['tl_calc_collection']['fields']['shippingCity']);

        $this->oForm = FormGroups::addWrapperStop($this->oForm, 'shippingAddress');
    }

    protected function addYearlyFields()
    {
        $this->oForm = FormGroups::addWrapperStart($this->oForm, 'yearlyFields');
        $this->oForm = FormGroups::addHeadline($this->oForm,'produktangaben','Preise');


        /**
         *
         */
        $this->oForm->addFormField('yearlyAmount', array
        (
            'label' => 'Jahresbedarf',
            'inputType' => 'text',
            'eval' => array
            (
//                'disabled' => true,
                'readonly' => true,
                'class' => 'widget-yearlyAmount'
            )
        ));

        $this->oForm->addFormField('yearlyPrice', array
        (
            'label' => 'Rechnungsbetrag in Euro',
            'inputType' => 'text',
            'eval' => array
            (
//                'disabled' => true,
                'readonly' => true,
                'class' => 'widget-yearlyPrice'
            )
        ));

        $this->oForm = FormGroups::addWrapperStop($this->oForm, 'yearlyFields');
    }

    protected function addSepaDescriptionFields()
    {
        $this->oForm = FormGroups::addHeadline($this->oForm, 'sepaDescription', 'Erteilung eines SEPA-Lastschriftmandats');

        $this->oForm->addFormField('sepaExplanation', array
        (
            'inputType' => 'html',
            'eval' => array
            (
                'html' => '<div class="sepaExplanation">' . $GLOBALS['TL_LANG']['zmzAccountForm']['sepaExplanation'] . '</div>'
            )
        ));
    }

    protected function addSepaInstructionFields()
    {
        $this->oForm->addFormField('sepaInstructions', array
        (
            'inputType' => 'html',
            'eval' => array
            (
                'html' => '<div class="sepaInstructions">' . $GLOBALS['TL_LANG']['zmzAccountForm']['sepaInstructions'] . '</div>'
            )
        ));
    }



}