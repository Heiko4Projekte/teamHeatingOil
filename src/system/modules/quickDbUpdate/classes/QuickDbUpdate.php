<?php
/**
 * Created by PhpStorm.
 * User: jrgregory
 * Date: 27.06.14 | KW 26
 * Time: 15:57
 */

namespace slashworks;


use Contao\Backend;
use Contao\BackendTemplate;

class QuickDbUpdate extends Backend {

    public function insertQudbButton($strContent, $strTemplate)
    {

        if ($strTemplate == 'be_main') {
            $strContent = str_replace
            (
                '</body>',
                $this->setOuptput().'</body>',
                $strContent
            );

        }

        return $strContent;

    }

    private function setOuptput()
    {

        $tpl = new BackendTemplate('be_quickDbUpdate');

        return $tpl->parse();

    }

} 
