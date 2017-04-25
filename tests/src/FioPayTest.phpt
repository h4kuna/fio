<?php

namespace h4kuna\Fio;

use Tester,
	Tester\Assert,
	h4kuna\Fio\Test,
	Salamium\Testinium;

$container = require_once __DIR__ . '/../bootstrap.php';

/**
 * @author Milan Matějček
 */
class FioPayTest extends Tester\TestCase
{

	/** @var FioPay */
	private $fioPay;

	/** @var Test\FioFactory */
	private $fioFactory;

	public function __construct(Test\FioFactory $fioFactory)
	{
		$this->fioFactory = $fioFactory;
	}

	protected function setUp()
	{
		$this->fioPay = $this->fioFactory->createFioPay();
	}

	public function testSend()
	{
		$payment1 = $this->fioPay->createNational(100, '24301556/1234')
			->setDate('2016-01-12');
		$this->fioPay->addPayment($payment1);
		$payment2 = $this->fioPay->createNational(200, '9865/0997')
			->setDate('2016-01-12');
		$xml = $this->fioPay->send($payment2);
		Assert::same(Testinium\File::load('payment/multi-pay.xml'), $xml);
	}

}

$fioFactory = new Test\FioFactory;
(new FioPayTest($fioFactory))->run();
