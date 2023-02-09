<?php declare(strict_types=1);

namespace h4kuna\Fio\Tests\Unit;

use h4kuna\Fio\Read\Transaction;
use h4kuna\Fio\Utils;
use h4kuna\Fio\FioRead;
use h4kuna\Fio\Tests\Fixtures\FioFactory;
use h4kuna\Fio\Tests\Fixtures\TestCase;
use Tester\Assert;
use function h4kuna\Fio\Tests\loadResult;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class FioReadTest extends TestCase
{
	private FioFactory $fioFactory;

	private FioRead $fioRead;

	private string $token;


	public function testMovements(): void
	{
		$data = $this->fioRead->movements(1420070400, '2015-04-16');
		$moveId = 7139752766;
		$out = [];
		foreach ($data as $key => $transaction) {
			assert($transaction instanceof Transaction);
			$out[] = $transaction;
			if ($key !== 1) {
				continue;
			}
			Assert::equal($moveId, $transaction->moveId);
			foreach (get_object_vars($transaction) as $property => $value) {
				if ($property === 'moveId') {
					Assert::equal($moveId, $value);
					$moveId = null;
					break;
				}
			}
		}

		Assert::null($moveId);
		Assert::type(\stdClass::class, $data->getInfo());
		Assert::same(10, count($data));
		Assert::same(10, count($out));

		Assert::equal(loadResult('2015-01-01-2015-04-16-transactions.srlz'), $out);
	}


	public function testMovementsEmpty(): void
	{
		$data = $this->fioRead->movements('2011-01-01', '2011-01-02');

		Assert::equal(loadResult('2011-01-01-2011-01-02-transactions.srlz'), $data);
	}


	public function testMovementId(): void
	{
		$fioRead = $this->fioFactory->createFioRead('bar');
		$token = $fioRead->getAccount()->getToken();
		Assert::same('hgfedcba', $token);
		$data = $fioRead->movementId(2, 2015);

		Assert::equal(loadResult('raw://2015-2-transactions.srlz', $data), serialize($data));
	}


	public function testLastDownload(): void
	{
		$data = $this->fioRead->lastDownload();
		Assert::equal(loadResult('raw://last-transactions.srlz', $data), serialize($data));

		foreach ($data as $transaction) {
			Assert::type(Transaction::class, $transaction);
		}
		Assert::same(4, $data->count());
	}


	public function testSetLastId(): void
	{
		$response = $this->fioRead->setLastId(7155451447);
		Assert::equal(Utils\Fio::REST_URL . 'set-last-id/' . $this->token . "/7155451447/", $response->getReasonPhrase());
	}


	public function testSetLastDate(): void
	{
		$dt = new \DateTime('-1 week');
		$response = $this->fioRead->setLastDate('-1 week');
		Assert::equal(Utils\Fio::REST_URL . 'set-last-date/' . $this->token . '/' . $dt->format('Y-m-d') . '/', $response->getReasonPhrase());
	}


	protected function setUp()
	{
		$this->fioFactory = new FioFactory();
		$this->fioRead = $this->fioFactory->createFioRead();
		$this->token = $this->fioRead->getAccount()->getToken();
	}

}

(new FioReadTest)->run();
