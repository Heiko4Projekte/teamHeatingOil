<?php

// Hooks
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('Slashworks\SimpleSvgIcons\Classes\Hooks', 'swReplaceInsertTags');
$GLOBALS['TL_HOOKS']['parseTemplate'][]     = array('Slashworks\SimpleSvgIcons\Classes\Hooks', 'swParseTemplate');