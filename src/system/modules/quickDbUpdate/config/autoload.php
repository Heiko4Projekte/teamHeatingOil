<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package QuickDbUpdate
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'slashworks',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'slashworks\QuickDbUpdate' => 'system/modules/quickDbUpdate/classes/QuickDbUpdate.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'be_quickDbUpdate' => 'system/modules/quickDbUpdate/templates/backend',
));
