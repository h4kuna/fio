<?php

namespace h4kuna\Fio\Request\Pay\Payment;

use h4kuna\Fio\Request\Pay,
    Tester\Assert,
    Tester\TestCase;

$container = require_once __DIR__ . '/../../../../bootstrap.php';

/**
 * @author Milan Matějček
 */
class InternationalTest extends TestCase
{

    /**
     * @var Pay\PaymentFactory
     */
    private $paymentFactory;

    /** @var Pay\XMLFile */
    private $xmlFile;
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    protected function setUp()
    {
        $this->paymentFactory = $this->container->getService('fioExtension.paymentFactory');
        $this->xmlFile = $this->container->getService('fioExtension.xmlFile');
    }

    public function testMinimum()
    {
        $pay = $this->paymentFactory->createInternational(500, 'AT611904300234573201', 'ABAGATWWXXX', 'Milan', 'Street 44', 'Prague', 'jp', 'Info 1');
        $pay->setDate('2015-01-23');
        $xml = $this->xmlFile->setData($pay)->getXml();
        Assert::equal(self::getContent('international-minimum.xml'), $xml);
    }

    public function testMaximum()
    {
        $pay = $this->paymentFactory->createInternational(500, 'AT611904300234573201', 'ABAGATWWXXX', 'Milan', 'Street 44', 'Prague', 'jp', 'Info 1')
                ->setDetailsOfCharges(International::CHARGES_SHA)
                ->setRemittanceInfo2('info 2')
                ->setRemittanceInfo3('info 3')
                ->setRemittanceInfo4('info 4')
                ->setCurrency('Usd')
                ->setMyComment('Lorem ipsum')
                ->setDate('2014-01-23')
                ->setPaymentReason('311');
        $xml = $this->xmlFile->setData($pay)->getXml();
        Assert::equal(self::getContent('international-maximum.xml'), $xml);
    }

    private static function getContent($file)
    {
        return file_get_contents(__DIR__ . '/../../../../data/payment/' . $file);
    }

}

$test = new InternationalTest($container);
$test->run();
