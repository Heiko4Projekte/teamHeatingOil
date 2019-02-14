<?php

/**
 * Adding swfeatures to article
 */
$GLOBALS['TL_DCA']['tl_article']['palettes']['default'] = str_replace
(
    '{publish',
    '{swfeatures_legend},articleFullWidth;{publish',
    $GLOBALS['TL_DCA']['tl_article']['palettes']['default']
);

$GLOBALS['TL_DCA']['tl_article']['fields']['articleFullWidth'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_article']['articleFullWidth'],
    'inputType' => 'checkbox',
    'eval'      => array(),
    'sql'       => "char(1) NOT NULL default ''"
);