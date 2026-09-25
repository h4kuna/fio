<?php declare(strict_types = 1);

namespace h4kuna\Fio\Tests\Unit\Pay\Payment;

use h4kuna\Fio\FioPay;
use h4kuna\Fio\Pay\Payment\International;
use h4kuna\Fio\Pay\XMLFile;
use h4kuna\Fio\Tests\Fixtures\FioFactory;
use h4kuna\Fio\Tests\Fixtures\TestCase;
use Tester\Assert;
use function h4kuna\Fio\Tests\loadResult;

require __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
class InternationalTest extends TestCase
{

	private FioPay $fioPay;

	private XMLFile $xmlFile;


	public function testMinimum(): void
	{
		$pay = $this->fioPay->createInternational(500, 'AT611904300234573201', 'Milan', 'Street 44', 'Prague', 'jp', 'Info 1', 'ABAGATWWXXX');
		$pay->setDate('2015-01-23');
		$xml = $this->xmlFile->setData($pay)->getXml();
		Assert::equal(loadResult('payment/international-minimum.xml'), $xml);
	}

	public function testMaximum(): void
	{
		$pay = $this->fioPay->createInternational(500, 'AT611904300234573201', 'Milan', 'Street 44', 'Prague', 'jp', 'Info 1', 'ABAGATWWXXX')
			->setDetailsOfCharges(International::CHARGES_SHA)
			->setRemittanceInfo2('info 2')
			->setRemittanceInfo3('info 3')
			->setRemittanceInfo4('info 4')
			->setCurrency('Usd')
			->setMyComment('Lorem ipsum')
			->setDate('2014-01-23')
			->setPaymentReason(311);
		$xml = $this->xmlFile->setData($pay)->getXml();
		Assert::equal(loadResult('payment/international-maximum.xml'), $xml);
	}

	protected function setUp(): void
	{
		$fioFactory = new FioFactory();
		$this->fioPay = $fioFactory->createFioPay();
		$this->xmlFile = $fioFactory->getXmlFile();
	}

}

(new InternationalTest())->run();
