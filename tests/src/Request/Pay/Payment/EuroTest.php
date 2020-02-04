<?php declare(strict_types=1);

namespace h4kuna\Fio\Request\Pay\Payment;

use h4kuna\Fio;
use h4kuna\Fio\Request\Pay\XMLFile;
use h4kuna\Fio\Test;
use Salamium\Testinium;
use Tester;

require __DIR__ . '/../../../../bootstrap.php';

class EuroTest extends Tester\TestCase
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


	public function testMinimum()
	{
		$pay = $this->fioPay->createEuro(500, 'AT611904300234573201', 'Milan', 'LAVBDD33XXX');
		$pay->setDate('2015-01-23');
		$xml = $this->xmlFile->setData($pay)->getXml();
		Testinium\File::save('payment/euro-minimum.xml', $xml);
		Tester\Assert::equal(Testinium\File::load('payment/euro-minimum.xml'), $xml);
	}


	public function testMaximum()
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
			->setPaymentType(Euro::PAYMENT_PRIORITY);
		$xml = $this->xmlFile->setData($pay)->getXml();
		Tester\Assert::equal(Testinium\File::load('payment/euro-maximum.xml'), $xml);
	}


	protected function setUp()
	{
		$this->fioPay = $this->fioFactory->createFioPay();
		$this->xmlFile = $this->fioFactory->getXmlFile();
	}

}

$fioFactory = new Test\FioFactory;
(new EuroTest($fioFactory))->run();
