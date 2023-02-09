<?php declare(strict_types=1);

namespace h4kuna\Fio\Tests\Unit;

use h4kuna;
use h4kuna\Fio\FioPay;
use h4kuna\Fio\Tests\Fixtures\FioFactory;
use h4kuna\Fio\Tests\Fixtures\TestCase;
use Tester\Assert;
use function h4kuna\Fio\Tests\loadResult;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class FioPayTest extends TestCase
{
	private FioPay $fioPay;


	public function testSend(): void
	{
		$this->fioPay->createNational(100, '24301556/0654')
			->setDate('2016-01-12');

		$this->fioPay->createNational(200, '9865/0123')
			->setDate('2016-01-12');
		$xml = $this->fioPay->send();

		Assert::same(loadResult('payment/multi-pay.xml'), (string) $xml);
	}


	/**
	 * @throws h4kuna\Fio\Exceptions\InvalidArgument
	 */
	public function testNoPayments(): void
	{
		$this->fioPay->send();
	}


	/**
	 * @throws h4kuna\Fio\Exceptions\InvalidArgument
	 */
	public function testNoContent(): void
	{
		$this->fioPay->createNational(200, '9865/0123')
			->setDate('2016-01-12');
		$this->fioPay->setLanguage('cs');
		$this->fioPay->getXml();
		$this->fioPay->send();
	}


	public function testContent(): void
	{
		$this->fioPay->createNational(200, '9865/0123')
			->setDate('2016-01-12');
		$xml = $this->fioPay->getXml();
		$response = $this->fioPay->send($xml);
		Assert::same(loadResult('payment/xml-pay.xml'), (string) $response);
	}


	protected function setUp()
	{
		$this->fioPay = (new FioFactory())->createFioPay();
	}

}

(new FioPayTest())->run();
