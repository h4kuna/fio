<?php declare(strict_types=1);

namespace h4kuna\Fio\Tests\Unit\Pay\Payment;

use h4kuna\Fio;
use h4kuna\Fio\Pay\XMLFile;
use Tester;

require __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
class EuroTest extends Fio\Tests\Fixtures\TestCase
{
	private Fio\FioPay $fioPay;

	private XMLFile $xmlFile;


	public function testMinimum(): void
	{
		$pay = $this->fioPay->createEuro(500, 'AT611904300234573201', 'Milan', 'LAVBDD33XXX');
		$pay->setDate('2015-01-23');
		$xml = $this->xmlFile->setData($pay)->getXml();
		Tester\Assert::equal(Fio\Tests\loadResult('payment/euro-minimum.xml'), $xml);
	}


	public function testMaximum(): void
	{
		$pay = $this->fioPay->createEuro(500, 'AT611904300234573201', 'Milan', 'ABAGATWWXXX')
			->setCity('Prague')
			->setRemittanceInfo1('info 1')
			->setRemittanceInfo2('info 2')
			->setRemittanceInfo3('info 3')
			->setStreet('Street 44')
			->setCountry('jp')
			->setConstantSymbol(321)
			->setCurrency('Usd')
			->setMyComment('Lorem ipsum')
			->setDate('2014-01-23')
			->setPaymentReason(110)
			->setSpecificSymbol(378)
			->setVariableSymbol(123456789)
			->setPaymentType(Fio\Pay\Payment\Euro::PAYMENT_PRIORITY);
		$xml = $this->xmlFile->setData($pay)->getXml();
		Tester\Assert::equal(Fio\Tests\loadResult('payment/euro-maximum.xml'), $xml);
	}


	protected function setUp(): void
	{
		$fioFactory = new Fio\Tests\Fixtures\FioFactory();
		$this->fioPay = $fioFactory->createFioPay();
		$this->xmlFile = $fioFactory->getXmlFile();
	}

}

(new EuroTest())->run();
