<?php


/**
 * Table tl_devmails
 */
$GLOBALS['TL_DCA']['tl_devmails'] = array
(

    // Config
    'config'   => array
    (
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'sql'              => array
        (
            'keys' => array
            (
                'id' => 'primary'
            )
        ),
    ),
// List
    'list'     => array
    (
        'sorting'           => array
        (
            'mode'        => 2,
            'fields'      => array('mailadresse'),
            'flag'        => 1,
            'panelLayout' => 'filter;sort,search,limit'
        ),
        'label'             => array
        (
            'fields' => array('mailadresse'),
            'format' => '%s',
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations'        => array
        (
            'edit'   => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_devmails']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif'
            ),
            'delete' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_devmails']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show'   => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_devmails']['show'],
                'href'       => 'act=show',
                'icon'       => 'show.gif',
                'attributes' => 'style="margin-right:3px"'
            ),
        )
    ),
// Palettes
    'palettes' => array
    (
        'default'       => '{title_legend},mailadresse,isenabled'
    ),
// Fields
    'fields'   => array
    (
        'id'     => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp' => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'mailadresse'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_devmails']['mailadresse'],
            'inputType' => 'text',
            'exclude'   => true,
            'sorting'   => true,
            'flag'      => 1,
            'search'    => true,
            'eval'      => array(
                'mandatory'   => true,
                'unique'         => true,
                'maxlength'   => 255,
                'tl_class'        => 'w50',

            ),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'isenabled' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_devmails']['isenabled'],
            'exclude'                 => true,
            'inputType'               => 'checkbox',
            'eval'                    => array('tl_class'=>'w50 m12'),
            'sql'                     => "char(1) NOT NULL default ''"
        )
    )
);