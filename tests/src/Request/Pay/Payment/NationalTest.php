<?php declare(strict_types=1);

namespace h4kuna\Fio\Request\Pay\Payment;

use h4kuna\Fio;
use h4kuna\Fio\Test;
use Tester\Assert;

require __DIR__ . '/../../../../bootstrap.php';

/**
 * @testCase
 */
class NationalTest extends Test\TestCase
{
	/** @var Fio\FioPay */
	private $fioPay;

	/** @var Fio\Request\Pay\XMLFile */
	private $xmlFile;

	/** @var Test\FioFactory */
	private $fioFactory;


	public function __construct(Test\FioFactory $fioFactory)
	{
		$this->fioFactory = $fioFactory;
	}


	public function testMinimum(): void
	{
		$pay = $this->fioPay->createNational(500, '987654321/0123');
		$pay->setDate('2015-01-23');
		$xml = $this->xmlFile->setData($pay)->getXml();

		// Testinium\File::save('payment/pay-minimum.xml', $xml);
		Assert::equal(Testinium\File::load('payment/pay-minimum.xml'), $xml);

		// same Property paymentFactory
		$pay->setAccountTo('987654321')->setBankCode('0123');
		$xml = $this->xmlFile->setData($pay)->getXml();
		Assert::equal(Testinium\File::load('payment/pay-minimum.xml'), $xml);

		// cloned paymentFactory Property
		$pay = $this->fioPay->createNational(500, '987654321', '0123');
		$xml = $this->xmlFile->setData($pay)->getXml();
		$expectedXml = Testinium\File::load('payment/pay-minimum.xml');
		Assert::same(str_replace('2015-01-23', date('Y-m-d'), $expectedXml, $count), $xml);
		Assert::same(1, $count);
	}


	public function testMaximum(): void
	{
		$pay = $this->fioPay->createNational(1000, '987654/0123')
			->setConstantSymbol(321)
			->setCurrency('eur')
			->setMyComment('Lorem ipsum')
			->setDate('2014-01-23')
			->setPaymentReason(333)
			->setMessage('Hello Mr. Joe')
			->setSpecificSymbol(378)
			->setVariableSymbol(123456789)
			->setPaymentType(National::PAYMENT_PRIORITY);
		$xml = $this->xmlFile->setData($pay)->getXml();
		// Testinium\File::save('payment/pay-maximum.xml', $xml);
		Assert::same(Testinium\File::load('payment/pay-maximum.xml'), $xml);
	}


	protected function setUp()
	{
		$this->fioPay = $this->fioFactory->createFioPay();
		$this->xmlFile = $this->fioFactory->getXmlFile();
	}

}

$fioFactory = new Test\FioFactory;
(new NationalTest($fioFactory))->run();
