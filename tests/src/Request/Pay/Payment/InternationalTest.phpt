<?php

namespace h4kuna\Fio\Request\Pay\Payment;

use h4kuna\Fio,
	Tester\Assert,
	Tester\TestCase,
	h4kuna\Fio\Test;

$container = require_once __DIR__ . '/../../../../bootstrap.php';

/**
 * @author Milan Matějček
 */
class InternationalTest extends TestCase
{

	/** @var Fio\FioPay */
	private $fioPay;

	/** @var XMLFile */
	private $xmlFile;

	/** @var Test\FioFactory */
	private $fioFactory;

	public function __construct(Test\FioFactory $fioFactory)
	{
		$this->fioFactory = $fioFactory;
	}

	protected function setUp()
	{
		$this->fioPay = $this->fioFactory->createFioPay();
		$this->xmlFile = $this->fioFactory->getXmlFile();
	}

	public function testMinimum()
	{
		$pay = $this->fioPay->createInternational(500, 'AT611904300234573201', 'ABAGATWWXXX', 'Milan', 'Street 44', 'Prague', 'jp', 'Info 1');
		$pay->setDate('2015-01-23');
		$xml = $this->xmlFile->setData($pay)->getXml();
		Assert::equal(Test\Utils::getContent('payment/international-minimum.xml'), $xml);
	}

	public function testMaximum()
	{
		$pay = $this->fioPay->createInternational(500, 'AT611904300234573201', 'ABAGATWWXXX', 'Milan', 'Street 44', 'Prague', 'jp', 'Info 1')
				->setDetailsOfCharges(International::CHARGES_SHA)
				->setRemittanceInfo2('info 2')
				->setRemittanceInfo3('info 3')
				->setRemittanceInfo4('info 4')
				->setCurrency('Usd')
				->setMyComment('Lorem ipsum')
				->setDate('2014-01-23')
				->setPaymentReason('311');
		$xml = $this->xmlFile->setData($pay)->getXml();
		Assert::equal(Test\Utils::getContent('payment/international-maximum.xml'), $xml);
	}

}

$fioFactory = new Test\FioFactory;
(new InternationalTest($fioFactory))->run();
