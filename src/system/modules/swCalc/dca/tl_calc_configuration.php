<?php

/**
 * Table tl_calc_configuration
 */
$GLOBALS['TL_DCA']['tl_calc_configuration'] = array
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
                'label' => &$GLOBALS['TL_LANG']['tl_calc_configuration']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.gif',
            ),
            'delete' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calc_configuration']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
            ),
            'update' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calc_configuration']['update'],
                'href' => 'key=update',
                'icon' => 'reload.gif',
            )
        )
    ),

    // Palettes
    'palettes' => array
    (
        'default' => '{title_legend},title,active,redirectTo;{pricing_legend},fallbackPostal,pricingTable,google_id,ga_conversion_tracking_order,minAmount,maxAmount;{documents_legend},agbFile,cancellationPolicyFile;{contact_legend},email;{email_legend},emailFrom,emailFromName,emailSubject,emailAdditionalRecipients,emailTemplate',
    ),

    // Fields
    'fields' => array
    (
        'id'             => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ),
        'tstamp'         => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ),
        'sorting'        => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'title'          => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_configuration']['title'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'active'         => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_configuration']['active'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => array('submitOnChange' => true, 'unique' => true, 'tl_class' => 'w50 m12'),
            'sql' => "char(1) NOT NULL default ''"
        ),
        'fallbackPostal' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_configuration']['fallbackPostal'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('tl_class' => 'w50', 'mandatory' => true),
            'sql'       => "varchar(5) NOT NULL default ''",
        ),
        'pricingTable'   => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_calc_configuration']['pricingTable'],
            'exclude'       => true,
            'inputType'     => 'fileTree',
            'eval'          => array(
                'filesOnly'  => true,
                'extensions' => 'csv',
                'path'       => 'files/pricingtable',
                'fieldType'  => 'radio',
                'mandatory'  => true,
                'tl_class'   => 'w50 autoheight',
            ),
            'save_callback' => array
            (
                array('Slashworks\SwCalc\Classes\PricingTableImporter', 'importPricingTable')
            ),
            'sql'           => "binary(16) NULL"
        ),
        'redirectTo'     => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_configuration']['redirectTo'],
            'inputType'               => 'pageTree',
            'foreignKey'              => 'tl_page.title',
            'eval'                    => array('fieldType'=>'radio','tl_class'=>"clr",'mandatory' => true),
            'sql'                     => "int(10) unsigned NOT NULL default '0'",
        ),
        'agbFile' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_configuration']['agbFile'],
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => array('filesOnly' => true, 'fieldType' => 'radio', 'mandatory' => true, 'tl_class' => 'clr'),
            'sql'       => "binary(16) NULL"
        ),
        'cancellationPolicyFile' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_configuration']['cancellationPolicyFile'],
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => array('filesOnly' => true, 'fieldType' => 'radio', 'mandatory' => true, 'tl_class' => 'clr'),
            'sql'       => "binary(16) NULL"
        ),
        'minAmount' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_configuration']['minAmount'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => array('tl_class' => 'w50 m12','mandatory' => true),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'maxAmount' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_configuration']['maxAmount'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => array('tl_class' => 'w50 m12','mandatory' => true),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'email' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_configuration']['email'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => array('tl_class' => 'w50 m12'),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'emailFrom' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_configuration']['emailFrom'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => array('rgxp' => 'email', 'tl_class' => 'w50 m12'),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'emailFromName' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_configuration']['emailFromName'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => array('tl_class' => 'w50 m12'),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'emailSubject' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_configuration']['emailSubject'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => array('decodeEntities' => true, 'tl_class' => 'w50 m12'),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'emailAdditionalRecipients' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_configuration']['emailAdditionalRecipients'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => array('rgxp' => 'emails', 'tl_class' => 'w50 m12'),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'emailTemplate' => array
        (
            'label'            => &$GLOBALS['TL_LANG']['tl_calc_configuration']['emailTemplate'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => array('Slashworks\SwCalc\Classes\Configuration', 'getConfirmationTemplates'),
            'eval'             => array('tl_class'=>'w50'),
            'sql'              => "varchar(64) NOT NULL default ''"
        ),
        'google_id' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_configuration']['google_id'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('tl_class' => 'w50 m12'),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'ga_conversion_tracking_order' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_configuration']['ga_conversion_tracking_order'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => array('tl_class' => 'w50 m12'),
            'sql'       => "char(1) NOT NULL default ''",
        ),
    )
);
