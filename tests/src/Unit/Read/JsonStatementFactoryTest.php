<?php declare(strict_types=1);

namespace h4kuna\Fio\Tests\Unit\Read;

use GuzzleHttp\Psr7\Response;
use h4kuna\Fio\Read\Json;
use h4kuna\Fio\Read\TransactionFactory;
use h4kuna\Fio\Tests\Fixtures\FioFactory;
use h4kuna\Fio\Tests\Fixtures\MyTransaction;
use h4kuna\Fio\Tests\Fixtures\TestCase;
use Tester\Assert;
use function h4kuna\Fio\Tests\loadResult;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class JsonStatementFactoryTest extends TestCase
{

	public function testCustomTransactionClass(): void
	{
		$fioFactory = (new class extends FioFactory {
			protected function createReader(): Json
			{
				return new Json((new class extends TransactionFactory {
					protected function createTransaction(): object
					{
						return new MyTransaction();
					}

				}));
			}

		});
		$json = $fioFactory->getReader();
		$sourceJson = loadResult('raw://2015-01-01-2015-04-16-transactions.json');
		assert(is_string($sourceJson));
		$list = $json->create(new Response(body: $sourceJson));

		Assert::same(10, count($list));
		$match = false;
		foreach ($list as $item) {
			assert($item instanceof MyTransaction);
			Assert::type(MyTransaction::class, $item);
			if ($item->amount === 0.25) {
				Assert::same('0000', $item->bank_code);
				Assert::same('', $item->to_account);
				$match = true;
			}
		}

		Assert::true($match);
	}

}

(new JsonStatementFactoryTest())->run();
