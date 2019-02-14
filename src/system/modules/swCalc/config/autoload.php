<?php

/**
 * Register namespace
 */
\Contao\ClassLoader::addNamespaces(array('Slashworks', 'Gruschit'));


/**
 * Register classes
 */
\Contao\ClassLoader::addClasses(array(

    // Classes
    'Slashworks\SwCalc\Classes\Formatter'            => 'system/modules/swCalc/classes/Formatter.php',
    'Slashworks\SwCalc\Classes\FormGroups'           => 'system/modules/swCalc/classes/FormGroups.php',
    'Slashworks\SwCalc\Classes\Configuration'        => 'system/modules/swCalc/classes/Configuration.php',
    'Slashworks\SwCalc\Classes\Validator'            => 'system/modules/swCalc/classes/Validator.php',
    'Slashworks\SwCalc\Classes\PaymentValidator'     => 'system/modules/swCalc/classes/PaymentValidator.php',
    'Slashworks\SwCalc\Classes\PricingTableImporter' => 'system/modules/swCalc/classes/PricingTableImporter.php',

    'Gruschit\SepaPayment'                                        => 'system/modules/swCalc/classes/SepaPayment.php',

    // Elements
    'Slashworks\SwCalc\Elements\CheckoutSteps'                    => 'system/modules/swCalc/elements/CheckoutSteps.php',

    // Backend
    'Slashworks\SwCalc\Backend\Collection'          => 'system/modules/swCalc/backend/Collection.php',
    'Slashworks\SwCalc\Backend\ExportCsv'           => 'system/modules/swCalc/backend/ExportCsv.php',

    // Hooks
    'Slashworks\SwCalc\Hooks\FormCalendarField'     => 'system/modules/swCalc/hooks/FormCalendarField.php',
    'Slashworks\SwCalc\Hooks\GetAttributesFromDca'  => 'system/modules/swCalc/hooks/GetAttributesFromDca.php',
    'Slashworks\SwCalc\Hooks\DailyCron'             => 'system/modules/swCalc/hooks/DailyCron.php',
    'Slashworks\SwCalc\Hooks\GeneratePage'             => 'system/modules/swCalc/hooks/GeneratePage.php',

    // Models
    'Slashworks\SwCalc\Models\Configuration'        => 'system/modules/swCalc/models/Configuration.php',
    'Slashworks\SwCalc\Models\Collection'           => 'system/modules/swCalc/models/Collection.php',
    'Slashworks\SwCalc\Models\ProductConfiguration' => 'system/modules/swCalc/models/ProductConfiguration.php',
    'Slashworks\SwCalc\Models\VarioValue'           => 'system/modules/swCalc/models/VarioValue.php',
    'Slashworks\SwCalc\Models\Product'              => 'system/modules/swCalc/models/Product.php',
    'Slashworks\SwCalc\Models\UnloadingPoint'       => 'system/modules/swCalc/models/UnloadingPoint.php',
    'Slashworks\SwCalc\Models\Pricing'              => 'system/modules/swCalc/models/Pricing.php',
    'Slashworks\SwCalc\Models\GeoInformation'       => 'system/modules/swCalc/models/GeoInformation.php',
    'Slashworks\SwCalc\Models\Label'                => 'system/modules/swCalc/models/Label.php',

    // Modules
    'Slashworks\SwCalc\Modules\AddressForm'         => 'system/modules/swCalc/modules/AddressForm.php',
    'Slashworks\SwCalc\Modules\UnloadingPointsForm' => 'system/modules/swCalc/modules/UnloadingPointsForm.php',
    'Slashworks\SwCalc\Modules\ProductList'         => 'system/modules/swCalc/modules/ProductList.php',
    'Slashworks\SwCalc\Modules\ProductConfigurator' => 'system/modules/swCalc/modules/ProductConfigurator.php',
    'Slashworks\SwCalc\Modules\AddressStepController'             => 'system/modules/swCalc/modules/AddressStepController.php',
    'Slashworks\SwCalc\Modules\OilTypeForm'                       => 'system/modules/swCalc/modules/OilTypeForm.php',
    'Slashworks\SwCalc\Modules\ZmzAccountForm'                    => 'system/modules/swCalc/modules/ZmzAccountForm.php',
    'Slashworks\SwCalc\Modules\AjaxController'                    => 'system/modules/swCalc/modules/AjaxController.php',
    'Slashworks\SwCalc\Modules\Review'                            => 'system/modules/swCalc/modules/Review.php',
    'Slashworks\SwCalc\Modules\Order'                             => 'system/modules/swCalc/modules/Order.php',
    'Slashworks\SwCalc\Modules\ClosedShop'                        => 'system/modules/swCalc/modules/ClosedShop.php',
    'Slashworks\SwCalc\Modules\PaymentForm'                        => 'system/modules/swCalc/modules/PaymentForm.php',

    // Validators
    'Slashworks\SwCalc\Validators\AppValidator'                   => 'system/modules/swCalc/validators/AppValidator.php',
    'Slashworks\SwCalc\Validators\Validator'                      => 'system/modules/swCalc/validators/Validator.php',
    'Slashworks\SwCalc\Validators\PaymentValidator'               => 'system/modules/swCalc/validators/PaymentValidator.php',
    'Slashworks\SwCalc\Validators\ShippingAddressValidator'       => 'system/modules/swCalc/validators/ShippingAddressValidator.php',
    'Slashworks\SwCalc\Validators\BillingAddressValidator'        => 'system/modules/swCalc/validators/BillingAddressValidator.php',
    'Slashworks\SwCalc\Validators\AdditionalInformationValidator' => 'system/modules/swCalc/validators/AdditionalInformationValidator.php',
    'Slashworks\SwCalc\Validators\OilTypeValidator'               => 'system/modules/swCalc/validators/OilTypeValidator.php',
    'Slashworks\SwCalc\Validators\ShippingValidator'              => 'system/modules/swCalc/validators/ShippingValidator.php',
    'Slashworks\SwCalc\Validators\PhoneOrMobile'                  => 'system/modules/swCalc/validators/PhoneOrMobile.php',
    'Slashworks\SwCalc\Validators\CollectionValidator'            => 'system/modules/swCalc/validators/CollectionValidator.php',
    'Slashworks\SwCalc\Validators\AmountValidator'                => 'system/modules/swCalc/validators/AmountValidator.php',
    'Slashworks\SwCalc\Validators\PostalValidator'                => 'system/modules/swCalc/validators/PostalValidator.php',

    //helper
    'Slashworks\SwCalc\Helper\zmzFormCreator'                     => 'system/modules/swCalc/helper/zmzFormCreator.php',
    'Slashworks\SwCalc\Helper\UnloadingPointsFormCreator'         => 'system/modules/swCalc/helper/UnloadingPointsFormCreator.php',
    'Slashworks\SwCalc\Helper\JavascriptHelper'                   => 'system/modules/swCalc/helper/JavascriptHelper.php',
    'Slashworks\SwCalc\Helper\LabelHelper'                        => 'system/modules/swCalc/helper/LabelHelper.php',
    'Slashworks\SwCalc\Helper\GeoCoder'                           => 'system/modules/swCalc/helper/GeoCoder.php',
    'Slashworks\SwCalc\Helper\EnhancedEcommerce'                  => 'system/modules/swCalc/helper/EnhancedEcommerce.php',
    'Slashworks\SwCalc\Helper\Shopvote'                           => 'system/modules/swCalc/helper/Shopvote.php',
    'Gruschit\SepaValidator'                                      => 'system/modules/swCalc/validators/SepaValidator.php',

    // Vendors
    'BusinessDays\Calculator' => 'system/modules/swCalc/vendors/andrejsstephanovs/business-days-calculator/BusinessDays/Calculator.php',

));


