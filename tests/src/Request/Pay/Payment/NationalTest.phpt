<?php

namespace h4kuna\Fio\Request\Pay\Payment;

use Tester,
	Tester\Assert,
	h4kuna\Fio,
	h4kuna\Fio\Test,
	Salamium\Testinium;

$container = require_once __DIR__ . '/../../../../bootstrap.php';

/**
 * @author Milan Matějček
 */
class NationalTest extends Tester\TestCase
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
		$pay = $this->fioPay->createNational(500, '987654321/4321');
		$pay->setDate('2015-01-23');
		$xml = $this->xmlFile->setData($pay)->getXml();

		Assert::equal(Testinium\File::load('payment/pay-minimum.xml'), $xml);

		// same Property paymentFactory
		$pay->setAccountTo('987654321/4321');
		$xml = $this->xmlFile->setData($pay)->getXml();
		Assert::equal(Testinium\File::load('payment/pay-minimum.xml'), $xml);

		// cloned paymentFactory Property
		$pay = $this->fioPay->createNational(500, '987654321', '4321');
		$xml = $this->xmlFile->setData($pay)->getXml();
		$expectedXml = Testinium\File::load('payment/pay-minimum.xml');
		Assert::equal(str_replace('2015-01-23', date('Y-m-d'), $expectedXml), $xml);
	}

	public function testMaximum()
	{
		$pay = $this->fioPay->createNational(1000, '987654/9874')
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
		Assert::equal(Testinium\File::load('payment/pay-maximum.xml'), $xml);
	}

}

$fioFactory = new Test\FioFactory;
(new NationalTest($fioFactory))->run();
