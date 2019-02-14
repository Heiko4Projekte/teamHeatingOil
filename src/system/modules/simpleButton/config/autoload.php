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
    'Slashworks\SimpleButton\Classes\Helper'   => 'system/modules/simpleButton/classes/Helper.php',
    'Slashworks\SimpleButton\Classes\Hooks'   => 'system/modules/simpleButton/classes/Hooks.php',

    // Elements

    // Models

    // Modules
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
    'ce_hyperlink' => 'system/modules/simpleButton/templates/elements'
));
