<?php

namespace Slashworks\SimpleButton\Classes;

class Helper
{

    /**
     * Return a list of button styles, that are defined in files/theme/<theme-name>/modules/button.css and files/theme/<theme-name>/config/variables.css.
     *
     * @return array
     */
    public static function getButtonStyles()
    {
        return array
        (
            'button-standard',
            'button-primary',
            'button-secondary',
            'button-cta'
        );
    }

}