<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

\Contao\ClassLoader::addNamespace('Slashworks');


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
    // Classes
    'Slashworks\Devmails\Classes\Hooks'  => 'system/modules/devmails/classes/Hooks.php',
    //Models
    'Slashworks\Devmails\Classes\Models\Devmails'  => 'system/modules/devmails/classes/models/Devmails.php',
));
