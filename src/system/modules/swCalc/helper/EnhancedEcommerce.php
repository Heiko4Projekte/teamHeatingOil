<?php

namespace Slashworks\SwCalc\Helper;

use Contao\FrontendTemplate;
use Slashworks\SwCalc\Classes\Formatter;
use Slashworks\SwCalc\Models\Product;
use Slashworks\SwCalc\Models\ProductConfiguration;

class EnhancedEcommerce
{
    /**
     * @var \stdClass
     */
    protected $oObject;

    /**
     * @var Product
     */
    protected $oProduct;

    /**
     * @var ProductConfiguration
     */
    protected $oProductConfiguration;

    public function __construct(Product $oProduct)
    {
        $this->oObject = new \stdClass();
        $this->oProduct = $oProduct;
        $this->oProductConfiguration = $oProduct->getConfiguration();
    }

    public function generateAddImpression()
    {
        $this->addId();
        $this->addName();
        $this->addList();
        $this->addPosition();

        $this->addPostal();
        $this->addShipping();
        $this->addPayment();

        $this->addAmount();
        $this->addUnloadingPoints();
        $this->addTotal();
        $this->addTotalPer100();

        $oTemplate = new FrontendTemplate('ec_addImpression');
        $oTemplate->attributes = json_encode($this->oObject);
        $this->addObjectToGlobal($oTemplate->parse());
    }

    public function generateAddProduct()
    {
        $this->addId();
        $this->addName();
        $this->addPosition();

        $this->addPostal();
        $this->addShipping();
        $this->addPayment();

        $this->addAmount();
        $this->addUnloadingPoints();
        $this->addTotal();
        $this->addTotalPer100();

        $oTemplate = new FrontendTemplate('ec_addProduct');
        $oTemplate->attributes = json_encode($this->oObject);
        $oTemplate->list = $this->oProduct->list;

        $this->addObjectToGlobal($oTemplate->parse());
    }

    protected function addObjectToGlobal($sString)
    {
        global $objPage;

        $aGACommands = array();

        if ($objPage->GACommands) {
            $aGACommands = $objPage->GACommands;
        }

        $aGACommands[] = $sString;
        $objPage->GACommands = $aGACommands;
    }

    protected function addId()
    {
        $this->oObject->id = ($this->oProductConfiguration->id) ? $this->oProductConfiguration->id : $this->oProduct->id;
    }

    protected function addName()
    {
        $this->oObject->name = ($this->oProductConfiguration->title) ? $this->oProductConfiguration->title : $this->oProduct->title;
    }

    protected function addList()
    {
        $this->oObject->list = $this->oProduct->list;
    }

    protected function addPosition()
    {
        $this->oObject->position = $this->oProduct->position;
    }

    protected function addPostal()
    {
        $this->oObject->dimension1 = $this->oProductConfiguration->postal;
    }

    protected function addOilType()
    {
        $this->oObject->dimension2 = $this->oProductConfiguration->getOilType();
    }

    protected function addShipping()
    {
        $this->oObject->dimension3 = $this->oProductConfiguration->getShipping();
    }

    protected function addPayment()
    {
        $this->oObject->dimension4 = $this->oProductConfiguration->getPayment();
    }

    protected function addAmount()
    {
        $this->oObject->metric1 = $this->oProductConfiguration->getAmount();
    }

    protected function addUnloadingPoints()
    {
        $this->oObject->metric2 = $this->oProductConfiguration->unloadingPoints;
    }

    protected function addTotal()
    {
        $this->oObject->metric3 = round($this->oProduct->getTotal(), 2);
    }

    protected function addTotalPer100()
    {
        $this->oObject->metric4 = round($this->oProduct->getTotalPer100(), 2);
    }

}