<?php

namespace h4kuna\Fio\Request\Pay\Payment;

use Tester,
	Tester\Assert,
	h4kuna\Fio\Request\Pay,
	h4kuna\Fio\Test;

$container = require_once __DIR__ . '/../../../../bootstrap.php';

/**
 * @author Milan Matějček
 */
class NationalTest extends Tester\TestCase
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
		$pay = $this->paymentFactory->createNational(500, '987654321/4321');
		$pay->setDate('2015-01-23');
		$xml = $this->xmlFile->setData($pay)->getXml();

		Assert::equal(Test\Utils::getContent('payment/pay-minimum.xml'), $xml);

		// same Property paymentFactory
		$pay->setAccountTo('987654321/4321', '4321'); // is OK
		$xml = $this->xmlFile->setData($pay)->getXml();
		Assert::equal(Test\Utils::getContent('payment/pay-minimum.xml'), $xml);

		// cloned paymentFactory Property
		$pay = $this->paymentFactory->createNational(500, '987654321', '4321');
		$xml = $this->xmlFile->setData($pay)->getXml();
		$expectedXml = Test\Utils::getContent('payment/pay-minimum.xml');
		Assert::equal(str_replace('2015-01-23', date('Y-m-d'), $expectedXml), $xml);
	}

	public function testMaximum()
	{
		$pay = $this->paymentFactory->createNational(1000, '987654/9874')
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
		Assert::equal(Test\Utils::getContent('payment/pay-maximum.xml'), $xml);
	}

}

$test = new NationalTest($container);
$test->run();
