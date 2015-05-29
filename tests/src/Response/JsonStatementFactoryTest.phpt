<?php

namespace h4kuna\Fio\Response\Read;

use Tester\Assert,
	h4kuna\Fio\Test;

require __DIR__ . '/../../bootstrap.php';

class JsonStatementFactoryTest extends \Tester\TestCase
{

	public function testCustonTransactionClass()
	{
		$statement = new JsonStatementFactory('h4kuna\Fio\Response\Read\MyTransactionTest');
		$json = new \h4kuna\Fio\Request\Read\Files\Json($statement);
		$list = $json->parse(Test\Utils::getContent('2015-01-01-2015-04-16-transactions.json'));
		Test\Utils::saveFile('custom.json', serialize($list));
		Assert::equal(Test\Utils::getContent('custom.json'), serialize($list));
	}

}

/**
 * @property-read float $amount [1]
 * @property-read string $to_account [2]
 * @property-read string $bank_code [3]
 */
class MyTransactionTest extends ATransaction
{

	/** custom method */
	public function setBank_code($value)
	{
		return str_pad($value, 4, '0', STR_PAD_LEFT);
	}

	public function setTo_account($value)
	{
		if (!$value) {
			return '';
		}
		return $value;
	}

}

$test = new JsonStatementFactoryTest();
$test->run();
