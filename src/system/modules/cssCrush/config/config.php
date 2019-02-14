<?php
/**
 * CssCrush for Contao Open Source CMS
 *
 * Copyright (C) 2013 Joe Ray Gregory
 *
 * @package Slashworks\CssCrush
 * @link    http://borowiakziehe.de
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

$GLOBALS['TL_HOOKS']['generatePage'][] = array('Slashworks\CssCrush\CssCrushLoader', 'loadCSSCrush');