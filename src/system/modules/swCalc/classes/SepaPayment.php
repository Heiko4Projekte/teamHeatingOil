<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package   isotope_payment_sepa
 * @author    Michael Gruschwitz <info@grusch-it.de>
 * @license   LGPL
 * @copyright Michael Gruschwitz 2015-2017
 */

namespace Gruschit;

/**
 * SEPA Payment Module.
 *
 * Collects and persists bank account data.
 *
 * @package    isotope_payment_sepa
 * @author     Michael Gruschwitz <info@grusch-it.de>
 * @copyright  Michael Gruschwitz 2015-2017
 * @see        http://stackoverflow.com/questions/20983339/validate-iban-php#20983340
 */
class SepaPayment
{

    /**
     * @param string $strRaw
     * @return string
     */
    public static function normalizeIBAN($strRaw)
    {
        return strtoupper(preg_replace('/[^\da-z]/i', '', $strRaw));
    }

    /**
     * Retrieve masked IBAN code
     *
     * The country code and the first 4 and last 4 digits will be preserved.
     * The rest will be replaced by $strChar letter.
     *
     * @param string $strRaw
     * @param string $strChar
     * @return string
     */
    public static function maskIBAN($strRaw, $strChar = 'X')
    {
        $normalized = self::normalizeIBAN($strRaw);
        $cut = preg_replace('/^([a-z]{2}[0-9]{4})([0-9]+)([0-9]{4})$/i', '\1\3', $normalized);

        $first = substr($cut, 0, 6);
        $middle = str_repeat($strChar, max(0, strlen($normalized) - 10));
        $last = substr($cut, 6);

        return $first . $middle . $last;
    }
}
