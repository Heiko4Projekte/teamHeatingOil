<?php

namespace Slashworks\SimpleSvgIcons\Classes;

use Contao\File;
use Contao\Files;
use Contao\FilesModel;
use Contao\LayoutModel;
use Contao\ThemeModel;

class Helper
{

    /**
     * Get all SVG files that were selected in the active theme.
     *
     * @return array
     */
    public static function getSvgIconFiles()
    {
        global $objPage;
        $aSvgSymbols = array();

        // Get layout for current page
        $iLayoutId = $objPage->layout;
        $oLayout = LayoutModel::findById($iLayoutId);

        if (null !== $oLayout) {
            // Get the theme, that the current layout is using.
            $oTheme = ThemeModel::findById($oLayout->pid);

            if (null !== $oTheme) {
                // Get SVG files selected in the theme.
                $aFiles = deserialize($oTheme->iconFiles);

                if (!empty($aFiles) && isset($aFiles[0])) {
                    foreach ($aFiles as $sFileHash) {

                        // Get object file.
                        $oFile = FilesModel::findByUuid($sFileHash);
                        if (file_exists($oFile->path)) {
                            if ($oFile->extension === 'svg') {
                                // Generate symbol array for SVG file.
                                $aSvgSymbols[] = self::getSvgSymbolsFromFile($oFile);
                            }
                        }
                    }
                }
            }
        }

        return $aSvgSymbols;
    }


    /**
     * Generate an array of symbols that are used in the SVG file.
     *
     * @param $oFile
     * @return array
     */
    public static function getSvgSymbolsFromFile($oFile)
    {
        $sFilePath = $oFile->path;
        $aSymbolIds = array();

        // Iterate over each <symbol>-Tag in the SVG file an get the value of the id-attribute.
        $xml = simplexml_load_file($sFilePath);
        foreach ($xml->symbol as $symbol) {
            $aSymbolIds[] = (string) $symbol->attributes()->id;
        }

        return array
        (
            'path' => $sFilePath,
            'symbols' => $aSymbolIds
        );
    }

}