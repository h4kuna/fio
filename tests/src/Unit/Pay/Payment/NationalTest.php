<?php declare(strict_types=1);

namespace h4kuna\Fio\Tests\Unit\Pay\Payment;

use h4kuna\Fio;
use Tester\Assert;

require __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
class NationalTest extends Fio\Tests\Fixtures\TestCase
{
	private Fio\FioPay $fioPay;

	private Fio\Pay\XMLFile $xmlFile;


	public function testMinimum(): void
	{
		$pay = $this->fioPay->createNational(500, '987654321/0123');
		$pay->setDate('2015-01-23');
		$xml = $this->xmlFile->setData($pay)->getXml();

		// Testinium\File::save('payment/pay-minimum.xml', $xml);
		Assert::equal(Fio\Tests\loadResult('payment/pay-minimum.xml'), $xml);

		// same Property paymentFactory
		$pay->setAccountTo('987654321')->setBankCode('0123');
		$xml = $this->xmlFile->setData($pay)->getXml();
		Assert::equal(Fio\Tests\loadResult('payment/pay-minimum.xml'), $xml);

		// cloned paymentFactory Property
		$pay = $this->fioPay->createNational(500, '987654321', '0123');
		$xml = $this->xmlFile->setData($pay)->getXml();
		$expectedXml = Fio\Tests\loadResult('payment/pay-minimum.xml');
		assert(is_string($expectedXml));
		Assert::same(str_replace('2015-01-23', date('Y-m-d'), $expectedXml, $count), $xml);
		Assert::same(1, $count);
	}


	public function testMaximum(): void
	{
		$pay = $this->fioPay->createNational(1000, '987654/0123')
			->setConstantSymbol('321')
			->setCurrency('eur')
			->setMyComment('Lorem ipsum')
			->setDate('2014-01-23')
			->setPaymentReason(333)
			->setMessage('Hello Mr. Joe')
			->setSpecificSymbol('378')
			->setVariableSymbol('0123456789')
			->setPaymentType(Fio\Pay\Payment\National::PAYMENT_PRIORITY);
		$xml = $this->xmlFile->setData($pay)->getXml();

		Assert::same(Fio\Tests\loadResult('payment/pay-maximum.xml'), $xml);
	}


	protected function setUp()
	{
		$fioFactory = new Fio\Tests\Fixtures\FioFactory();
		$this->fioPay = $fioFactory->createFioPay();
		$this->xmlFile = $fioFactory->getXmlFile();
	}

}

(new NationalTest())->run();
