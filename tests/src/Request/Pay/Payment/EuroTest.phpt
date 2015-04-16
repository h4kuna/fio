<?php

namespace h4kuna\Fio\Request\Pay\Payment;

use h4kuna\Fio\Request\Pay\PaymentFactory,
    h4kuna\Fio\Request\Pay\XMLFile,
    Tester;

$container = require_once __DIR__ . '/../../../../bootstrap.php';

/**
 * @author Milan Matějček
 */
class EuroTest extends Tester\TestCase
{

    /**
     * @var PaymentFactory
     */
    private $paymentFactory;

    /** @var XMLFile */
    private $xmlFile;
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    protected function setUp()
    {
        $this->paymentFactory = $this->container->createService('fioExtension.paymentFactory');
        $this->xmlFile = $this->container->createService('fioExtension.xmlFile');
    }

    public function testMinimum()
    {
        $pay = $this->paymentFactory->createEuro(500, 'AT611904300234573201', 'ABAGATWWXXX', 'Milan', 'jp');
        $pay->setDate('2015-01-23');
        $xml = $this->xmlFile->setData($pay)->getXml();
        Tester\Assert::equal(self::getContent('euro-minimum.xml'), $xml);
    }

    public function testMaximum()
    {
        $pay = $this->paymentFactory->createEuro(500, 'AT611904300234573201', 'ABAGATWWXXX', 'Milan', 'jp')
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
        Tester\Assert::equal(self::getContent('euro-maximum.xml'), $xml);
    }

    private static function getContent($file)
    {
        return file_get_contents(__DIR__ . '/../../../../data/payment/' . $file);
    }

}

$test = new EuroTest($container);
$test->run();
