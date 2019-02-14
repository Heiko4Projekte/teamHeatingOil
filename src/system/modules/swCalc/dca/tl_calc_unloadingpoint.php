<?php

\Contao\Controller::loadLanguageFile('tl_calc_collection');
\Contao\Controller::loadLanguageFile('tl_calc_unloadingpoint');

/**
 * Table tl_calc_unloadingpoint
 */
$GLOBALS['TL_DCA']['tl_calc_unloadingpoint'] = array
(

    // Config
    'config' => array
    (
        'dataContainer' => 'Table',
        'ptable'        => 'tl_calc_collection',
        'sql' => array(
            'keys' => array(
                'id' => 'primary',
                'pid' => 'index'
            ),
        )
    ),

    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ),
        'pid' => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'tstamp' => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ),
        'ptable' => array
        (
            'sql' => "varchar(64) NOT NULL default ''"
        ),
        'unloadingpointorder' => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'shippingSalutation' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['shippingSalutation'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => array('male', 'female'),
            'reference' => &$GLOBALS['TL_LANG']['tl_calc_collection']['salutation'],
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('unloadingpointsForm'), 'class' => 'widget-shippingSalutation', 'unloadingField' => true),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'shippingFirstname' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['shippingFirstname'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('unloadingpointsForm'), 'class' => 'widget-shippingFirstname', 'unloadingField' => true),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'shippingLastname' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['shippingLastname'],
            'exclude'   => true,
            'inputType' => 'text',
            'search' => true,
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('unloadingpointsForm'), 'class' => 'widget-shippingLastname', 'unloadingField' => true),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'shippingStreet' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['shippingStreet'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('unloadingpointsForm'), 'class' => 'widget-shippingStreet', 'placeholder' => 'Geben Sie Ihre Adresse ein.', 'autocomplete' => 'new-password', 'unloadingField' => true, 'autocompleterKey' => 'streetFieldId'),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'shippingPostal' => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_calc_collection']['shippingPostal'],
            'exclude'       => true,
            'inputType'     => 'text',
            'eval'          => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('unloadingpointsForm'), 'class' => 'widget-shippingPostal', 'unloadingField' => true, 'autocompleterKey' => 'postalFieldId'),
            'sql'           => "varchar(255) NOT NULL default ''",
        ),
        'shippingCity' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['shippingCity'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('unloadingpointsForm'), 'class' => 'widget-shippingCity', 'placeholder' => 'Geben Sie Ihre Stadt ein.', 'unloadingField' => true, 'autocompleterKey' => 'cityFieldId', 'autocomplete' => 'new-password'),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'phone' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_unloadingpoint']['phone'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255, 'tl_class' => 'w50', 'group' => array('unloadingpointsForm'), 'class' => 'widget-phone', 'unloadingField' => true),
            'sql'       => "varchar(64) NOT NULL default ''",
        ),
        'partialAmount' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['partialAmount'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('rgxp' => 'number', 'minval' => \Slashworks\SwCalc\Models\Pricing::getMinAmount(), 'mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('unloadingpointsForm'), 'class' => 'widget-partialAmount', 'unloadingField' => true),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'antifreeze' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['antifreeze'],
            'inputType' => 'select',
            'options'   => $GLOBALS['TL_CALC']['antifreezeValues'],
            'reference' => &$GLOBALS['TL_LANG']['tl_calc_collection']['antifreezeOptions'],
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('unloadingpointsForm'), 'class' => 'widget-antifreeze', 'unloadingField' => true, 'addPriceToLabel' => true),
            'sql'       => "varchar(32) NOT NULL default ''",
        )
    )
);