<?php

ini_set('xdebug.var_display_max_depth', 10);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 1024);

/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['swcalc'] = array
(

    'orders' => array
    (
        'tables' => array('tl_calc_collection'),
        'icon' => 'system/themes/flexible/images/settings.gif',
        'mail' => array('Slashworks\SwCalc\Backend\Collection', 'sendMailConfirmation'),
        'salesmail' => array('Slashworks\SwCalc\Backend\Collection', 'sendSalesMailConfirmation'),
        'exportCsv' => array('Slashworks\SwCalc\Backend\ExportCsv', 'exportCsv')
    ),
    'configuration'        => array
    (
        'tables' => array('tl_calc_configuration'),
        'icon' => 'system/themes/flexible/images/settings.gif',
        'update' => array('Slashworks\SwCalc\Classes\PricingTableImporter', 'updateConfiguration')
    ),
    'productconfiguration' => array
    (
        'tables' => array('tl_calc_product_configuration'),
        'icon' => 'system/themes/flexible/images/settings.gif'
    ),
    'variovalues'          => array
    (
        'tables' => array('tl_calc_variovalues'),
        'icon' => 'system/themes/flexible/images/settings.gif'
    ),
    'sw_calc_labels'       => array
    (
        'tables' => array('tl_calc_label'),
        'icon'   => 'system/themes/flexible/images/settings.gif',
    )
);


/**
 * Frontend modules
 */
$GLOBALS['FE_MOD']['swcalc'] = array
(
    'productlist' => 'Slashworks\SwCalc\Modules\ProductList',
    'ajaxcontroller' => 'Slashworks\SwCalc\Modules\AjaxController',
    'productconfigurator' => 'Slashworks\SwCalc\Modules\ProductConfigurator',
    'addressstepcontroller' => 'Slashworks\SwCalc\Modules\AddressStepController',
    'oiltypeform' => 'Slashworks\SwCalc\Modules\OilTypeForm',
    'addressform' => 'Slashworks\SwCalc\Modules\AddressForm',
    'unloadingpointsform' => 'Slashworks\SwCalc\Modules\UnloadingPointsForm',
    'review' => 'Slashworks\SwCalc\Modules\Review',
    'order' => 'Slashworks\SwCalc\Modules\Order',
    'closedshop' => 'Slashworks\SwCalc\Modules\ClosedShop',
);


/**
 * Content elements
 */
$GLOBALS['TL_CTE']['swcalc']['checkoutsteps'] = 'Slashworks\SwCalc\Elements\CheckoutSteps';


/**
 * Register models
 */
$GLOBALS['TL_MODELS']['tl_calc_configuration'] = 'Slashworks\SwCalc\Models\Configuration';
$GLOBALS['TL_MODELS']['tl_calc_collection'] = 'Slashworks\SwCalc\Models\Collection';
$GLOBALS['TL_MODELS']['tl_calc_product_configuration'] = 'Slashworks\SwCalc\Models\ProductConfiguration';
$GLOBALS['TL_MODELS']['tl_calc_variovalues'] = 'Slashworks\SwCalc\Models\VarioValue';
$GLOBALS['TL_MODELS']['tl_calc_unloadingpoint'] = 'Slashworks\SwCalc\Models\UnloadingPoint';
$GLOBALS['TL_MODELS']['pricing'] = 'Slashworks\SwCalc\Models\Pricing';
$GLOBALS['TL_MODELS']['tl_calc_label'] = 'Slashworks\SwCalc\Models\Label';


/**
 * Configuration
 */
$GLOBALS['TL_CALC']['unloadingPointValues'] = array('1', '2','3', '4', '5', '6', '7', '8', '9');
$GLOBALS['TL_CALC']['shippingValues'] = array('deliveryStandard', 'deliveryPremium', 'deliveryExpress');
$GLOBALS['TL_CALC']['paymentValues'] = array('invoice', 'ecCard', 'cash', 'zmzAccount');
$GLOBALS['TL_CALC']['oilTypeValues'] = array('economyOil', 'default');
$GLOBALS['TL_CALC']['hoseValues'] = array('default', 'hoseUpTo60', 'hoseUpTo80');
$GLOBALS['TL_CALC']['tankerValues'] = array('default', 'specialTankTruckSmall');
$GLOBALS['TL_CALC']['antifreezeValues'] = array('0','1','2','3','4','5','6','7','8','9');


// Defines the checkout steps.
$GLOBALS['TL_CALC']['checkoutSteps'] = array
(
    1 => 'offer',
    2 => 'address',
    3 => 'confirmation'
);

$GLOBALS['TL_CALC']['googleApiKey'] = 'AIzaSyCbLr8Kx_ifDi-5yp_gGyIuX96pgLRR69k';


/**
 * Register hooks
 */
$GLOBALS['TL_HOOKS']['addCustomRegexp'][] = array('Gruschit\SepaValidator', 'validate');
$GLOBALS['TL_HOOKS']['formCalendarField'][] = array('Slashworks\SwCalc\Hooks\FormCalendarField', 'customizeBirthdayField');
$GLOBALS['TL_HOOKS']['getAttributesFromDca']['includePricesInLabels'] = array(
    'Slashworks\SwCalc\Hooks\GetAttributesFromDca',
    'includePricesInLabels',
);
$GLOBALS['TL_HOOKS']['generatePage'][] = array('Slashworks\SwCalc\Hooks\GeneratePage', 'addCollectionDataToGlobalData');

//$GLOBALS['TL_HOOKS']['parseWidget'][] = array('Slashworks\SwCalc\Hooks\ParseWidget', 'addPricesForAntifreeze');
$GLOBALS['TL_CRON']['daily'][] = array('Slashworks\SwCalc\Hooks\DailyCron', 'removeOldSession');

if (TL_MODE === 'FE') {

    $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/swCalc/vendors/cleave.js/cleave.min.js|static';
    $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/swCalc/assets/googlePlacesAutocompleter.js|static';

    $GLOBALS['TL_HEAD'][] = '<script src="https://maps.googleapis.com/maps/api/js?key=' . $GLOBALS['TL_CALC']['googleApiKey'] . '&libraries=places&region=DE"></script>';

    $GLOBALS['TL_BODY'][] = '<script src="system/modules/swCalc/assets/modalbox.js"></script>';
    $GLOBALS['TL_BODY'][] = '<script src="system/modules/swCalc/vendors/jquery-match-height/jquery.matchHeight-min.js"></script>';
} else if (TL_MODE === 'BE') {
    $GLOBALS['TL_CSS'][] = 'system/modules/swCalc/assets/backend.css|static';
}
