<?php

namespace Slashworks\SwCalc\Models;

use Contao\Database;
use Contao\Model;

/**
 * Class VarioValue
 * @package Slashworks\SwCalc\Models
 */
class VarioValue extends Model
{

  /**
   * Table name.
   *
   * @var string
   */
  protected static $strTable = 'tl_calc_variovalues';

  /**
   * Determine, if a value of a field is configured to be a vario value.
   *
   * @param $sFieldName
   * @param $sValue
   *
   * @return bool
   */
  public static function isVarioValue($sFieldName, $sValue)
  {
    $t = static::$strTable;
    $oDb = Database::getInstance();
    $sQuery = "SELECT count(*) as total FROM $t WHERE varioField=? AND varioValue=?";

    return ($oDb->prepare($sQuery)->execute($sFieldName, $sValue)->total > 0);
  }

}