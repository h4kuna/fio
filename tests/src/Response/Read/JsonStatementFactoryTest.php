<?php declare(strict_types=1);

namespace h4kuna\Fio\Response\Read;

use h4kuna\Fio\Request\Read\Files;
use h4kuna\Fio\Test;
use Tester\Assert;

require __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class JsonStatementFactoryTest extends Test\TestCase
{

	public function testCustonTransactionClass(): void
	{
		$fioFactory = new Test\FioFactory(MyTransaction::class);
		$json = $fioFactory->getReader();
		$list = $json->create(file_get_contents(__DIR__ . '/../../../data/tests/2015-01-01-2015-04-16-transactions.json'));
		if (Files\Json::isJsonBug()) {
			Assert::same(file_get_contents(__DIR__ . '/../../../data/tests/php7.1/custom.srlz'), serialize($list));
		} else {
			// Testinium\File::save('custom.srlz', serialize($list));
			Assert::same(file_get_contents(__DIR__ . '/../../../data/tests/custom.srlz'), serialize($list));
		}
	}


	/**
	 * @throws \h4kuna\Fio\Exceptions\Runtime
	 */
	public function testThrow(): void
	{
		$factory = new JsonTransactionFactory(WrongTransaction::class);
		$factory->createTransaction(new \stdClass(), 'Y');
	}

}

final class WrongTransaction extends \stdClass
{

}

/**
 * @property-read float $amount [1]
 * @property-read string $to_account [2]
 * @property-read string $bank_code [3]
 */
final class MyTransaction extends TransactionAbstract
{

	/** custom method */
	public function setBank_code(?string $value): string
	{
		return str_pad((string) $value, 4, '0', STR_PAD_LEFT);
	}


	public function setTo_account(?string $value): string
	{
		if ($value === null) {
			return '';
		}
		return $value;
	}

}

(new JsonStatementFactoryTest())->run();
