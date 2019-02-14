<?php

// Front end modules


// Content elements


// Hooks
if (TL_MODE === 'FE') {
//    $GLOBALS['TL_HOOKS']['getContentElement'][] = array('Slashworks\Swfeatures\Classes\Hooks', 'addGridWrapperToContentElements');
}


if (TL_MODE === 'FE') {
    // Add global app.js
    $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/swfeatures/assets/js/app.js|static';
}