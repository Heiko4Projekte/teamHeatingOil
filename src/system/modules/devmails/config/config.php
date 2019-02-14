<?php

/**
 * Back end modules
 */

$GLOBALS['BE_MOD']['content']['Devmails'] = array('tables' => array('tl_devmails'), 'icon'   => '/system/modules/devmails/assets/icon.gif');
$GLOBALS['TL_HOOKS']['prepareFormData'][] = array('Slashworks\Devmails\Classes\Hooks', 'overrideMailRecipient');

?>