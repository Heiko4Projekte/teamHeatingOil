<?php

namespace Slashworks\Devmails\Classes\Models;

use Contao\Database;
use Contao\Model;

class Devmails extends Model {


    /**
     * @return mixed
     */
    public static function getAllActiveRecipients()
    {
        $query = 'select mailadresse, isenabled from tl_devmails';
        $odb = Database::getInstance();
        $result = $odb->prepare($query)->execute();
        if ($result != null) {
            $aresult = $result->fetchAllAssoc();
        }
        return $aresult;
    }

    /**
     * @return bool
     */
    public static function isEnabledDevmail() {
        $query = 'select count(*) from tl_devmails where isenabled = 1';
        $odb = Database::getInstance();
        $result = $odb->prepare($query)->execute();

        $aresult = $result->fetchRow();

        if($aresult[0] == true) {
            return true;
        } else {
            return false;
        }
    }


}