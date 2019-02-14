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
    'Slashworks\Swfeatures\Classes\Helper'  => 'system/modules/swfeatures/classes/Helper.php',
    'Slashworks\Swfeatures\Classes\Hooks'   => 'system/modules/swfeatures/classes/Hooks.php',

    // Elements

    // Models

    // Modules
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
    'ce_accordion_start' => 'system/modules/swfeatures/templates/elements',

    'form'          => 'system/modules/swfeatures/templates/forms',
    'form_checkbox' => 'system/modules/swfeatures/templates/forms',
    'form_row'      => 'system/modules/swfeatures/templates/forms',
//    'form_select'   => 'system/modules/swfeatures/templates/forms',
    'form_submit'   => 'system/modules/swfeatures/templates/forms',

    'mod_article'   => 'system/modules/swfeatures/templates/modules'
));
