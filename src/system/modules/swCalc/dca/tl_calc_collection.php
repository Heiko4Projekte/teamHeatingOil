<?php

/**
 * Table tl_calc_collection
 */
$GLOBALS['TL_DCA']['tl_calc_collection'] = array
(

    // Config
    'config' => array
    (
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'ctable'           => array('tl_calc_unloadingpoint'),
        'sql' => array(
            'keys' => array(
                'id'        => 'primary',
                'sessionID' => 'unique'
            ),
        ),
        'notCreatable' => true,

    ),

    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'   => 2,
            'fields' => array('tstamp DESC, type'),
            'flag'   => 6,
            'disableGrouping' => true,
            'panelLayout' => 'sort,search,filter,limit'
        ),
        'label' => array
        (
            'fields' => array('naming','date','product','email'),
            'showColumns' => true,
            'label_callback' => array('Slashworks\SwCalc\Backend\Collection','labelCallback'),
        ),
        'global_operations' => array
        (
            'exportCsv' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calc_collection']['exportCsv'],
                'href'  => 'key=exportCsv',
                'class' => 'header_export_csv'
            ),
            'back' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['MSC']['backBT'],
                'href'       => 'mod=&table=',
                'class'      => 'header_back',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ),
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calc_collection']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ),
            'show' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calc_collection']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ),
            'mail' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calc_collection']['mail'],
                'href' => 'key=mail',
                'icon' => 'system/modules/newsletter/assets/icon.gif',
                'button_callback' => array('Slashworks\SwCalc\Backend\Collection', 'buttonCallback'),
            ),
            'salesMail' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calc_collection']['mail'],
                'href' => 'key=salesmail',
                'icon' => 'system/themes/default/images/user.gif',
                'button_callback' => array('Slashworks\SwCalc\Backend\Collection', 'salesMailButtonCallback'),
            )
        )
    ),

    // Palettes
    'palettes' => array
    (
        'default' => '{title_legend},type,orderId;
                        {shipping_legend},shippingSalutation,shippingFirstname, shippingLastname,shippingStreet,shippingPostal,shippingCity;
                        {billing_legend},billingSalutation,billingFirstname, billingLastname,billingStreet,billingPostal,billingCity;
                        {zmz_legend},zmzCustomerId,zmzChildSalutation, zmzChildFirstname,zmzChildLastname,zmzChildLastname,zmzChildBirthday;
                        {sepa_Legend},sepaSalutation,sepaFirstname, sepaLastname,sepaDueDay,sepaBank,sepaBic,sepaIban,sepaAcceptPayment,sepaRecurringPayment;
                        {contact_legend},birthday,email,phone,mobile,
                        {order_legend},amount,shipping,payment,oilType,hose,unloadingPoints,tanker,antifreeze,notes,acceptAgb,total;'
    ),

    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ),
        'tstamp' => array
        (
            'search' => true,
            'label' => &$GLOBALS['TL_LANG']['tl_calc_collection']['tstamp'],
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ),
        'product' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_collection']['product'],
        ),
        'date' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_collection']['date'],
        ),
        'naming' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_collection']['naming'],
        ),
        'sorting' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_collection']['sorting'],
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'orderId' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_collection']['orderId'],
            'exclude' => true,
            'inputType' => 'text',
            'search' => true,
            'eval' => array('maxlength' => 255, 'tl_class' => 'w50','readonly' => true),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'type' => array
        (
            'eval' => array('doNotShow' => true),
            'filter' => true,
            'reference' => &$GLOBALS['TL_LANG']['tl_calc_collection']['type'],
            'sql' => "varchar(32) NOT NULL default ''",
        ),
        'sessionID' => array
        (
            'eval' => array('doNotShow' => true),
            'sql' => "varchar(128) NOT NULL default ''",
        ),
        'labelGroup' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calc_collection']['labelGroup'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => array('maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''",
        ),
        'postal' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['postal'],
            'exclude'   => true,
            'inputType' => 'text',
            'save_callback' => array
            (
                array('Slashworks\SwCalc\Models\Collection', 'saveCallbackPostalField')
            ),
            'eval'      => array('rgxp' => 'number', 'mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'configurationField' => true, 'group' => array('fixedField'), 'autocomplete' => 'new-password'),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'amount' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['amount'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('rgxp' => 'number', 'mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'configurationField' => true, 'group' => array('fixedField'), 'autocomplete' => 'new-password'),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'partialAmount' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['partialAmount'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('rgxp' => 'number', 'minval' => \Slashworks\SwCalc\Models\Pricing::getMinAmount(), 'mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'unloadingPoints' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['unloadingPoints'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => $GLOBALS['TL_CALC']['unloadingPointValues'],
            'reference' => &$GLOBALS['TL_LANG']['tl_calc_collection']['unloadingPoint'],
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'configurationField' => true, 'group' => array('fixedField')),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'oilType' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['oilTypeValues'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => $GLOBALS['TL_CALC']['oilTypeValues'],
            'reference' => &$GLOBALS['TL_LANG']['tl_calc_collection']['oilType'],
            'eval'      => array('mandatory' => true, 'tl_class' => 'w50', 'configurationField' => true, 'group' => array('varioField'), 'addPriceToLabel' => true),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'shipping' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['shippingValues'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => $GLOBALS['TL_CALC']['shippingValues'],
            'reference' => &$GLOBALS['TL_LANG']['tl_calc_collection']['shipping'],
            'eval'      => array('mandatory' => true, 'tl_class' => 'w50', 'configurationField' => true, 'group' => array('varioField')),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'shippingDate' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['shippingDate'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'payment' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['paymentValues'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => $GLOBALS['TL_CALC']['paymentValues'],
            'reference' => &$GLOBALS['TL_LANG']['tl_calc_collection']['payment'],
            'eval'      => array('mandatory' => true, 'tl_class' => 'w50', 'configurationField' => true, 'group' => array('varioField')),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'zmzCustomerId' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['zmzCustomerId'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255, 'tl_class' => 'w50', 'group' => array('zmzMaster','zmzAccount')),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'shippingCompany' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['shippingCompany'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255, 'tl_class' => 'w50', 'group' => array('shipping')),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'shippingSalutation' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['shippingSalutation'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => array('male', 'female'),
            'reference' => &$GLOBALS['TL_LANG']['tl_calc_collection']['salutation'],
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('shipping'), 'class' => 'widget-shippingSalutation'),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'shippingFirstname' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['shippingFirstname'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('shipping'), 'class' => 'widget-shippingFirstname'),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'shippingLastname' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['shippingLastname'],
            'exclude'   => true,
            'inputType' => 'text',
            'search' => true,
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('shipping'), 'class' => 'widget-shippingLastname'),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'shippingStreet' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['shippingStreet'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('shipping'), 'class' => 'widget-shippingStreet', 'placeholder' => 'Geben Sie Ihre Adresse ein.', 'autocomplete' => 'new-password', 'autocompleterKey' => 'streetFieldId', 'autocompleterValue' => 'ctrl_shippingStreet', 'order' => 1),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'shippingPostal' => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_calc_collection']['shippingPostal'],
            'exclude'       => true,
            'inputType'     => 'text',
            'save_callback' => array
            (
                array('Slashworks\SwCalc\Models\Collection', 'saveCallbackShippingPostalField')
            ),
            'eval'          => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('shipping'), 'class' => 'widget-shippingPostal', 'autocompleterKey' => 'postalFieldId', 'autocompleterValue' => 'ctrl_shippingPostal', 'order' => 1),
            'sql'           => "varchar(255) NOT NULL default ''",
        ),
        'shippingCity' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['shippingCity'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('shipping'), 'class' => 'widget-shippingCity', 'placeholder' => 'Geben Sie Ihre Stadt ein.', 'autocomplete' => 'new-password', 'autocompleterKey' => 'cityFieldId', 'autocompleterValue' => 'ctrl_shippingCity', 'order' => 1),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'shippingAddressEqualsBillingAddress' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['shippingAddressEqualsBillingAddress'],
            'exclude'   => true,
            'default'   => '1',
            'inputType' => 'checkbox',
            'eval'      => array('group' => array('shippingAddressEqualsBillingAddress')),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'billingCompany' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['billingCompany'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255, 'tl_class' => 'w50', 'group' => array('billing')),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'billingSalutation' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['billingSalutation'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => array('male', 'female'),
            'reference' => &$GLOBALS['TL_LANG']['tl_calc_collection']['salutation'],
            'eval'      => array('maxlength' => 255, 'tl_class' => 'w50', 'group' => array('billing' ,'zmzMaster','zmzAccount')),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'billingFirstname' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['billingFirstname'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('billing','zmzMaster','zmzAccount')),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'billingLastname' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['billingLastname'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('billing','zmzMaster','zmzAccount')),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'billingStreet' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['billingStreet'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('billing','zmzBillingAddress','zmzAccount'), 'autocomplete' => 'new-password', 'autocompleterKey' => 'streetFieldId', 'autocompleterValue' => 'ctrl_billingStreet', 'order' => 1),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'billingPostal' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['billingPostal'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('billing','zmzBillingAddress','zmzAccount'), 'autocompleterKey' => 'postalFieldId', 'autocompleterValue' => 'ctrl_billingPostal', 'order' => 1),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'billingCity' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['billingCity'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('billing','zmzBillingAddress','zmzAccount'), 'autocomplete' => 'new-password', 'autocompleterKey' => 'cityFieldId', 'autocompleterValue' => 'ctrl_billingCity', 'order' => 1),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'zmzChildSalutation' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['zmzChildSalutation'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => array('male', 'female'),
            'reference' => &$GLOBALS['TL_LANG']['tl_calc_collection']['salutation'],
            'eval'      => array('maxlength' => 255, 'tl_class' => 'w50', 'group' => array('zmzChild','zmzAccount')),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'zmzChildFirstname' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['zmzChildFirstname'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('zmzChild','zmzAccount')),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'zmzChildLastname' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['zmzChildLastname'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('zmzChild','zmzAccount')),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'email' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['email'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'rgxp' => 'email', 'tl_class' => 'w50', 'group' => array('contact')),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'phone' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['phone'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255, 'tl_class' => 'w50', 'group' => array('contact')),
            'sql'       => "varchar(64) NOT NULL default ''",
        ),
        'mobile' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['mobile'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255, 'tl_class' => 'w50', 'group' => array('contact')),
            'sql'       => "varchar(64) NOT NULL default ''",
        ),
        'hose' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['hose'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => $GLOBALS['TL_CALC']['hoseValues'],
            'reference' => &$GLOBALS['TL_LANG']['tl_calc_collection']['hose'],
            'eval'      => array('maxlength' => 255, 'tl_class' => 'w50', 'group' => array('additionalInformation', 'additionalInformationReview'), 'addPriceToLabel' => true),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'tanker' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['tanker'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => $GLOBALS['TL_CALC']['tankerValues'],
            'reference' => &$GLOBALS['TL_LANG']['tl_calc_collection']['tanker'],
            'eval'      => array('maxlength' => 255, 'tl_class' => 'w50', 'group' => array('additionalInformation', 'additionalInformationReview')),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'antifreeze' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['antifreeze'],
            'inputType' => 'select',
            'options' => $GLOBALS['TL_CALC']['antifreezeValues'],
            'reference' => &$GLOBALS['TL_LANG']['tl_calc_collection']['antifreezeOptions'],
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('additionalInformation','additionalInformationReview'), 'addPriceToLabel' => true),
            'sql'       => "varchar(32) NOT NULL default ''",
        ),
        'antifreezeSurcharge' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['antifreezeSurcharge'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'antifreezePrice' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['antifreezePrice'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'paymentmethod' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['paymentmethod'],
            'exclude'   => true,
            'inputType' => 'text',
            'options'   => $GLOBALS['TL_CALC']['paymentValues'],
            'eval'      => array('maxlength' => 255, 'tl_class' => 'w50', 'group' => array('paymentmethod')),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'birthday' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['birthday'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50 cleave-date-field','group'=>array('zmzMaster','zmzAccount'), 'placeholder' => 'DD.MM.JJJJ'),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'zmzChildBirthday' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['zmzChildBirthday'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50 cleave-date-field','group'=>array('zmzChild','zmzAccount'), 'placeholder' => 'DD.MM.JJJJ'),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'notes' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['notes'],
            'exclude'   => true,
            'inputType' => 'textarea',
            'eval'      => array('group' => array('additionalInformation', 'additionalInformationReview'), 'placeholder' => $GLOBALS['TL_LANG']['addressForm']['notesPlaceholder']),
            'sql'       => "mediumtext NULL"
        ),
        'acceptAgb' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['acceptAgb'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => array('mandatory' => true, 'group' => array('additionalInformation','acceptAgb')),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'sendNews' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['sendNews'],
            'inputType' => 'checkbox',
            'eval'      => array('group' => array('additionalInformation','sendNews')),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'sepaSalutation' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['sepaSalutation'],
            'inputType' => 'select',
            'options'   => array('male', 'female'),
            'reference' => &$GLOBALS['TL_LANG']['tl_calc_collection']['salutation'],
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('sepa','zmzAccount')),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'sepaFirstname' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['sepaFirstname'],
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('sepa','zmzAccount')),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'sepaLastname' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['sepaLastname'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255, 'tl_class' => 'w50', 'group' => array('sepa','zmzAccount')),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'sepaInitialPrice' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['sepaInitialPrice'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('readonly'=>true,'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('sepa','zmzAccount')),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'sepaMonthlyPrice' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['sepaMonthlyPrice'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('readonly'=>true,'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('sepa','zmzAccount')),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'sepaDueDay' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['sepaDueDay'],
            'inputType' => 'select',
            'options'   => array('1', '15', '30'),
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('sepa','zmzAccount')),
            'sql'       => "varchar(32) NOT NULL default ''",
        ),
        'sepaRecurringPayment' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['sepaRecurringPayment'],
            'inputType' => 'checkbox',
            'eval'      => array('group' => array('sepa','zmzAccount')),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'sepaBank' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['sepaBank'],
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('sepa','zmzAccount'), 'placeholder' => $GLOBALS['TL_LANG']['zmzAccountForm']['sepaBankPlaceholder']),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'sepaBic' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['sepaBic'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'rgxp' => 'sepa_bic', 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('sepa','zmzAccount'), 'placeholder' => $GLOBALS['TL_LANG']['zmzAccountForm']['sepaBicPlaceholder']),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'sepaIban' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['sepaIban'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'rgxp' => 'sepa_iban', 'maxlength' => 255, 'tl_class' => 'w50', 'group' => array('sepa','zmzAccount'), 'placeholder' => $GLOBALS['TL_LANG']['zmzAccountForm']['sepaIbanPlaceholder']),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'sepaAcceptPayment' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['sepaAcceptPayment'],
            'inputType' => 'checkbox',
            'eval'      => array('mandatory' => true, 'group' => array('sepa','zmzAccount')),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'total' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['total'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'subTotal' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['subTotal'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'subTotalPerAmount' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['subTotalPerAmount'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'subTotalPer100' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['subTotalPer100'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'adr' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['adr'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'vat' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['vat'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'totalPer100' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['totalPer100'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'amountSurcharge' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['amountSurcharge'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'shippingSurcharge' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['shippingSurcharge'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'paymentSurcharge' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['paymentSurcharge'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'hoseSurcharge' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['hoseSurcharge'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'oilTypeSurcharge' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['oilTypeSurcharge'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'noSelection' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['noSelection'],
            'exclude'   => true,
            'default'   => '1',
            'inputType' => 'checkbox',
            'eval'      => array(),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'showOriginalValues' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_calc_collection']['showOriginalValues'],
            'exclude'   => true,
            'default'   => '1',
            'inputType' => 'checkbox',
            'eval'      => array(),
            'sql'       => "char(1) NOT NULL default '1'"
        )
    )
);

if (TL_MODE == 'BE'){
    foreach ($GLOBALS['TL_DCA']['tl_calc_collection']['fields'] as $field=>$value){
        if($value['eval']['mandatory']){
            $GLOBALS['TL_DCA']['tl_calc_collection']['fields'][$field]['eval']['mandatory'] = false;
        }
    }
}