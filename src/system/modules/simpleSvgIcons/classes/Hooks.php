<?php

namespace Slashworks\SimpleSvgIcons\Classes;

use Contao\Frontend;

class Hooks extends Frontend {

    /**
     * Custom replaceInsertTags Hook to recognize insert tag for SVG icons.
     *
     * @param $sTag
     * @return bool|string
     */
    public function swReplaceInsertTags($sTag)
    {
        // Get all selected SVG files.
        $aSvgFiles = Helper::getSvgIconFiles();

        // Return if there are no selected SVG files.
        if (empty($aSvgFiles)) {
            return false;
        }

        $aSplit = explode('::', $sTag);
        $sCssClass = 'svg-icon';

        if ($aSplit[0] === 'svg') {

            if (isset($aSplit[1])) {

                foreach ($aSvgFiles as $aSvgFile) {

                    // If the second identifier of the insert tag is in a symbols array of a file, replace insert tag.
                    if (in_array($aSplit[1], $aSvgFile['symbols'])) {
                        // Add svg4everybody library
                        $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/simpleSvgIcons/vendor/svg4everybody/svg4everybody.min.js||static';
                        // Place svg4everybody-call in the footer.
                        $GLOBALS['TL_JQUERY'][] = '<script>svg4everybody();</script>';

                        if (isset($aSplit[2])) {
                            $sCssClass .= ' ' . $aSplit[2];
                        }

                        $sSvg = '<svg class="' . $sCssClass . '"><use xlink:href="' . $aSvgFile['path'] . '#' . $aSplit[1] . '"></use></svg>';
                        return $sSvg;
                    }

                }

            }

        }

        return false;
    }


    /**
     * Replace link title of hyperlink elements when using svg insert tags.
     *
     * @param $oTemplate
     */
    public function swParseTemplate($oTemplate)
    {
        if (strpos($oTemplate->getName(), 'ce_hyperlink') !== false) {
            if (strpos($oTemplate->linkTitle, '{{svg::') !== false) {
                $oTemplate->linkTitle = $oTemplate->href;
            }
        }
    }

}