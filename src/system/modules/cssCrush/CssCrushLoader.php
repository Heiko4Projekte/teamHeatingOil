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

namespace Slashworks\CssCrush;

/**
 * Process CSS-Crush file and options.
 *
 * Class CssCrushLoader
 * @package Slashworks\CssCrush
 */
class CssCrushLoader extends \Frontend
{

    static $oCssCrushFile;
    static $aOptions;
    static $cssCrushCtoCombiner;
    /**
     * Hook into generatePage and get CSS-Crush options from layout settings.
     *
     * @param              $objPage
     * @param              $objLayout
     * @param \PageRegular $objPageRegular
     *
     * @return string
     */
    public function loadCSSCrush($objPage, $objLayout, \PageRegular $objPageRegular)
    {
        if (!empty($objLayout->cssCrushFile)) {

            // Get CSS file and check if file exists.
            CssCrushLoader::$oCssCrushFile = \FilesModel::findByPk($objLayout->cssCrushFile);
            CssCrushLoader::$cssCrushCtoCombiner = $objLayout->cssCrushCtoCombiner;

            if (CssCrushLoader::$oCssCrushFile === null || !is_file(TL_ROOT . '/' . CssCrushLoader::$oCssCrushFile->path)) {
                return '';
            }

            // Load cssCrush compiler.
            require_once 'system/modules/cssCrush/vendor/cssCrush/CssCrush.php';

            // Buffer the plugins.
            $plugins = self::generatePluginArrays($objLayout);

            // Generate options array. List of all possible CSS-Crush options: http://the-echoplex.net/csscrush/#api--options
            CssCrushLoader::$aOptions = array(
                'minify'      => ($objLayout->cssCrushMinify) ? true : false,
                'cache'       => ($objLayout->cssCrushCache) ? true : false,
                'versioning'  => ($objLayout->cssCrushVersioning) ? true : false,
                'output_dir'  => ($objLayout->cssCrushDirName) ? TL_ROOT . '/' . $objLayout->cssCrushDirName : false,
                'output_file' => ($objLayout->cssCrushFileName) ? $objLayout->cssCrushFileName : false,
                'context'     => ($objLayout->cssCrushContext) ? $objLayout->cssCrushContext : false,
                'source_map'  => ($objLayout->cssCrushSourceMap) ? true : false,
                'enable'      => $plugins['enabled'],
                'disable'     => $plugins['disabled']
            );

            // HOOK: Add custom options to CssCrushLoader
            if (isset($GLOBALS['TL_HOOKS']['addCssCrushOptions']) && is_array($GLOBALS['TL_HOOKS']['addCssCrushOptions']))
            {
                foreach ($GLOBALS['TL_HOOKS']['addCssCrushOptions'] as $callback)
                {
                    $this->import($callback[0]);
                    $aNewOptions = $this->{$callback[0]}->{$callback[1]}(self::$aOptions);
                    if (!empty($aNewOptions) && is_array($aNewOptions)) {
                        CssCrushLoader::$aOptions = array_merge(CssCrushLoader::$aOptions, $aNewOptions);
                    }
                }
            }

            if ($objLayout->cssCrushDocRoot) {
                $options['doc_root'] = $objLayout->cssCrushDocRoot;
            }

            // Generate the CSS file.
            CssCrushLoader::recompile(CssCrushLoader::$aOptions);
        }

        return null;
    }

    /**
     * (Re)Compile CSS file with given CSS-Crush options.
     *
     * @param $aOptions
     */
    public static function recompile($aOptions)
    {
        // Load CSS-Crush compiler.
        require_once 'system/modules/cssCrush/vendor/cssCrush/CssCrush.php';
        $strStatic = "";

        // Set filepath.
        $sPath = TL_ROOT . '/' . CssCrushLoader::$oCssCrushFile->path;

        // Crush the CSS file.
        $compiled_file = csscrush_file($sPath, CssCrushLoader::$aOptions);

        // Add static flag if the combine option is set.
        if (CssCrushLoader::$cssCrushCtoCombiner) {
            $strStatic = "||static";
        }

        // Add the crushed CSS file to the template.
        $GLOBALS['TL_CSS'][CssCrushLoader::$oCssCrushFile->hash] = $compiled_file . '?v=4' . $strStatic;
    }


    /**
     * Get all CSS-Crush plugins from plugins folder (vendor/cssCrush/plugins) and return them as array.
     *
     * @return array
     */
    public static function getPlugins()
    {
        $aPlugins = array();

        if ($rHandle = opendir(TL_ROOT . '/' . 'system/modules/cssCrush/vendor/cssCrush/plugins')) {

            while (false !== ($file = readdir($rHandle))) {
                if ($file != "." && $file != "..") {
                    $aPlugins[] = str_replace('.php', '', $file);
                }
            }
            closedir($rHandle);

        }

        return $aPlugins;
    }


    /**
     * Generate the enable disable plugins and return them as array
     *
     * @param $objLayout
     *
     * @return array
     */
    private function generatePluginArrays($objLayout)
    {
        $aActivePlugins = deserialize($objLayout->cssCrushPlugins);

        $aAllPlugins = self::getPlugins();
        if (is_array($aActivePlugins)) {
            $aDisabledPlugins = array_diff($aAllPlugins, $aActivePlugins);
        } else {
            $aDisabledPlugins = array();
        }

        return array
        (
            'enabled'  => $aActivePlugins,
            'disabled' => $aDisabledPlugins
        );
    }
}
