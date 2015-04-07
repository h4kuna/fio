<?php

namespace h4kuna\Fio\Request\Pay\Payment;

use h4kuna\Fio\Request\Pay\XMLFile;
use h4kuna\Fio\Request\Pay\PaymentFactory;
use Tester;

$container = require_once __DIR__ . '/../../bootstrap.php';

/**
 * @author Milan Matějček
 */
class EuroTest extends Tester\TestCase
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
        $pay = $this->object->getEuro(500, 'AT611904300234573201', 'ABAGATWWXXX', 'Milan', 'jp');
        $pay->setDate('2015-01-23');
        $xml = $this->xmlFile->setData($pay)->getXml();
        $this->assertXmlStringEqualsXmlFile(self::getRoot() . '/data/euro-minimum.xml', $xml);
    }

    public function testMaximum()
    {
        $pay = $this->object->getEuro(500, 'AT611904300234573201', 'ABAGATWWXXX', 'Milan', 'jp')
                ->setCity('Prague')
                ->setRemittanceInfo1('info 1')
                ->setRemittanceInfo2('info 2')
                ->setRemittanceInfo3('info 3')
                ->setStreet('Street 44')
                ->setConstantSymbol('321')
                ->setCurrency('Usd')
                ->setMyComment('Lorem ipsum')
                ->setDate('2014-01-23')
                ->setPaymentReason('110')
                ->setSpecificSymbol('378')
                ->setVariableSymbol('123456789')
                ->setPaymentType(Euro::PAYMENT_PRIORITY);
        $xml = $this->xmlFile->setData($pay)->getXml();
        $this->assertXmlStringEqualsXmlFile(self::getRoot() . '/data/euro-maximum.xml', $xml);
    }

    private static function getRoot()
    {
        return __DIR__ . '/../../../..';
    }

}
