<?php

namespace Slashworks\SwCalc\Models;

use Contao\Database;
use Contao\Model;

class UnloadingPoint extends Model
{

    protected static $strTable = 'tl_calc_unloadingpoint';

    public static function findByPid($iPid)
    {
        $aOptions = array();
        $aOptions['column'] = array("pid = '$iPid'");
        $aOptions['order'] = 'unloadingpointorder ASC';

        return static::find($aOptions);
    }

    public static function findOneByPidParentAndOrder($iPid, $sParentTable, $sOrder)
    {
        $aOptions = array();
        $aOptions['column'] = array("pid = '$iPid'", "ptable = '$sParentTable'", "unloadingpointorder = '$sOrder'");

        return static::find($aOptions);
    }

    public static function deleteAllAddressesByPidAndParentTable($iPid, $sParentTable)
    {
        $t = static::$strTable;

        $oDb = Database::getInstance();
        $sQuery = "DELETE FROM $t WHERE pid = ? AND ptable = ?";

        $oDb->prepare($sQuery)->execute($iPid, $sParentTable);
    }

    public static function savedUnloadingPointsEqualSelectedValue()
    {
        $oCollection = Collection::getCurrent();

        $iSelectedUnloadingPoints = (int) $oCollection->unloadingPoints;

        $iPid = $oCollection->id;
        $sParentTable = 'tl_calc_collection';

        $aOptions['column'] = array("pid = '$iPid'", "ptable = '$sParentTable'");
        $iSavedUnloadingPoints = static::countBy(null, null, $aOptions);

        return ($iSelectedUnloadingPoints - 1 === $iSavedUnloadingPoints);
    }

    public static function getAntifreezeAmountsByPid($iPid)
    {
        $t = static::$strTable;
        $oCollection = Collection::getCurrent();

        $oDb = Database::getInstance();
        $sQuery = "SELECT SUM(antifreeze) AS antifreezeSum FROM $t WHERE pid='$iPid' AND ptable='tl_calc_collection'";

        $oResult = $oDb->prepare($sQuery)->execute();

        if ($oResult->numRows < 1) {
            return null;
        }

        return (int) $oResult->fetchAssoc()['antifreezeSum'];
    }

}