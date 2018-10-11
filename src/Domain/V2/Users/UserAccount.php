<?php

namespace Koma136\MyTarget\Domain\V2\Users;


use yii\base\BaseObject;

class UserAccount extends BaseObject
{
    /**
     * @var int
     * @Field(name="a_balance", type="int")
     */
    private $aBalance;

    /**
     * @var int
     * @Field(type="int")
     */
    private $balance;

    /**
     * @var int
     * @Field(name="currency_balance_hold", type="int")
     */
    private $currencyBalanceHold;

    /**
     * @var int
     * @Field(type="int")
     */
    private $id;

    /**
     * @var string
     * @Field(type="string")
     */
    private $type;

    /**
     * @return int
     */
    public function getABalance()
    {
        return $this->aBalance;
    }

    /**
     * @param $aBalance
     * @return mixed
     */
    public function setABalance($aBalance)
    {
        return $this->aBalance = $aBalance;
    }

    /**
     * @return int
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param $balance
     * @return mixed
     */
    public function setBalance($balance)
    {
        return $this->balance = $balance;
    }

    /**
     * @return int
     */
    public function getCurrencyBalanceHold()
    {
        return $this->currencyBalanceHold;
    }

    /**
     * @param $currencyBalanceHold
     * @return mixed
     */
    public function setCurrencyBalanceHold($currencyBalanceHold)
    {
        return $this->currencyBalanceHold = $currencyBalanceHold;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function setId($id)
    {
        return $this->id = $id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $type
     * @return mixed
     */
    public function setType($type)
    {
        return $this->type = $type;
    }
}