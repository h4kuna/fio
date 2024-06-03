<?php declare(strict_types=1);

namespace h4kuna\Fio;

use h4kuna\Fio\Response\Read\Transaction;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class FioReadTest extends Test\TestCase
{
	/** @var Test\FioFactory */
	private $fioFactory;

	/** @var FioRead */
	private $fioRead;

	/** @var string */
	private $token;


	public function testMovements(): void
	{
		$data = $this->fioRead->movements(1420070400, '2015-04-16');
		$moveId = 7139752766;
		foreach ($data as $key => $transaction) {
			if ($key === 0) {
				continue;
			}
			assert($transaction instanceof Response\Read\Transaction);
			Assert::equal($moveId, $transaction->moveId);
			foreach ($transaction as $property => $value) {
				if ($property === 'moveId') {
					Assert::equal($moveId, $value);
					break 2;
				}
			}
		}

		Assert::type(\stdClass::class, $data->getInfo());
		Assert::same(10, count($data));

		Assert::equal(Fio::REST_URL . 'periods/' . $this->token . '/2015-01-01/2015-04-16/transactions.json', $this->fioRead->getRequestUrl());

		if (Request\Read\Files\Json::isJsonBug()) {
			Assert::equal(unserialize(file_get_contents(__DIR__ . '/../data/tests/php7.1/2015-01-01-2015-04-16-transactions.srlz')), $data);
		} else {
			Assert::equal(unserialize(file_get_contents(__DIR__ . '/../data/tests/2015-01-01-2015-04-16-transactions.srlz')), $data);
		}
	}


	public function testMovementsEmpty(): void
	{
		$data = $this->fioRead->movements('2011-01-01', '2011-01-02');

		if (Request\Read\Files\Json::isJsonBug()) {
			Assert::equal(unserialize(file_get_contents(__DIR__ . '/../data/tests/php7.1/2011-01-01-2011-01-02-transactions.srlz')), $data);
		} else {
			Assert::equal(unserialize(file_get_contents(__DIR__ . '/../data/tests/2011-01-01-2011-01-02-transactions.srlz')), $data);
		}
	}


	public function testMovementId(): void
	{
		$fioRead = $this->fioFactory->createFioRead('bar');
		$token = $fioRead->getAccount()->getToken();
		Assert::same('hgfedcba', $token);
		$data = $fioRead->movementId(2, 2015);

		if (Request\Read\Files\Json::isJsonBug()) {
			Assert::equal(unserialize(file_get_contents(__DIR__ . '/../data/tests/php7.1/2015-2-transactions.srlz')), $data);
		} else {
			Assert::equal(unserialize(file_get_contents(__DIR__ . '/../data/tests/2015-2-transactions.srlz')), $data);
		}

		Assert::equal(Fio::REST_URL . 'by-id/' . $token . '/2015/2/transactions.json', $fioRead->getRequestUrl());
	}


	public function testLastDownload(): void
	{
		$data = $this->fioRead->lastDownload();
		if (Request\Read\Files\Json::isJsonBug()) {
			Assert::equal(unserialize(file_get_contents(__DIR__ . '/../data/tests/php7.1/last-transactions.srlz')), $data);
		} else {
			Assert::equal(unserialize(file_get_contents(__DIR__ . '/../data/tests/last-transactions.srlz')), $data);
		}
		Assert::equal(Fio::REST_URL . 'last/' . $this->token . '/transactions.json', $this->fioRead->getRequestUrl());

		$i = 0;
		foreach ($data as $i => $transaction) {
			Assert::type(Transaction::class, $transaction);
		}
		Assert::same(3, $i);
	}


	public function testSetLastId(): void
	{
		$this->fioRead->setLastId(7155451447);
		Assert::equal(Fio::REST_URL . 'set-last-id/' . $this->token . "/7155451447/", $this->fioRead->getRequestUrl());
	}


	public function testSetLastDate(): void
	{
		$dt = new \DateTime('-1 week');
		$this->fioRead->setLastDate('-1 week');
		Assert::equal(Fio::REST_URL . 'set-last-date/' . $this->token . '/' . $dt->format('Y-m-d') . '/', $this->fioRead->getRequestUrl());
	}


	protected function setUp()
	{
		$this->fioFactory = new Test\FioFactory();
		$this->fioRead = $this->fioFactory->createFioRead();
		$this->token = $this->fioRead->getAccount()->getToken();
	}

}

(new FioReadTest)->run();
