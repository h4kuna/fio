<?php declare(strict_types=1);

namespace h4kuna\Fio\Request\Pay\Payment;

use h4kuna\Fio;
use h4kuna\Fio\Test;
use Salamium\Testinium;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../../../bootstrap.php';

class InternationalTest extends TestCase
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


	public function testMinimum()
	{
		$pay = $this->fioPay->createInternational(500, 'AT611904300234573201', 'Milan', 'Street 44', 'Prague', 'jp', 'Info 1', 'ABAGATWWXXX');
		$pay->setDate('2015-01-23');
		$xml = $this->xmlFile->setData($pay)->getXml();
		Testinium\File::save('payment/international-minimum.xml', $xml);
		Assert::equal(Testinium\File::load('payment/international-minimum.xml'), $xml);
	}


	public function testMaximum()
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
		Assert::equal(Testinium\File::load('payment/international-maximum.xml'), $xml);
	}


	protected function setUp()
	{
		$this->fioPay = $this->fioFactory->createFioPay();
		$this->xmlFile = $this->fioFactory->getXmlFile();
	}

}

$fioFactory = new Test\FioFactory;
(new InternationalTest($fioFactory))->run();
