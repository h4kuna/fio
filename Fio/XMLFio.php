<?php

namespace h4kuna\Fio;

use Nette\DateTime;

/**
 *
 * @author Milan Matějček
 */
class XMLFio {

    const PAYMENT_STANDARD = 431001;
    const PAYMENT_STANDARD_EURO = 431008;

    /**
     * Payment types
     *
     * @var array
     */
    private static $euroPaymentTypes = array('euro_standard' => self::PAYMENT_STANDARD_EURO, 'euro_priority' => 431009);

    /**
     * Payment types
     *
     * @var array
     */
    private static $paymentTypes = array('standard' => self::PAYMENT_STANDARD, 'fast' => 431004, 'priority' => 431005, 'collection' => 431022);

    /** @var string */
    private $currency = 'CZK';

    /** @var int */
    private $variableSymbol;

    /** @var int */
    private $specificSymbol;

    /** @var int */
    private $constatnSymbol;

    /** @var int */
    private $account;

    /** @var \XMLWriter */
    private $xml;

    /** @var DateTime */
    private $date;

    /** @var int */
    private $paymentType;

    /** @var int */
    private $paymentReason;

    /** @var string */
    private $comment;

    /** @var string */
    private $message;

    /** @var string */
    private $content;

    /** @var string */
    private $temp;

    /** @var string */
    private $benefStreet;

    /** @var string */
    private $benefCity;

    /** @var string */
    private $remittanceInfo1;

    /** @var string */
    private $remittanceInfo2;

    /** @var string */
    private $remittanceInfo3;

    /** @var int */
    private $paymentTypeEuro = self::PAYMENT_STANDARD_EURO;

    /**
     *
     * @param string $account
     */
    public function __construct($account, $temp = NULL) {
        $this->account = preg_replace('~(/\d+)$~', '', $account);
        $this->setTemp($temp);
        $this->createEmptyXml();
    }

// <editor-fold defaultstate="collapsed" desc="Setters">
    /**
     * @param string $ks
     * @return XMLFio
     */
    public function setConstantSymbol($ks) {
        if (!$ks) {
            $ks = NULL;
        } elseif (!preg_match('~\d{1,4}~', $ks)) {
            throw new FioException('Constant symbol must contain 1-10 digits.');
        }
        $this->constatnSymbol = $ks;
        return $this;
    }

    /**
     * Currency code ISO 4217
     *
     * @param string $str
     * @return XMLFio
     */
    public function setCurrency($code) {
        if (!preg_match('~[a-z]{3}~i', $code)) {
            throw new FioException('Currency code must match ISO 4217.');
        }
        $this->currency = strtoupper($code);
        return $this;
    }

    /**
     * @param string|DateTime $str
     * @return XMLFio
     */
    public function setDate($str) {
        if (!$str) {
            $str = 'now';
        }
        $this->date = $str;
        return $this;
    }

    /**
     * @param int|string $type
     * @return XMLFio
     * @throws FioException
     */
    public function setPaymentType($type) {
        if (isset(self::$paymentTypes[$type]) || in_array($type, self::$paymentTypes)) {
            $this->paymentType = isset(self::$paymentTypes[$type]) ? self::$paymentTypes[$type] : $type;
        } else {
            throw new FioException('Unsupported payment type: ' . $type);
        }
        return $this;
    }

    /**
     *
     * @param string $ss
     * @return XMLFio
     */
    public function setSpecificSymbol($ss) {
        if (!$ss) {
            $ss = NULL;
        } elseif (!preg_match('~\d{1,10}~', $ss)) {
            throw new FioException('Specific symbol must contain 1-10 digits.');
        }
        $this->specificSymbol = $ss;
        return $this;
    }

    /**
     * @param string $str
     * @return XMLFio
     */
    public function setMessage($str) {
        $this->message = $this->substr($str, 140);
        return $this;
    }

    /**
     * @param string $str
     * @return XMLFio
     */
    public function setComment($str) {
        $this->comment = $this->substr($str, 255);
        return $this;
    }

