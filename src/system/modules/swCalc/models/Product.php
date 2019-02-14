<?php

namespace Slashworks\SwCalc\Models;

use Contao\ContentText;
use Contao\Database;
use Contao\Environment;
use Contao\Model;
use Haste\Number\Number;
use Slashworks\SwCalc\Classes\Formatter;

/**
 * Class Product
 * @package Slashworks\SwCalc\Models
 */
class Product
{

    /**
     * The total price of a product.
     *
     * @var float|int
     */
    protected $total = 0;

    /**
     * The total price of a product without Tax.
     *
     * @var float|int
     */
    protected $subTotal = 0;

    /**
     * The total percent tax.
     *
     * @var float|int
     */
    protected $tax = 19;

    /**
     * The total price of the tax value.
     *
     * @var float|int
     */
    protected $vat = 0;

    /**
     * The total price of the tax value.
     *
     * @var float|int
     */
    protected $vatPer100 = 0;

    /**
     * The price per 100 litres of a product.
     *
     * @var float|int
     */
    protected $subTotalPer100 = 0;

    /**
     * The price per 100 litres of a product, without any additional fees, e. g. antifreeze.
     *
     * @var float|int
     */
    protected $subTotalPerAmount = 0;

    /**
     * The price per 100 litres of a product.
     *
     * @var float|int
     */
    protected $totalPer100 = 0;

    /**
     * The surcharge portion defined by the amount.
     *
     * @var float|int
     */
    protected $amountSurcharge = 0;

    /**
     * The surcharge portion defined by the amount.
     *
     * @var float|int
     */
    protected $interpolatedValueBetweenTwoColumns = 0;

    /**
     * The surcharge portion defined by the shipping.
     *
     * @var float|int
     */
    protected $shippingSurcharge = 0;

    /**
     * The surcharge portion defined by the payment.
     *
     * @var float|int
     */
    protected $paymentSurcharge = 0;

    /**
     * The surcharge portion defined by the hose.
     *
     * @var float|int
     */
    protected $hoseSurcharge = 0;

    /**
     * The surcharge portion defined by the oilType.
     *
     * @var float|int
     */
    protected $oilTypeSurcharge = 0;

    /**
     * The surcharge portion defined by the tanker.
     *
     * @var float|int
     */
    protected $tankerSurcharge = 0;

    /**
     * The surcharge portion defined by the antifreeze.
     *
     * @var float|int
     */
    protected $antifreezeSurcharge = 0;

    /**
     * Price of a single unit of antifreeze.
     *
     * @var float|int
     */
    protected $antifreezePrice = 0;

    /**
     * The surcharge portion defined by ADR.
     *
     * @var float|int
     */
    protected $adr = 0;

    /**
     * The configuration property of a product.
     *
     * @var ProductConfiguration
     */
    protected $configuration;

    /**
     * @var Pricing
     */
    protected $pricing;

    /**
     * Product constructor.
     * @param ProductConfiguration|null $oConfiguration
     * @throws \Exception
     */
    public function __construct(ProductConfiguration $oConfiguration = null)
    {

        // Get configuration from collection
        if ($oConfiguration === null) {
            $oConfiguration = new ProductConfiguration();
            $oConfiguration->generateFromCollection();
        }

        $this->configuration = $oConfiguration;
        $this->pricing = Pricing::findByPostal();

        if ($this->pricing === null) {
            global $objPage;
            $objPage->customPageViewUrl = Environment::get('path') . '/ausserhalb-des-liefergebiets.html';

            throw new \Exception($GLOBALS['TL_LANG']['product']['nopostal']);
        }

        $this->calculatePrice();
    }


    /**
     * @return float|int
     */
    public function getTotal()
    {
        return $this->total;
    }

    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * @return float|int
     */
    public function getTotalPer100()
    {
        return $this->totalPer100;
    }

    public function getSubTotalPer100()
    {
        return $this->subTotalPer100;
    }

    public function getSubTotalPerAmount()
    {
        return $this->subTotalPerAmount;
    }

    public function getAdr()
    {
        return $this->adr + ($this->adr / 100 * $this->tax);
    }

    public function getVat()
    {
        return $this->vat;
    }

    public function getVatPer100()
    {
        return $this->vatPer100;
    }