/**
 * Register templates
 */
\Contao\TemplateLoader::addFiles(array(

    // Backend
    'be_exportcsv'                   => 'system/modules/swCalc/templates/backend',

    // Elements
    'ce_checkout_steps'              => 'system/modules/swCalc/templates/elements',

    // Forms
    'form_address'                   => 'system/modules/swCalc/templates/forms',
    'form_unloadingpoints'           => 'system/modules/swCalc/templates/forms',
    'form_select'                    => 'system/modules/swCalc/templates/forms',
    'form_textfield'                 => 'system/modules/swCalc/templates/forms',
    'form_explanation'               => 'system/modules/swCalc/templates/forms',
    'modalbox_wrapper'               => 'system/modules/swCalc/templates/forms',

    // Modalbox forms

    //widgets
    'widget_radioExplanation'        => 'system/modules/swCalc/templates/forms',
    'widget_birthday'                => 'system/modules/swCalc/templates/forms',

    // Modules
    'mod_calc_addressstepcontroller' => 'system/modules/swCalc/templates/modules',
    'mod_calc_addressform'           => 'system/modules/swCalc/templates/modules',
    'mod_calc_unloadingpointsform'   => 'system/modules/swCalc/templates/modules',
    'mod_calc_productlist'           => 'system/modules/swCalc/templates/modules',
    'mod_calc_oiltypeform'           => 'system/modules/swCalc/templates/modules',
    'mod_calc_zmzaccountform'        => 'system/modules/swCalc/templates/modules',
    'mod_ajaxcontroller'             => 'system/modules/swCalc/templates/modules',
    'mod_review'                     => 'system/modules/swCalc/templates/modules',
    'mod_order'                      => 'system/modules/swCalc/templates/modules',
    'mod_closed'                     => 'system/modules/swCalc/templates/modules',

    'mod_calc_productconfigurator' => 'system/modules/swCalc/templates/modules',

    'mail_template'                           => 'system/modules/swCalc/templates/mail',
    'confirmation'                            => 'system/modules/swCalc/templates/mail',
    'confirmation_nikolaus'                   => 'system/modules/swCalc/templates/mail',

    //partials
    'partial_review_block'                    => 'system/modules/swCalc/templates/partials',
    'partial_review_block_tableless'          => 'system/modules/swCalc/templates/partials',
    'partial_review_block_price'              => 'system/modules/swCalc/templates/partials',
    'partial_review_price_details'            => 'system/modules/swCalc/templates/partials',
    'partial_review_block_unloadingpoints'    => 'system/modules/swCalc/templates/partials',
    'partial_unloadingpoints_maininformation' => 'system/modules/swCalc/templates/partials',
    'teamMember'                              => 'system/modules/swCalc/templates/partials',

    // jQuery
    'j_tooltip'                               => 'system/modules/swCalc/templates/jquery',


    // Google analytics enhanced ecommerce
    'ec_addImpression'                        => 'system/modules/swCalc/templates/googleAnalytics',
    'ec_addProduct'                           => 'system/modules/swCalc/templates/googleAnalytics',

    'mod_shop_deactivated'  => 'system/modules/swCalc/templates/modules',

    // Shopvote
    'shopvote_summary'      => 'system/modules/swCalc/templates/shopvote',
    'shopvote_rich_snippet' => 'system/modules/swCalc/templates/shopvote',

));