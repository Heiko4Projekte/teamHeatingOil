<?php

/**
 * Table tl_calc_variovalues
 */
$GLOBALS['TL_DCA']['tl_calc_variovalues'] = array
(

    // Config
    'config' => array
    (
        'dataContainer' => 'Table',
        'enableVersioning' => true,
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
            'mode' => 0,
            'fields' => array('varioField')
        ),
        'label' => array
        (
            'fields' => array('varioField', 'varioValue'),
            'format' => '%s: %s'
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
                'label' => &$GLOBALS['TL_LANG']['tl_calc_variovalues']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.gif',
            ),
            'delete' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calc_variovalues']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
            ),
            'show' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calc_variovalues']['show'],
                'href' => 'act=show',
                'icon' => 'show.gif',
            )
        )
    ),

    // Palettes
    'palettes' => array
    (
        'default' => '{title_legend},varioField,varioValue',
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
        'varioField' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_variovalues']['varioField'],
            'exclude' => true,
            'inputType' => 'select',
            'options_callback' => array('Slashworks\SwCalc\Models\Collection', 'getVarioFields'),
            'eval' => array('submitOnChange' => true, 'includeBlankOption' => true, 'mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'varioValue' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_variovalues']['varioValue'],
            'exclude' => true,
            'inputType' => 'select',
            'options_callback' => array('Slashworks\SwCalc\Models\Collection', 'getValuesByFieldForDca'),
            'eval' => array('tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        )
    )
);
