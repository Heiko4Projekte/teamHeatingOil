<?php

namespace Slashworks\SwCalc\Modules;

use Contao\BackendTemplate;
use Contao\Controller;
use Contao\Input;
use Contao\Module;
use Contao\ModuleModel;
use Contao\Session;
use Slashworks\SwCalc\Models\Collection;
use Slashworks\SwCalc\Validators\AppValidator;

class AddressStepController extends Module
{

    /**
     * The template file.
     *
     * @var string
     */
    protected $strTemplate = 'mod_calc_addressstepcontroller';

    /**
     * The rendered form.
     *
     * @var string
     */
    protected $sRenderedForm = '';

    public function generate()
    {
        if (TL_MODE === 'BE') {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['addressstepcontroller'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        else{
            //validate session and call
            $App = new AppValidator();
            $App->validateAll();
        }

        return parent::generate();
    }

    /**
     *
     */
    protected function compile()
    {
        if (Input::get('reset')) {
            /** @var \PageModel $objPage */
            global $objPage;

            self::removeSessionData();

            Controller::redirect($objPage->getFrontendUrl());
        }

        $this->selectModuleToShow();
        $this->Template->form = $this->sRenderedForm;
    }

    /**
     *
     */
    public static function removeSessionData(){
        Session::getInstance()->remove('skipOilTypeForm');
        Session::getInstance()->remove('skipZmzAccountForm');
    }

    /**
     *
     */
    protected function selectModuleToShow()
    {
        $oCollection = Collection::getCurrent();

        // Show oil type form only if the selected oil type is not economyOil and the session variable for successfully submitted oil type form is not set.
        if ($oCollection->getOilType() !== 'economyOil' && Session::getInstance()->get('skipOilTypeForm') != 1) {
            $this->generateOilTypeModule();
            return;
        }

        // Show zmz account form only if the selected payment option is the zmz account and the session variable for successfully submitted zmz account form is not set.
        if ($oCollection->getPayment() === 'zmzAccount' && Session::getInstance()->get('skipZmzAccountForm') != 1) {
            $this->generateZmzAccountModule();
            return;
        }

        $this->generateAddressForm();

    }

    /**
     *
     */
    protected function generateOilTypeModule()
    {
        $oModuleModel = new ModuleModel();
        $oModule = new OilTypeForm($oModuleModel);

        $this->sRenderedForm = $oModule->generate();

        // Add data to global collection data array for google tag manager
        global $objPage;
        if ($objPage->collectionData) {
            $collectionData = $objPage->collectionData;
            $collectionData = array_merge($collectionData, array('step' => 30));
        } else {
            $collectionData = array('step' => 30);
        }
        $objPage->collectionData = $collectionData;
    }

    /**
     *
     */
    protected function generateZmzAccountModule()
    {
        $oModuleModel = new ModuleModel();
        $oModule = new ZmzAccountForm($oModuleModel);

        $this->sRenderedForm = $oModule->generate();

        // Add data to global collection data array for google tag manager
        global $objPage;
        if ($objPage->collectionData) {
            $collectionData = $objPage->collectionData;
            $collectionData = array_merge($collectionData, array('step' => 40));
        } else {
            $collectionData = array('step' => 40);
        }
        $objPage->collectionData = $collectionData;
    }

    /**
     *
     */
    protected function generateAddressForm()
    {
        $oModuleModel = new ModuleModel();
        $oModule = new AddressForm($oModuleModel);
        $oModule->jumpTo = $this->jumpTo;

        $this->sRenderedForm = $oModule->generate();

        // Add data to global collection data array for google tag manager
        global $objPage;
        if ($objPage->collectionData) {
            $collectionData = $objPage->collectionData;
            $collectionData = array_merge($collectionData, array('step' => 50));
        } else {
            $collectionData = array('step' => 50);
        }
        $objPage->collectionData = $collectionData;
    }

}