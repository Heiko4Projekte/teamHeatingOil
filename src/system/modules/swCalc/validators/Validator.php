<?php

namespace Slashworks\SwCalc\Validators;

use Slashworks\SwCalc\Models\Collection;


class Validator
{

    /**
     * @var int
     */
    protected $valid = 0;


    /**
     * @var Collection
     */
    protected $collection;


    /**
     * @var Collection
     */
    protected $data = [];


    /**
     * @var Form
     */
    protected $oForm;


    /**
     *
     */
    public function __construct()
    {
        $this->setCollection();
        $this->checkValidation();

    }


    /**
     * @return mixed
     */
    protected function checkValidation(){}


    /**
     * @param $status
     */
    protected function setValidStatus($status)
    {
        $this->valid = $status;
    }


    /**
     *
     */
    public function getValidstatus()
    {
        return $this->valid;
    }

    /**
     *
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     *
     */
    public function isValid()
    {

        if ($this->valid) {
            return true;
        }

        return false;
    }

    /**
     *
     */
    protected function setCollection()
    {
        $collection = Collection::getCurrent();
        $this->collection = $collection;
    }


}