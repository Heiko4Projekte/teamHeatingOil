<?php

namespace Slashworks\SwCalc\Modules;

use Contao\BackendTemplate;
use Contao\Controller;
use Contao\Input;
use Contao\Module;
use Contao\PageModel;
use Contao\Widget;
use Haste\Form\Form;
use Slashworks\SwCalc\Helper\UnloadingPointsFormCreator;
use Slashworks\SwCalc\Models\Collection;
use Slashworks\SwCalc\Models\UnloadingPoint;

class UnloadingPointsForm extends Module
{

    /**
     * The template name.
     *
     * @var string
     */
    protected $strTemplate = 'mod_calc_unloadingpointsform';

    /**
     * The haste form object.
     *
     * @var Form
     */
    protected $oForm;

    /**
     * @var array
     */
    protected $aUnloadingPointFields;

    /**
     * @var Collection
     */
    protected $oCollection;

    /**
     * @var int
     */
    protected $iUnloadingPoints;

    /**
     * @var string
     */
    protected $sParentTable = 'tl_calc_collection';

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

        $this->oCollection = Collection::getCurrent();
        $this->iUnloadingPoints = (int) $this->oCollection->unloadingPoints;

        if ($this->iUnloadingPoints <= 1) {
            $this->redirectToNextStep();
        }

        return parent::generate();
    }

    /**
     * Parse the template.
     */
    protected function compile()
    {
        Controller::loadLanguageFile('tl_calc_collection');

        $this->hl = 'h1';
        $this->headline = $GLOBALS['TL_LANG']['unloadingPointsForm']['headlineDefault'];

        $this->generateMainInformation();

        $oUnloadingPointsForm = new UnloadingPointsFormCreator();
        $this->oForm = $oUnloadingPointsForm->create();

        // Validate form if it has been submitted.
        if ($this->oForm->isSubmitted()) {
            $this->validateForm();
        }

        // Generate form string.
        $this->Template->form = $this->oForm->generate('form_unloadingpoints');
        $this->Template->globalErrors = $this->aGlobalErrors;
    }

    /**
     * Get information of main address.
     */
    protected function generateMainInformation()
    {

    }

    protected function validateForm()
    {
        if ($this->oForm->validate()) {

            $this->oCollection->antifreeze = $this->oForm->getWidget('antifreeze')->value;
            $this->oCollection::updateCollection();

            // Delete all old entries
            UnloadingPoint::deleteAllAddressesByPidAndParentTable($this->oCollection->id, $this->sParentTable);

            $aUnloadingPoints = array();
            // Get entered partial amount for main address.
            $iMainPartialAmount = $this->oForm->fetch('partialAmount');
            $iCombinedAmount = $iMainPartialAmount;

            $this->oCollection->partialAmount = $iMainPartialAmount;
            $this->oCollection->save();

            $iOriginalAntifreezeAmount = $this->oCollection->getAntifreeze();

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
                $oUnloadingPoint->pid = $this->oCollection->id;
                $oUnloadingPoint->tstamp = time();
                $oUnloadingPoint->ptable = 'tl_calc_collection';
                $oUnloadingPoint->unloadingpointorder = $iOrder;

                /** @var Widget $oWidget */
                foreach ($aUnloadingPoint as $oWidget) {
                    // Store values from form submission in unloadingPoint
                    $oUnloadingPoint->{$oWidget->originalField} = $this->oForm->fetch($oWidget->name);

                    if ($oWidget->originalField === 'partialAmount') {
                        $iCombinedAmount += (int) $this->oForm->fetch($oWidget->name);
                    }
                }

                $oUnloadingPoint->save();

            }

            $iNewAntifreezeAmount = $this->oCollection->getAntifreeze();

            /**
             * Update collection if combined amount differs from originally entered amount
             * OR
             * antifreeze amount has changed
             */
            if ($iCombinedAmount != $this->oCollection->getAmount() || $iNewAntifreezeAmount != $iOriginalAntifreezeAmount) {
                $this->oCollection->amount = $iCombinedAmount;
                Collection::updateCollection(true);
            }

            $this->redirectToNextStep();
        }

        // Group errors.
        /** @var Widget $oWidget */
        foreach ($this->oForm->getWidgets() as $oWidget) {
            if ($oWidget->hasErrors()) {

                $this->aGlobalErrors[] = array
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