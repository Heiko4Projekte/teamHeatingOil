<?php

/**
 * Table tl_calc_product_configuration
 */
$GLOBALS['TL_DCA']['tl_calc_product_configuration'] = array
(

    // Config
    'config' => array
    (
        'dataContainer' => 'Table',
        'enableVersioning' => true,
        'closed' => true,
        'sql' => array(
            'keys' => array(
                'id' => 'primary',
            ),
        ),
    ),

    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode' => 2,
            'fields' => array('title'),
            'flag' => 1,
        ),
        'label' => array
        (
            'fields' => array('title'),
        ),
        'global_operations' => array
        (
            'back' => array
            (
                'label' => &$GLOBALS['TL_LANG']['MSC']['backBT'],
                'href' => 'mod=&table=',
                'class' => 'header_back',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ),
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calc_product_configuration']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.gif',
            ),
            'delete' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calc_product_configuration']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
            ),
            'show' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calc_product_configuration']['show'],
                'href' => 'act=show',
                'icon' => 'show.gif',
            )
        )
    ),

    // Palettes
    'palettes' => array
    (
        'default' => '{title_legend},title;{config_legend},amount,shipping,payment,hose,oilType,tanker,uspField,labelGroup',
    ),

    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ),
        'tstamp' => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ),
        'sorting' => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'title' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_product_configuration']['title'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'amount' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_product_configuration']['amount'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => array('rgxp' => 'number', 'mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'shipping' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_product_configuration']['shipping'],
            'exclude' => true,
            'inputType' => 'select',
            'options' => $GLOBALS['TL_CALC']['shippingValues'],
            'eval' => array('mandatory' => true, 'tl_class' => 'w50', 'varioField' => true),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'payment' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_product_configuration']['payment'],
            'exclude' => true,
            'inputType' => 'select',
            'options' => $GLOBALS['TL_CALC']['paymentValues'],
            'eval' => array('mandatory' => true, 'tl_class' => 'w50', 'varioField' => true),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'oilType' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_product_configuration']['oilType'],
            'exclude' => true,
            'inputType' => 'select',
            'options' => $GLOBALS['TL_CALC']['oilTypeValues'],
            'eval' => array('mandatory' => true, 'tl_class' => 'w50', 'varioField' => true),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'hose' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_product_configuration']['hose'],
            'exclude' => true,
            'inputType' => 'select',
            'options' => $GLOBALS['TL_CALC']['hoseValues'],
            'eval' => array('mandatory' => true, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'tanker' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_product_configuration']['tanker'],
            'exclude' => true,
            'inputType' => 'select',
            'options' => $GLOBALS['TL_CALC']['tankerValues'],
            'eval' => array('mandatory' => true, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'uspField' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_product_configuration']['uspField'],
            'exclude' => true,
            'inputType' => 'select',
            'options_callback' => array('Slashworks\SwCalc\Models\ProductConfiguration', 'getUspFields'),
            'eval' => array('includeBlankOption' => true, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'labelGroup' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_product_configuration']['labelGroup'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        )
    )
);
