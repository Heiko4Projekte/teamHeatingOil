<?php

if(TL_MODE === 'BE') {

    $GLOBALS['TL_CSS'][] = 'system/modules/quickDbUpdate/assets/css/style.css';

}

$GLOBALS['TL_HOOKS']['parseBackendTemplate'][] = array('slashworks\QuickDbUpdate', 'insertQudbButton');