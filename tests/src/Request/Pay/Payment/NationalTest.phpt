<?php

namespace h4kuna\Fio\Request\Pay\Payment;

use h4kuna\Fio\Request\Pay\XMLFile;
use h4kuna\Fio\Request\Pay\PaymentFactory;
use Tester,
    Tester\Assert;

$container = require_once __DIR__ . '/../../../../bootstrap.php';

/**
 * @author Milan Matějček
 */
class NationalTest extends Tester\TestCase
{

    /**
     * @var PaymentFactory
     */
    protected $object;

    /** @var XMLFile */
    protected $xmlFile;

    protected function setUp()
    {
        $this->object = new PaymentFactory('123456789');
        $this->xmlFile = new XMLFile(self::getRoot() . '/temp');
    }

    public function testMinimum()
    {
        $pay = $this->object->getNational(500, '987654321/4321');
        $pay->setDate('2015-01-23');
        $xml = $this->xmlFile->setData($pay)->getXml();
        Assert::matchFile(self::getRoot() . '/data/pay-minimum.xml', $xml);

        // same Property object
        $pay->setAccountTo('987654321/4321', '4321'); // is OK
        $xml = $this->xmlFile->setData($pay)->getXml();
        Assert::matchFile(self::getRoot() . '/data/pay-minimum.xml', $xml);

        // cloned object Property
        $pay = $this->object->getNational(500, '987654321', '4321');
        $xml = $this->xmlFile->setData($pay)->getXml();
        $expectedXml = file_get_contents(self::getRoot() . '/data/pay-minimum.xml');
        Assert::matchFile($xml, str_replace('2015-01-23', date('Y-m-d')));
    }

    public function testMaximum()
    {
        $pay = $this->object->getNational(1000, '987654/9874')
                ->setConstantSymbol('321')
                ->setCurrency('eur')
                ->setMyComment('Lorem ipsum')
                ->setDate('2014-01-23')
                ->setPaymentReason('333')
                ->setMessage('Hello Mr. Joe')
                ->setSpecificSymbol('378')
                ->setVariableSymbol('123456789')
                ->setPaymentType(National::PAYMENT_FAST);
        $xml = $this->xmlFile->setData($pay)->getXml();
        //$this->assertXmlStringEqualsXmlFile(self::getRoot() . '/data/pay-maximum.xml', $xml);
    }

    private static function getRoot()
    {
        return __DIR__ . '/../../../..';
    }

}

$test = new NationalTest();
$test->run();