    public function getTax()
    {
        return $this->tax;
    }

    public function getAntifreezeSurcharge()
    {
        return $this->antifreezeSurcharge;
    }

    public function getAntifreezePrice()
    {
        return $this->antifreezePrice;
    }

    public function getAntifreezePriceGross()
    {
        return $this->antifreezePrice * (1 + $this->tax/100);
    }

    public function getHoseSurcharge()
    {
        return $this->hoseSurcharge;
    }

    public function getOilTypeSurcharge()
    {
        return $this->oilTypeSurcharge;
    }

    public function getShippingSurcharge()
    {
        return $this->shippingSurcharge;
    }

    public function getPaymentSurcharge()
    {
        return $this->paymentSurcharge;
    }

    public function getShippingDate()
    {
        return $this->calculateShippingDate();
    }

    /**
     * @return null|ProductConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }


    /**
     * Calculate the price for a product by all its price determining properties.
     *
     *
     */
    protected function calculatePrice()
    {
        $this->calculatePurchasePrice();
        $this->addAdrFlat();
        $this->calculateShipping();
        $this->calculateUnloadingPoints();
        $this->calculatePayment();
        $this->calculateHose();
        $this->calculateOilType();
        $this->calculateTanker();

        $this->calculateAntifreezePrice();
        $this->calculateAntifreeze();
//
//         tax per 100
        $this->calculateTotalPer100();

        $this->calculateSubTotalPerAmount();
//
//         tax per total
        $this->calculateTotal();
    }


    /**
     * Calculate the purchasePrice dependent portion of the product's price.
     */
    protected function calculatePurchasePrice()
    {
        $amount = $this->configuration->getAmount();
        $fPurchasePricePer100 = $this->pricing->getPurchasePrice();
        $this->interpolatedValueBetweenTwoColumns = $this->pricing->getInterpolatedValueBetweenTwoColumns($amount);

        $this->totalPer100 += $fPurchasePricePer100 + $this->interpolatedValueBetweenTwoColumns;
    }


    /**
     * add adr fix to total per 100
     */
    protected function addAdrFlat(){

        $this->adr = $this->pricing->getPriceForAdr();
        $this->addToTotalPer100($this->adr);
    }


    /**
     * @param $iAmount
     */
    public function addToTotalPer100($iAmount)
    {
        $this->totalPer100 += ($iAmount / $this->configuration->getAmount()) * 100;
    }


    /**
     * @param $iAmount
     */
    public function addToTotal($iAmount)
    {
        $this->totalPer100 += $iAmount;
    }


    /**
     * Calculate the shipping dependent portion of the product's price.
     */
    protected function calculateShipping()
    {
        $fShippingSurcharge = $this->pricing->getPriceForShipping($this->configuration->getShipping());
        $this->shippingSurcharge = $fShippingSurcharge;
        $this->addToTotalPer100($fShippingSurcharge);
    }


    /**
     * Calculate the payment dependent portion of the product's price.
     */
    protected function calculatePayment()
    {
        $fPaymentSurcharge = $this->pricing->getPriceForPayment($this->configuration->getPayment());
        $this->paymentSurcharge = $fPaymentSurcharge;
        $this->addToTotalPer100($fPaymentSurcharge);
    }


    /**
     * Calculate the payment dependent portion of the product's price.
     */
    protected function calculateUnloadingPoints()
    {
        if($this->configuration->unloadingPoints > 1){
            $this->addToTotalPer100(($this->configuration->unloadingPoints - 1) * $this->pricing->additionalUnloadingPoint);
        }
    }


    /**
     * Calculate the hose dependent portion of the product's price.
     */
    protected function calculateHose()
    {
        $fHoseSurcharge = $this->pricing->getPriceForHose($this->configuration->getHose());
        $this->hoseSurcharge = $fHoseSurcharge;
        $this->addToTotalPer100($fHoseSurcharge);
    }


    /**
     * Calculate the oilType dependent portion of the product's price.
     */
    protected function calculateOilType()
    {
        $fOilTypeSurcharge = $this->pricing->getPriceForOilType($this->configuration->getOilType());
        $this->oilTypeSurcharge = $fOilTypeSurcharge;
        $this->addToTotal($fOilTypeSurcharge);
    }


