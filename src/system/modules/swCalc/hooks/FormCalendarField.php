<?php

namespace Slashworks\SwCalc\Hooks;

use Contao\Controller;
use Contao\Date;

class FormCalendarField
{

    public function customizeBirthdayField($aConfig, $oFormCalendarField)
    {
        $aConfig['nextText'] = Controller::replaceInsertTags('{{svg::chevron-right}}');
        $aConfig['prevText'] = Controller::replaceInsertTags('{{svg::chevron-left}}');
        $aConfig['yearRange'] = '1920:' . Date::parse('Y', time());

        return $aConfig;
    }

}