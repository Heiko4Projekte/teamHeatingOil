<?php

/**
 * Add grid functionality (grid columns, float reset), custom error messages and form field icon.
 */
foreach ($GLOBALS['TL_DCA']['tl_form_field']['palettes'] as $sPaletteName => $mPalette) {
    if (!is_array($mPalette)) {
        $GLOBALS['TL_DCA']['tl_form_field']['palettes'][$sPaletteName] = str_replace
        (
            '{template_legend',
            '{icon_legend},formFieldIcon;{widthSettings_legend},gridColumns,gridClear;{errorSettings_legend},customErrorMessage;{template_legend',
            $GLOBALS['TL_DCA']['tl_form_field']['palettes'][$sPaletteName]
        );
    }
}

$GLOBALS['TL_DCA']['tl_form_field']['fields']['gridColumns'] = array
(
    'label'            => &$GLOBALS['TL_LANG']['MSC']['gridColumns'],
    'inputType'        => 'select',
    'options_callback' => array('Slashworks\Swfeatures\Classes\Helper', 'getGridColumns'),
    'reference'        => &$GLOBALS['TL_LANG']['MSC'],
    'eval'             => array('includeBlankOption' => true, 'tl_class' => 'w50'),
    'sql'              => "varchar(32) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_form_field']['fields']['gridClear'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['MSC']['gridClear'],
    'inputType' => 'checkbox',
    'eval'      => array('tl_class' => 'w50 m12'),
    'sql'       => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_form_field']['fields']['customErrorMessage'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_form_field']['customErrorMessage'],
    'inputType' => 'text',
    'eval'      => array('maxlength' => 255, 'tl_class' => 'long'),
    'sql'       => "varchar(255) NOT NULL default ''"
);