<?php

/**
 * Add grid related selections to content elements
 */
foreach ($GLOBALS['TL_DCA']['tl_content']['palettes'] as $mPaletteName => $sPalette) {
    if (!is_array($sPalette)) {
        $GLOBALS['TL_DCA']['tl_content']['palettes'][$mPaletteName] = str_replace
        (
            '{invisible_',
            '{swfeatures_legend},gridColumns,gridClear,gridExclude;{invisible_',
            $GLOBALS['TL_DCA']['tl_content']['palettes'][$mPaletteName]
        );
    }
}

$GLOBALS['TL_DCA']['tl_content']['fields']['gridColumns'] = array
(
    'label'            => &$GLOBALS['TL_LANG']['MSC']['gridColumns'],
    'inputType'        => 'select',
    'options_callback' => array('Slashworks\Swfeatures\Classes\Helper', 'getGridColumns'),
    'reference'        => &$GLOBALS['TL_LANG']['MSC'],
    'eval'             => array('includeBlankOption' => true, 'tl_class' => 'w50'),
    'sql'              => "varchar(32) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['gridClear'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['MSC']['gridClear'],
    'inputType' => 'checkbox',
    'eval'      => array('tl_class' => 'w50 m12'),
    'sql'       => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['gridExclude'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['MSC']['gridExclude'],
    'inputType' => 'checkbox',
    'eval'      => array('tl_class' => 'clr w50 m12'),
    'sql'       => "char(1) NOT NULL default ''"
);