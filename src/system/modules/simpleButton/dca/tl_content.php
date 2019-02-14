<?php

/**
 * Add button-options for hyperlink element.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'isButton';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['isButton'] = 'buttonStyle';
$GLOBALS['TL_DCA']['tl_content']['palettes']['hyperlink'] = str_replace
(
    '{template_legend',
    '{button_legend},isButton;{template_legend',
    $GLOBALS['TL_DCA']['tl_content']['palettes']['hyperlink']
);

$GLOBALS['TL_DCA']['tl_content']['fields']['isButton'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_content']['isButton'],
    'inputType' => 'checkbox',
    'eval'      => array('submitOnChange'=>true),
    'sql'       => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['buttonStyle'] = array
(
    'label'            => &$GLOBALS['TL_LANG']['tl_content']['buttonStyle'],
    'inputType'        => 'select',
    'options_callback' => array('Slashworks\SimpleButton\Classes\Helper', 'getButtonStyles'),
    'reference'        => &$GLOBALS['TL_LANG']['MSC'],
    'eval'             => array('tl_class' => 'w50'),
    'sql'              => "varchar(32) NOT NULL default ''"
);