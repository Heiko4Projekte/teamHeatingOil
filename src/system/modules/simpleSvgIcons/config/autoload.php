<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
    'Slashworks',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
    // Classes
    'Slashworks\SimpleSvgIcons\Classes\Helper'  => 'system/modules/simpleSvgIcons/classes/Helper.php',
    'Slashworks\SimpleSvgIcons\Classes\Hooks'   => 'system/modules/simpleSvgIcons/classes/Hooks.php',
));