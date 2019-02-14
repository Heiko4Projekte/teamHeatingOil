<?php

namespace Slashworks\SwCalc\Classes;

use Contao\Controller;
use Haste\Form\Form;

/**
 * Haste form helper class to add fields of a specific group to a haste form object.
 *
 * Class FormGroups
 * @package Slashworks\SwCalc\Classes
 */
class FormGroups
{

    /**
     * @var
     */
    protected static $strName;


    /**
     * @param $oForm
     * @param $sAlias
     * @return mixed
     */
    public static function addWrapperStart($oForm, $sAlias)
    {
        $oForm->addFormField('wrapper-start-' . $sAlias, array
        (
            'inputType' => 'html',
            'eval' => array
            (
                'html' => '<div class="form-group form-group-' . $sAlias . '">'
            )
        ));

        return $oForm;
    }

    /**
     * @param $oForm
     * @param $sAlias
     * @param $headline
     * @return mixed
     */
    public static function addHeadline($oForm, $sAlias,$headline)
    {


        $oForm->addFormField('wrapper-start-' . $sAlias, array
        (
            'inputType' => 'html',
            'eval' => array
            (
                'html' => '<h2 class="form-headline form-group-headline">' . $headline . '</h2>'
            )
        ));

        return $oForm;
    }

    /**
     * @param $oForm
     * @param $sAlias
     * @return mixed
     */
    public static function addWrapperStop($oForm, $sAlias)
    {
        $oForm->addFormField('wrapper-stop-' . $sAlias, array
        (
            'inputType' => 'html',
            'eval' => array
            (
                'html' => '</div>'
            )
        ));

        return $oForm;
    }


    /**
     * @param Form $oForm
     * @param $strName
     * @param $strHeadline
     * @return mixed
     */
    public static function addFormGroupFields($oForm, $strName, $strHeadline)
    {
        static::$strName = $strName;

        if ($strHeadline !== '') {
            $oForm->addFormField('headline_' . $strName, array
            (
                'inputType' => 'explanation',
                'eval' => array
                (
                    'text' => $strHeadline,
                    'class' => 'form-group-headline'
                )
            ));
        }


        $oForm->addFieldsFromDca('tl_calc_collection', function (&$sField, &$aDca) {
            // make sure to skip elements without inputType or you will get an exception
            if (!isset($aDca['inputType'])) {
                return false;
            }

            // Skip fields that are not in the contact group.
            if (is_array($aDca['eval']['group'])) {
                if (!in_array(static::$strName, $aDca['eval']['group'])) {

                    return false;
                }

                if (!is_array($aDca['label'])) {
                    $aDca['label'] = $sField;
                }
            } else {
                return false;
            }

            // Add selected group as additional attribute, because a form field can have multiple groups assigned via dca, e. g.: 'group' => array('groupOne', 'groupTwo')
            $aDca['eval']['selectedGroup'] = static::$strName;

            // Add field name as CSS class.
            $aDca['eval']['class'] = 'widget-' . $sField;


            // We have to return true, otherwise the field will be skipped.
            return true;
        });


        return $oForm;
    }
}