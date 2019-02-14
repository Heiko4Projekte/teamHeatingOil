<?php

use Slashworks\SwCalc\Models\Label;


/**
 * backend
 */

$GLOBALS['TL_LANG']['MOD']['swcalc'] = 'team energie';
$GLOBALS['TL_LANG']['MOD']['orders'][0] = 'Bestellung';
$GLOBALS['TL_LANG']['MOD']['configuration'][0] = 'Konfigurationen';
$GLOBALS['TL_LANG']['MOD']['productconfiguration'][0] = 'Produkte';
$GLOBALS['TL_LANG']['MOD']['variovalues'][0] = 'Felddefinitionen';
$GLOBALS['TL_LANG']['MOD']['sw_calc_labels'][0] = 'Labels';


// Replace all "!" with "." in error messages.
$aErrorLabels = array('general', 'unique', 'mandatory', 'mdtryNoLabel', 'minlength', 'maxlength', 'minval', 'maxval', 'digit', 'natural', 'prcnt', 'alpha', 'alnum', 'phone', 'email', 'emails', 'url');
foreach ($aErrorLabels as $sErrorLabel) {
    $GLOBALS['TL_LANG']['ERR'][$sErrorLabel] = str_replace(
        '!',
        '.',
        $GLOBALS['TL_LANG']['ERR'][$sErrorLabel]
    );
}


/**
 * Checkout Form Validation Errors
 */
$GLOBALS['TL_LANG']['ERR']['sepa']['iban_country'] = 'Die eingegebene IBAN enthält kein oder ein ungültiges Länderkürzel.';
$GLOBALS['TL_LANG']['ERR']['sepa']['iban_length'] = 'Die eingebene IBAN ist zu lang oder zu kurz.';
$GLOBALS['TL_LANG']['ERR']['sepa']['iban_invalid'] = 'Bitte geben Sie eine gültige IBAN ein.';
$GLOBALS['TL_LANG']['ERR']['sepa']['bic_invalid'] = 'Bitte geben Sie eine gültige BIC ein.';

$GLOBALS['TL_LANG']['ERR']['postal']['invalid'] = 'Bitte geben Sie eine deutsche Postleitzahl mit 5 Ziffern ein.';
$GLOBALS['TL_LANG']['ERR']['postal']['outOfScope'] = 'Für die von Ihnen angegebene Postleitzahl bieten wir online kein Heizöl an. Bitte rufen Sie uns an. Wir informieren Sie gerne über unsere Angebote: ' . Label::getLabel('company.phone');

$GLOBALS['TL_LANG']['ERR']['amount']['invalid'] = 'Bitte geben Sie die benötigte Menge als ganze Zahl zwischen %s und %s ein.';
$GLOBALS['TL_LANG']['ERR']['amount']['outOfScope'] = 'Online sind Bestellungen nur zwischen %s und %s Liter möglich.';
$GLOBALS['TL_LANG']['ERR']['amount']['minForMultipleUnloadingPoints'] = 'Die Mindestmenge je Entladestelle beträgt 500 Liter.';
$GLOBALS['TL_LANG']['ERR']['payment']['zmzinvalid'] = 'Das Wärmekonto bieten wir zurzeit nur Einzelbestellern an. Bitte wählen Sie eine andere Bezahlart aus.';

/**
 * Checkout steps
 */
$GLOBALS['TL_LANG']['checkoutSteps']['offer'] = Label::getLabel('checkout.step1');
$GLOBALS['TL_LANG']['checkoutSteps']['address'] = Label::getLabel('checkout.step2');
$GLOBALS['TL_LANG']['checkoutSteps']['confirmation'] = Label::getLabel('checkout.step3');


/**
 * Address form
 */
$GLOBALS['TL_LANG']['addressForm']['headlineDefault'] = Label::getLabel('addressform.headline.default');
$GLOBALS['TL_LANG']['addressForm']['notesPlaceholder'] = 'z. B. keine Wendemöglichkeit, Sackgasse oder Halteverbotsschilder erforderlich';


/**
 * Unloading points form
 */
$GLOBALS['TL_LANG']['unloadingPointsForm']['headlineDefault'] = Label::getLabel('unloadingpointsform.headline.default');
//$GLOBALS['TL_LANG']['unloadingPointsForm']['headlineDefault'] = 'Das ist ein Test.';


/**
 * ZMZ account form
 */
$GLOBALS['TL_LANG']['zmzAccountForm']['headlineDefault'] = Label::getLabel('zmzaccount.headline.default');

$GLOBALS['TL_LANG']['zmzAccountForm']['sepaBankPlaceholder'] = 'Musterbank';
$GLOBALS['TL_LANG']['zmzAccountForm']['sepaBicPlaceholder'] = 'DEUTDEFFXXX';
$GLOBALS['TL_LANG']['zmzAccountForm']['sepaIbanPlaceholder'] = 'DEXXXXXXXXXXXXXXXXXXXX';

// TODO: Dynamically get label from Typo3.
$GLOBALS['TL_LANG']['zmzAccountForm']['sepaExplanation'] = Label::getLabel('sepa.directdebitmandate.explanation');

// TODO: Dynamically get label from Typo3.
$GLOBALS['TL_LANG']['zmzAccountForm']['sepaInstructions'] = Label::getLabel('sepa.payment.explanation');


/**
 * Oil type form
 */
$GLOBALS['TL_LANG']['oilTypeForm']['headlineDefault'] = Label::getLabel('oiltypeform.headline.default');


/**
 * Review form
 */
$GLOBALS['TL_LANG']['review']['headlineDefault'] = Label::getLabel('review.headline.default');


/**
 * Birthday form
 */
$GLOBALS['TL_LANG']['birthdayForm']['additionalText'] = '<p><strong>Rechnung</strong><br>Damit wir eine Bonitätsprüfung vornehmen können, benötigen wir noch das Geburtsdatum des Rechnungsempfängers.</p>';


/**
 * errors
 */

$noPostalLabel = '<div class="postal-not-found">';
$noPostalLabel .= "<h1>Vielen Dank für Ihr Interesse</h1>";
$noPostalLabel .= "<p><strong>Für die von Ihnen verwendete Postleitzahl bieten wir online kein Heizöl an.</strong> <br>Bitte rufen Sie uns an. Wir informieren Sie gerne über unsere Angebote. %s</p>";
$noPostalLabel .= '<p class="additional-text">' . Slashworks\SwCalc\Models\Label::getLabel('checkout.step4.order.additionalText') . '</p>';
$noPostalLabel .= '<div class="cta-wrapper"><a class="button" target="_blank" href="' . Slashworks\SwCalc\Models\Label::getLabel('checkout.step4.order.cta.link') . '">' . Slashworks\SwCalc\Models\Label::getLabel('checkout.step4.order.cta.text') . '</a></div>';
$noPostalLabel .= '</div>';

$GLOBALS['TL_LANG']['product']['nopostal'] = Label::getDynamicLabel($noPostalLabel,'team');