    /**
     *
     */
    protected function calculateTanker()
    {
        $fTankerSurcharge = $this->pricing->getPriceForTanker($this->configuration->getTanker());
        $this->tankerSurcharge = $fTankerSurcharge;
        $this->addToTotalPer100($fTankerSurcharge);
    }

    /**
     *
     */
    protected function calculateAntifreeze()
    {
        $fAntifreezeSurcharge = $this->pricing->getPriceForAntifreeze($this->configuration->getAntifreeze());
        $this->antifreezeSurcharge = $fAntifreezeSurcharge;

        // Do not add to total per 100
        // $this->addToTotalPer100($fAntifreezeSurcharge);
    }

    /**
     * Price of a single unit of antifreeze
     */
    protected function calculateAntifreezePrice()
    {
        $this->antifreezePrice = $this->pricing->getPriceForAntifreeze(1);
    }


    protected function calculateSubTotalPerAmount()
    {
        $this->subTotalPerAmount = round($this->subTotalPer100, 2) * $this->configuration->getAmount() / 100;
    }

    /**
     *
     */
    protected function calculateTotalPer100()
    {

        // gerundeter subtotal = netto
        $this->subTotalPer100 = round($this->totalPer100, 2);

        // steuer vom gerundetem netto berechnet
        $this->vatPer100 = round($this->subTotalPer100 / 100 * $this->tax, 2);

        // neuer wert für total, dieses mal gerundet und brutto
        $this->totalPer100 = $this->subTotalPer100 + $this->vatPer100;
    }

    /**
     *
     */
    protected function calculateTotal()
    {
        // netto total = gesamtpreis nettp
        $this->subTotal = $this->subTotalPer100 * $this->configuration->getAmount() / 100;
        // Add antifreeze
        $this->subTotal += $this->antifreezeSurcharge;
        // steuer des gesamtpreises
        $this->vat = $this->subTotal / 100 * $this->tax;
        $this->total = $this->subTotal + $this->vat;
    }

    protected function calculateShippingDate()
    {
        $oCollection = Collection::getCurrent();

        $freeWeekDays = array(\BusinessDays\Calculator::SATURDAY, \BusinessDays\Calculator::SUNDAY);
        $freeDays = array();
        $holidays = array
        (
            'germany' => array
            (
                '2018-01-01', // Neujahrstag
                '2018-03-30', // Karfreitag
                '2018-04-02', // Ostermontag
                '2018-05-01', // Tag der Arbeit
                '2018-05-10', // Christi Himmelfahrt
                '2018-05-21', // Pfingsmontag
                '2018-10-03', // Tag der Deutschen Einheit
                '2018-12-25', // 1. Weihnachtstag
                '2018-12-26', // 2. Weihnachtstag

                '2019-01-01', // Neujahrstag
                '2019-04-19', // Karfreitag
                '2019-04-22', // Ostermontag
                '2019-05-01', // Tag der Arbeit
                '2019-05-30', // Christi Himmelfahrt
                '2019-06-10', // Pfingsmontag
                '2019-10-03', // Tag der Deutschen Einheit
                '2019-12-25', // 1. Weihnachtstag
                '2019-12-26', // 2. Weihnachtstag

                '2020-01-01', // Neujahrstag
                '2020-04-10', // Karfreitag
                '2020-04-13', // Ostermontag
                '2020-05-01', // Tag der Arbeit
                '2020-05-21', // Christi Himmelfahrt
                '2020-06-01', // Pfingsmontag
                '2020-10-03', // Tag der Deutschen Einheit
                '2020-12-25', // 1. Weihnachtstag
                '2020-12-26', // 2. Weihnachtstag
            )
        );

        foreach ($holidays['germany'] as $day) {
            $freeDays[] = new \DateTime($day);
        }

        $calculator = new \BusinessDays\Calculator();
        $calculator->setStartDate(new \DateTime('now'));
        $calculator->setFreeWeekDays($freeWeekDays);
        $calculator->setFreeDays($freeDays);

        $deliveryDays = Pricing::getShippingDaysByShipping($oCollection->shipping);
        $calculator->addBusinessDays($deliveryDays);

        $deliveryDate = $calculator->getDate();

        return $deliveryDate->format('d.m.Y');
    }

}
