<?php

namespace Slashworks\Devmails\Classes;

use Slashworks\Devmails\Classes\Models\Devmails;

class Hooks {

    public function overrideMailRecipient($arrSubmitted, $arrLabels, $self, $arrFields)
    {
        $results = Devmails::getAllActiveRecipients();
        $recipients = '';
        foreach($results as $result) {
            if($result['isenabled'] == 1){
                $recipients[] .= $result['mailadresse'];
            }
        }
        if(!empty($recipients)) {
            $recipients = implode(',', $recipients);
            $self->__set('recipient', $recipients);
        }
    }

}
?>