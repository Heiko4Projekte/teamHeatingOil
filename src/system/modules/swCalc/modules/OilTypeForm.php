<?php

namespace Slashworks\SwCalc\Modules;

use Contao\BackendTemplate;
use Contao\Controller;
use Contao\Environment;
use Contao\Input;
use Contao\Module;
use Contao\Session;
use Haste\Form\Form;
use Slashworks\SwCalc\Models\Collection;
use Slashworks\SwCalc\Models\Pricing;
use Slashworks\SwCalc\Models\Product;
use Slashworks\SwCalc\Models\ProductConfiguration;

class OilTypeForm extends Module
{

    /**
     * The template name.
     *
     * @var string
     */
    protected $strTemplate = 'mod_calc_oiltypeform';

    /**
     * The haste form object.
     *
     * @var Form
     */
    protected $oForm;

    public function generate()
    {
        if (TL_MODE === 'BE') {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['oiltypeform'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        global $objPage;
        $objPage->customPageViewUrl = Environment::get('path') . '/qualitaetsauswahl.html';
        $objPage->title = 'Qualitätsauswahl';

        return parent::generate();
    }

    protected function compile()
    {
        $this->hl = 'h1';
        $this->headline = $GLOBALS['TL_LANG']['oilTypeForm']['headlineDefault'];

        $this->oForm = new Form('oiltype_form', 'POST', function($oHaste) {
            return Input::post('FORM_SUBMIT') === $oHaste->getFormId();
        });

        $this->oForm->bindModel(Collection::getCurrent());
        $this->oForm->setNoValidate(true);
        $this->addFields();

        if ($this->oForm->isSubmitted()) {
            $this->validateForm();
        }

        $this->Template->form = $this->oForm->generate();
    }

    protected function addFields()
    {
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
    }

    protected function validateForm()
    {
        if ($this->oForm->validate()) {
            Session::getInstance()->set('skipOilTypeForm', 1);

            Collection::updateCollection();

            Controller::reload();
        }
    }

}