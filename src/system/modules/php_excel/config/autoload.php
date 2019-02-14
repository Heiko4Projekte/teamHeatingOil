<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'PHPExcel'                   => 'system/modules/php_excel/vendor/PHPExcel.php',
	'PHPExcel_Writer_Excel2007'  => 'system/modules/php_excel/vendor/PHPExcel/Writer/Excel2007.php',
));
