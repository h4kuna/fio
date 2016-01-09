<?php

namespace h4kuna\Fio\Request\Pay\Payment;

use h4kuna\Fio\Utils;

/**
 * @author Milan Matějček
 */
class International extends Euro
{

    const
            CHARGES_OUR = 470501,
            CHARGES_BEN = 470502,
            CHARGES_SHA = 470503;

    /** @var string */
    protected $benefStreet = TRUE;

    /** @var string */
    protected $benefCity = TRUE;

    /** @var string */
    protected $remittanceInfo1 = TRUE;

    /** @var string */
    protected $remittanceInfo4;

    /**
     * Default value is goods export.
     * @see Property
     */
    protected $paymentReason = 110;

    /** @var int */
    protected $detailsOfCharges = self::CHARGES_BEN;

    /**
     * @param int $type
     * @return self
     * @throws Utils\FioException
     */
    public function setDetailsOfCharges($type)
    {
        static $types = array(self::CHARGES_BEN, self::CHARGES_OUR, self::CHARGES_SHA);
        if (!in_array($type, $types)) {
            throw new Utils\FioException('Select one type from constatns. Section in manual 6.3.4.');
        }
        $this->detailsOfCharges = $type;
        return $this;
    }

    /**
     *
     * @param string $str
     * @return self
     */
    public function setRemittanceInfo4($str)
    {
        $this->remittanceInfo4 = Utils\Strings::substr($str, 35);
        return $this;
    }

    protected function getExpectedProperty()
    {
        return array('accountFrom', 'currency', 'amount', 'accountTo', 'bic', 'date',
            'comment', 'benefName', 'benefStreet', 'benefCity', 'benefCountry',
            'remittanceInfo1', 'remittanceInfo2', 'remittanceInfo3', 'remittanceInfo4',
            'detailsOfCharges', 'paymentReason');
    }

    public function getStartXmlElement()
    {
        return 'ForeignTransaction';
    }

    /** @deprecated */
    public function setConstantSymbol($ks)
    {
        throw new Utils\FioException('Not available.');
    }

    /** @deprecated */
    public function setSpecificSymbol($ss)
    {
        throw new Utils\FioException('Not available.');
    }

    /** @deprecated */
    public function setVariableSymbol($vs)
    {
        throw new Utils\FioException('Not available.');
    }

    /** @deprecated */
    public function setPaymentType($type)
    {
        throw new Utils\FioException('Not available.');
    }

}
