<?php

namespace h4kuna\Fio\Request\Pay\Payment;

use h4kuna\Fio\Utils\FioException;
use h4kuna\Fio\Utils\String;

/**
 * @author Milan Matějček
 */
class Euro extends Property
{

    const PAYMENT_STANDARD = 431008;
    const PAYMENT_PRIORITY = 431009;

    /** @var string */
    protected $bic = TRUE;

    /** @var string */
    protected $benefName = TRUE;

    /** @var string */
    protected $benefStreet;

    /** @var string */
    protected $benefCity;

    /** @var string */
    protected $benefCountry = TRUE;

    /** @var string */
    protected $remittanceInfo1;

    /** @var string */
    protected $remittanceInfo2;

    /** @var string */
    protected $remittanceInfo3;

    /** @var int */
    protected $paymentType = self::PAYMENT_STANDARD;

    public function __construct($account)
    {
        parent::__construct($account);
        $this->setCurrency('EUR');
    }

    /**
     *
     * @param string $accountTo ISO 13616
     * @throws FioException
     */
    public function setAccountTo($accountTo)
    {
        if (strlen($accountTo) > 34) {
            throw new FioException('Account is to long. ISO 13616.');
        }
        $this->accountTo = $accountTo;
        return $this;
    }

    /**
     *
     * @param string $bic
     * @return self
     * @throws FioException
     */
    public function setBic($bic)
    {
        if (strlen($bic) != 11) {
            throw new FioException('BIC must lenght 11. Is ISO 9362.');
        }
        $this->bic = $bic;
        return $this;
    }

    /**
     *
     * @param string $str
     * @return self
     */
    public function setStreet($str)
    {
        $this->benefStreet = String::substr($str, 35);
        return $this;
    }

    /**
     *
     * @param string $str
     * @return self
     */
    public function setCity($str)
    {
        $this->benefCity = String::substr($str, 35);
        return $this;
    }

    /**
     *
     * @param string $benefCountry
     * @return self
     */
    public function setCountry($benefCountry)
    {
        $country = strtoupper($benefCountry);
        if (strlen($country) != 2 && $country != 'TCH') {
            throw new FioException('Look at to manual for country code section 6.3.3.');
        }
        $this->benefCountry = $country;
        return $this;
    }

    /**
     *
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->benefName = String::substr($name, 35);
        return $this;
    }

    /**
     *
     * @param string $str
     * @return self
     */
    public function setRemittanceInfo1($str)
    {
        $this->remittanceInfo1 = String::substr($str, 35);
        return $this;
    }

    /**
     *
     * @param string $str
     * @return self
     */
    public function setRemittanceInfo2($str)
    {
        $this->remittanceInfo2 = String::substr($str, 35);
        return $this;
    }

    /**
     *
     * @param string $str
     * @return self
     */
    public function setRemittanceInfo3($str)
    {
        $this->remittanceInfo3 = String::substr($str, 35);
        return $this;
    }

    /**
     * @param int $type
     * @return self
     * @throws FioException
     */
    public function setPaymentType($type)
    {
        static $types = array(self::PAYMENT_STANDARD, self::PAYMENT_PRIORITY);
        if (!in_array($type, $types)) {
            throw new FioException('Unsupported payment type: ' . $type);
        }
        $this->paymentType = $type;
        return $this;
    }

    protected function getExpectedProperty()
    {
        return array('accountFrom', 'currency', 'amount', 'accountTo', 'ks', 'vs',
            'ss', 'bic', 'date', 'comment', 'benefName', 'benefStreet', 'benefCity',
            'benefCountry', 'remittanceInfo1', 'remittanceInfo2', 'remittanceInfo3',
            'paymentReason', 'paymentType');
    }

    public function getStartXmlElement()
    {
        return 'T2Transaction';
    }

}
