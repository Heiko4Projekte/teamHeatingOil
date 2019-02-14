<?php

namespace Slashworks\SwCalc\Hooks;

use Slashworks\SwCalc\Models\Collection;


/**
 * Class DailyCron
 *
 * @package Slashworks\SwCalc\Hooks
 */
class DailyCron
{

    /**
     *
     */
    public function removeOldSession()
    {

        $time = time() - 86400;

        $objCollection = Collection::findAll(array('column' => array('tstamp < ' . $time, 'type = "cart"')));

        while ($objCollection->next()) {
            $objCollection->delete();
        }

    }

}