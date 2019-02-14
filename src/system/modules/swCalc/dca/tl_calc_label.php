<?php

/*
CREATE TABLE tx_teamlabel_domain_label (
    uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
        pid int(11) DEFAULT '0' NOT NULL,

        alias varchar(255) DEFAULT '' NOT NULL,
        value text NOT NULL,
        title varchar(255) DEFAULT '' NOT NULL,

        PRIMARY KEY (uid),
        KEY parent (pid)
);

*/


/**
 * Table tl_calc_configuration
 */
$GLOBALS['TL_DCA']['tl_calc_label'] = array
(

    // Config
    'config'   => array
    (
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'sql'              => array(
            'keys' => array(
                'id' => 'primary',
            ),
        ),
    ),

    // List
    'list'     => array
    (
        'sorting'           => array
        (
            'mode'        => 2,
            'fields'      => array('title'),
            'panelLayout' => 'filter;search,limit',
            'flag'        => 1,
        ),
        'label'             => array
        (
            'fields'      => array('title', 'alias'),
            'showColumns' => true,
        ),
        'global_operations' => array
        (
            'back' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['MSC']['backBT'],
                'href'       => 'mod=&table=',
                'class'      => 'header_back',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ),
        ),
        'operations'        => array
        (
            'edit'   => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calc_label']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ),
            'copy'   => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calc_label']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ),
            'delete' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_calc_label']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
            ),
        ),
    ),

    // Palettes
    'palettes' => array
    (
        'default' => '{title_legend},title,alias,value;',
    ),

    // Fields
    'fields'   => array
    (
        'id'      => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ),
        'tstamp'  => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ),
        'sorting' => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ),
        'title'   => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_label']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'alias'   => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_label']['alias'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'value'   => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_label']['value'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'textarea',
            'eval'      => array('mandatory' => true, 'preserveTags' => true, 'tl_class' => 'clr'),
            'sql'       => "mediumtext NULL",
        ),
    ),
);

