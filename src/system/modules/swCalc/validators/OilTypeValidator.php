<?php

namespace Slashworks\SwCalc\Validators;

use Contao\Input;
use Contao\Module;
use Contao\ModuleModel;
use Haste\Form\Form;
use Slashworks\SwCalc\Helper\UnloadingPointsFormCreator;
use Slashworks\SwCalc\Models\UnloadingPoint;
use Slashworks\SwCalc\Modules\UnloadingPointsForm;

/**
 * Class PaymentValidator
 * @package Slashworks\SwCalc\Classes
 */
class OilTypeValidator extends Validator
{

    /**
     * @return mixed
     */
    protected function checkValidation()
    {
        if ($this->collection->unloadingPoints == 1) {
            // Delete needless unloading point entry.
            UnloadingPoint::deleteAllAddressesByPidAndParentTable($this->collection->id, 'tl_calc_collection');
            // Set valid status if only 1 unloading point has been selected.
            $this->setValidStatus('true');
        } else if (!UnloadingPoint::savedUnloadingPointsEqualSelectedValue()) {
            // Selected unloading points differ from saved ones.
            $this->generateUnloadingPointsForm();
        } else {
            $this->setValidStatus('true');
        }

    }

    protected function generateUnloadingPointsForm()
    {
        $oUnloadingPointsForm = new UnloadingPointsFormCreator();
        $this->oForm = $oUnloadingPointsForm->create();
        $this->oForm->setFormActionFromUri('ajaxcontroller.html?action=setUnloadingPoints');
        $this->oForm->setNoValidate(false);

        $this->data = $this->oForm;
    }

}