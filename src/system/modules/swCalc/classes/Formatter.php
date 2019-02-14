<?php

namespace Slashworks\SwCalc\Classes;

/**
 * Used to format prices and surcharges.
 *
 * Class Formatter
 * @package Slashworks\SwCalc\Classes
 */
class Formatter
{

  /**
   * Round a price with 2 decimal places.
   *
   * @param $fPrice
   *
   * @return float
   */
  public static function roundPrice($fPrice)
  {
    return round($fPrice, 2);
  }

  /**
   * Format price to german notation.
   *
   * @param $fPrice
   *
   * @return string
   */
  public static function formatPrice($fPrice)
  {
    $fPrice = static::roundPrice($fPrice);

    return number_format($fPrice, 2, ',', '.');
  }

  /**
   * Add currency label to formatted price.
   *
   * @param $fPrice
   *
   * @return string
   */
  public static function formatPriceWithCurrency($fPrice)
  {
    $sPrice = static::formatPrice($fPrice);

    return $sPrice . '&thinsp;€';
  }

    /**
     * Add currency label to formatted price.
     *
     * @param $fPrice
     *
     * @return string
     */
    public static function formatPriceWithCurrencyAndSign($fPrice)
    {
        $sPrice = static::formatPriceWithCurrency($fPrice);

        if($fPrice > 0){
            $sPrice = '+' .$sPrice;
        }

        return $sPrice;
    }


  public static function formatPricePer100WithCurrency($fPrice)
  {
      $sPrice = static::formatPriceWithCurrency($fPrice);

      if ($fPrice > 0) {
          $sPrice = '+' . $sPrice;
      }

      $sPrice .= ' / 100 Liter';

      return $sPrice;
  }


  public static function formatNumber($int){

      return number_format($int, 0, ',', '.');

  }


}