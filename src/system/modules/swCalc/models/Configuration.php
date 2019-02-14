<?php

namespace Slashworks\SwCalc\Models;

use Contao\Model;

/**
 * Class Configuration
 * @package Slashworks\SwCalc\Models
 */
class Configuration extends Model
{

  /**
   * The table name.
   *
   * @var string
   */
  protected static $strTable = 'tl_calc_configuration';

  /**
   * Get active configuration.
   *
   * @return mixed
   */
  public static function getActive()
  {
    return static::findOneBy('active', 1);
  }

}