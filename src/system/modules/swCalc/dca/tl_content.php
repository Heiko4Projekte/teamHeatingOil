<?php

/**
 * New content element for checkout steps.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['checkoutsteps'] = '{type_legend},type;{steps_legend},activeCheckoutStep';

$GLOBALS['TL_DCA']['tl_content']['fields']['activeCheckoutStep'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_content']['activeCheckoutStep'],
    'exclude'   => true,
    'search'    => true,
    'inputType' => 'select',
    'options'   => $GLOBALS['TL_CALC']['checkoutSteps'],
    'reference' => &$GLOBALS['TL_LANG']['checkoutSteps'],
    'eval'      => array(),
    'sql'       => "varchar(255) NOT NULL default ''"
);