    /**
     *
     * @param string|int $vs
     * @return XMLFio
     */
    public function setVariableSymbol($vs) {
        if (!$vs) {
            $vs = NULL;
        } elseif (!preg_match('~\d{1,10}~', $vs)) {
            throw new FioException('Variable symbol must contain 1-10 digits.');
        }
        $this->variableSymbol = $vs;
        return $this;
    }

    /**
     *
     * @param int $code
     * @throws FioException
     */
    public function setPaymentReason($code) {
        if (!$code) {
            $code = NULL;
        } elseif (!preg_match('~\d{3}~', $code)) {
            throw new FioException('Payment reason must contain 3 digits.');
        }
        $this->paymentReason = $code;
        return $this;
    }

    /**
     * Writeable temporary dir
     *
     * @param string $temp
     * @throws FioException
     */
    public function setTemp($temp) {
        if ($temp === NULL) {
            $temp = ini_get('upload_tmp_dir');
        }

        $temp = @realpath($temp);
        if (!$temp || !is_writable($temp)) {
            throw new FioException('Temporary directory must exists and writeable.');
        }

        $this->temp = $temp;
        return $this;
    }

    /**
     * @param int|string $type
     * @return XMLFio
     * @throws FioException
     */
    public function setPaymentTypeEuro($type) {
        if (isset(self::$euroPaymentTypes[$type]) || in_array($type, self::$euroPaymentTypes)) {
            $this->paymentType = isset(self::$euroPaymentTypes[$type]) ? self::$euroPaymentTypes[$type] : $type;
        } else {
            throw new FioException('Unsupported payment type: ' . $type);
        }
        return $this;
    }

    /**
     *
     * @param string $str
     * @return XMLFio
     */
    public function setBenefStreet($str) {
        $this->benefStreet = $this->substr($str, 50);
        return $this;
    }

    /**
     *
     * @param string $str
     * @return XMLFio
     */
    public function setBenefCity($str) {
        $this->benefCity = $this->substr($str, 50);
        return $this;
    }

    /**
     *
     * @param string $str
     * @return XMLFio
     */
    public function setBemittanceInfo1($str) {
        $this->benefCity = $this->substr($str, 35);
        return $this;
    }

    /**
     *
     * @param string $str
     * @return XMLFio
     */
    public function setBemittanceInfo2($str) {
        $this->benefCity = $this->substr($str, 35);
        return $this;
    }

    /**
     *
     * @param string $str
     * @return XMLFio
     */
    public function setBemittanceInfo3($str) {
        $this->benefCity = $this->substr($str, 35);
        return $this;
    }

    /**
     *
     * @param string $val
     * @param int $limit
     * @return string
     */
    private function substr($str, $limit) {
        return $str ? mb_substr($str, 0, $limit) : NULL; // max length from API
    }

// </editor-fold>

    /**
     *
     * @param string $amount
     * @param string $accountTo - 2212-2000000699, 2212-2000000699/2010
     * @param string $bankCode - 2010
     * @throws FioException
     */
    public function addPayment($amount, $accountTo, $bankCode = NULL) {
        if ($this->content) {
            $this->createEmptyXml();
        }

        $this->xml->startElement('DomesticTransaction');
        $this->xmlContent($amount, $accountTo, $bankCode);
        $this->addXmlNode('date', DateTime::from($this->date)->format('Y-m-d'));
        $this->addXmlNode('messageForRecipient', $this->message);
        $this->addXmlNode('comment', $this->comment);
        $this->addXmlNode('paymentReason', $this->paymentReason, FALSE);
        $this->addXmlNode('paymentType', $this->paymentType, self::PAYMENT_STANDARD);
        $this->xml->endElement();
    }

