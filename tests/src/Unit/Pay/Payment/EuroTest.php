<?php declare(strict_types = 1);

namespace h4kuna\Fio\Tests\Unit\Pay\Payment;

use h4kuna\Fio\FioPay;
use h4kuna\Fio\Pay\Payment\Euro;
use h4kuna\Fio\Pay\XMLFile;
use h4kuna\Fio\Tests\Fixtures\FioFactory;
use h4kuna\Fio\Tests\Fixtures\TestCase;
use Tester\Assert;
use function h4kuna\Fio\Tests\loadResult;

require __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
class EuroTest extends TestCase
{

	private FioPay $fioPay;

	private XMLFile $xmlFile;


	public function testMinimum(): void
	{
		$pay = $this->fioPay->createEuro(500, 'AT611904300234573201', 'Milan', 'LAVBDD33XXX');
		$pay->setDate('2015-01-23');
		$xml = $this->xmlFile->setData($pay)->getXml();
		Assert::equal(loadResult('payment/euro-minimum.xml'), $xml);
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
			->setConstantSymbol('0321')
			->setCurrency('Usd')
			->setMyComment('Lorem ipsum')
			->setDate('2014-01-23')
			->setPaymentReason(110)
			->setSpecificSymbol('0378')
			->setVariableSymbol('0123456789')
			->setPaymentType(Euro::PAYMENT_PRIORITY);
		$xml = $this->xmlFile->setData($pay)->getXml();
		Assert::equal(loadResult('payment/euro-maximum.xml'), $xml);
	}

	protected function setUp(): void
	{
		$fioFactory = new FioFactory();
		$this->fioPay = $fioFactory->createFioPay();
		$this->xmlFile = $fioFactory->getXmlFile();
	}

}

(new EuroTest())->run();
