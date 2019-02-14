<?php

$GLOBALS['TL_DCA']['tl_form_field']['palettes']['submit'] = str_replace
(
    '{template_legend',
    '{button_legend},buttonStyle;{template_legend',
    $GLOBALS['TL_DCA']['tl_form_field']['palettes']['submit']
);

$GLOBALS['TL_DCA']['tl_form_field']['fields']['buttonStyle'] = array
(
    'label'            => &$GLOBALS['TL_LANG']['tl_form_field']['buttonStyle'],
    'inputType'        => 'select',
    'options_callback' => array('Slashworks\SimpleButton\Classes\Helper', 'getButtonStyles'),
    'reference'        => &$GLOBALS['TL_LANG']['MSC'],
    'eval'             => array('tl_class' => 'w50'),
    'sql'              => "varchar(32) NOT NULL default ''"
);