    /**
     *
     * @param float $amount
     * @param string $iban
     * @param string $bic
     * @param string $benefName
     * @param string $benefCountry
     * @throws FioException
     */
    public function addPaymentForeing($amount, $iban, $bic, $benefName, $benefCountry) {
        if ($this->content) {
            $this->createEmptyXml();
        }

        if (strlen($bic) != 11) {
            throw new FioException('BIC must lenght 11. Is ISO 9362.');
        }

        if (strlen($benefCountry) != 2 && $benefCountry != 'TCH') {
            throw new FioException('Country code consists of two letters.');
        }

        $this->xml->startElement('T2Transaction');
        $this->xmlContent($amount, $iban);
        $this->addXmlNode('bic', $bic, TRUE);
        $this->addXmlNode('date', DateTime::from($this->date)->format('Y-m-d'));
        $this->addXmlNode('comment', $this->comment);
        $this->addXmlNode('benefName', $benefName, TRUE);
        $this->addXmlNode('benefStreet', $this->benefStreet);
        $this->addXmlNode('benefCity', $this->benefCity);
        $this->addXmlNode('benefCountry', strtoupper($benefCountry), TRUE);
        $this->addXmlNode('remittanceInfo1', $this->remittanceInfo1);
        $this->addXmlNode('remittanceInfo2', $this->remittanceInfo2);
        $this->addXmlNode('remittanceInfo3', $this->remittanceInfo3);
        $this->addXmlNode('paymentReason', $this->paymentReason, FALSE);
        $this->addXmlNode('paymentType', $this->paymentTypeEuro, self::PAYMENT_STANDARD_EURO);

        $this->xml->endElement();
    }

    /**
     * Common content for national payment and foreing payment
     *
     * @param float $amount
     * @param string $accountTo
     * @param string|NULL $bankCode
     * @throws FioException
     */
    private function xmlContent($amount, $accountTo, $bankCode = 'foreing') {
        if (!$amount || !is_numeric($amount)) {
            throw new FioException('Amount can\'t be zero and must be a number.');
        }

        if (strpos($accountTo, '/') !== FALSE) {
            if ($bankCode) {
                throw new FioException('You have bank code in account and in param. Only one input is avaible.');
            }
            list($accountTo, $bankCode) = explode('/', $accountTo);
        }

        if (!$bankCode) {
            throw new FioException('Let\'s fill bank code in account or in parameter.');
        }

        $this->addXmlNode('accountFrom', $this->account, TRUE);
        $this->addXmlNode('currency', $this->currency);
        $this->addXmlNode('amount', $amount, TRUE);
        $this->addXmlNode('accountTo', $accountTo, TRUE);
        if ($bankCode !== 'foreing') {
            $this->addXmlNode('bankCode', $bankCode, TRUE);
        }
        $this->addXmlNode('ks', $this->constatnSymbol);
        $this->addXmlNode('vs', $this->variableSymbol);
        $this->addXmlNode('ss', $this->specificSymbol);
    }

    /**
     * Render XML
     *
     * @return string
     */
    public function getXml() {
        if ($this->content) {
            return $this->content;
        }
        $this->xml->endDocument();
        return $this->content = $this->xml->outputMemory();
    }

    /**
     *
     * @return string
     */
    public function getPathname() {
        $filename = $this->temp . DIRECTORY_SEPARATOR . md5(microtime(TRUE)) . '.xml';
        file_put_contents($filename, $this->getXml());
        register_shutdown_function(function() use ($filename) {
            @unlink($filename);
        });
        return $filename;
    }

    /**
     * Render XML
     *
     * @return string
     */
    public function __toString() {
        return (string) $this->getPathname();
    }

    /**
     * Create XML node and fill value
     *
     * @param string $node
     * @param mixed $val
     * @param mixed $default - TRUE = required, FALSE = if value empty does not write to xml
     * @throws FioException
     */
    private function addXmlNode($node, $val, $default = NULL) {
        if ($val === NULL && $default === FALSE) {
            return;
        }
        $this->xml->startElement($node);
        if ($val === NULL) {
            if ($default === TRUE) {
                throw new FioException('The node ' . $node . ' is required.');
            }

            $val = $default;
        }
        $this->xml->text($val);
        $this->xml->endElement();
    }

    /**
     * Prepare XML
     */
    private function createEmptyXml() {
        $this->xml = new \XMLWriter;
        $this->xml->openMemory();
        $this->xml->startDocument('1.0', 'UTF-8');
        $this->xml->startElement('Import');
        $this->xml->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $this->xml->writeAttribute('xsi:noNamespaceSchemaLocation', 'http://www.fio.cz/schema/importIB.xsd');
        $this->xml->startElement('Orders');
        $this->content = NULL;
    }

}
