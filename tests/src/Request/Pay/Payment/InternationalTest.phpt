<?php

namespace h4kuna\Fio\Request\Pay\Payment;

use h4kuna\Fio\Request\Pay\XMLFile;
use h4kuna\Fio\Request\Pay\PaymentFactory;
use Tester;

$container = require_once __DIR__ . '/../../bootstrap.php';

/**
 * @author Milan Matějček
 */
class InternationalTest extends Tester\TestCase
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
        $pay = $this->object->getInternational(500, 'AT611904300234573201', 'ABAGATWWXXX', 'Milan', 'Street 44', 'Prague', 'jp', 'Info 1');
        $pay->setDate('2015-01-23');
        $xml = $this->xmlFile->setData($pay)->getXml();
        $this->assertXmlStringEqualsXmlFile(self::getRoot() . '/data/international-minimum.xml', $xml);
    }

    public function testMaximum()
    {
        $pay = $this->object->getInternational(500, 'AT611904300234573201', 'ABAGATWWXXX', 'Milan', 'Street 44', 'Prague', 'jp', 'Info 1')
                ->setDetailsOfCharges(International::CHARGES_SHA)
                ->setRemittanceInfo2('info 2')
                ->setRemittanceInfo3('info 3')
                ->setRemittanceInfo4('info 4')
                ->setCurrency('Usd')
                ->setMyComment('Lorem ipsum')
                ->setDate('2014-01-23')
                ->setPaymentReason('311');
        $xml = $this->xmlFile->setData($pay)->getXml();
        $this->assertXmlStringEqualsXmlFile(self::getRoot() . '/data/international-maximum.xml', $xml);
    }

    private static function getRoot()
    {
        return __DIR__ . '/../../../..';
    }

}
