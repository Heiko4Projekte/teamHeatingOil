<?php

namespace Slashworks\SwCalc\Helper;

use Contao\Controller;
use Contao\FilesModel;
use Slashworks\SwCalc\Models\Configuration;

class LabelHelper
{

    public static function getAgbLabel()
    {
        $oConfiguration = Configuration::getActive();
        $sAgbPart = 'AGB';
        $sCancellationPolicyPart = 'Widerrufsbelehrung';

        if ($oConfiguration->agbFile) {
            $oFile = FilesModel::findByUuid($oConfiguration->agbFile);

            if ($oFile !== null) {
                $sAgbPart = '<a href="' . $oFile->path . '" target="_blank">' . $sAgbPart . '</a>';
            }
        }

        if ($oConfiguration->cancellationPolicyFile) {
            $oFile = FilesModel::findByUuid($oConfiguration->cancellationPolicyFile);

            if ($oFile !== null) {
                $sCancellationPolicyPart = '<a href="' . $oFile->path . '" target="_blank">' . $sCancellationPolicyPart . '</a>';
            }
        }

        $sReturn = 'Ich erkenne die ' . $sAgbPart . ' an und habe die ' . $sCancellationPolicyPart . ' gelesen.';

        return $sReturn;
    }

}
