<?php declare(strict_types=1);

namespace h4kuna\Fio;

use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class FioPayTest extends Test\TestCase
{
	/** @var FioPay */
	private $fioPay;

	/** @var Test\FioFactory */
	private $fioFactory;


	public function __construct(Test\FioFactory $fioFactory)
	{
		$this->fioFactory = $fioFactory;
	}


	public function testSend(): void
	{
		$payment1 = $this->fioPay->createNational(100, '24301556/0654')
			->setDate('2016-01-12');
		$this->fioPay->addPayment($payment1);
		$payment2 = $this->fioPay->createNational(200, '9865/0123')
			->setDate('2016-01-12');
		$xml = $this->fioPay->send($payment2);
		// Testinium\File::save('payment/multi-pay.xml', $xml);
		Assert::same(file_get_contents(__DIR__ . '/../data/tests/payment/multi-pay.xml'), (string) $xml);
	}


	protected function setUp()
	{
		$this->fioPay = $this->fioFactory->createFioPay();
		$this->fioPay->setLanguage('cs');
	}

}

$fioFactory = new Test\FioFactory;
(new FioPayTest($fioFactory))->run();
