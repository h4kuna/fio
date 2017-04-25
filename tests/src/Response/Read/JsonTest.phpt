<?php

namespace h4kuna\Fio\Response\Read;

use h4kuna\Fio\Test,
	h4kuna\Fio\Request\Read\Files,
	Salamium\Testinium,
	Tester\Assert;

require __DIR__ . '/../../../bootstrap.php';

class JsonStatementFactoryTest extends \Tester\TestCase
{

	/** @var Test\FioFactory */
	private $fioFactory;

	public function __construct(Test\FioFactory $fioFactory)
	{
		$this->fioFactory = $fioFactory;
	}

	public function testCustonTransactionClass()
	{
		$json = $this->fioFactory->getReader();
		$list = $json->create(Testinium\File::load('2015-01-01-2015-04-16-transactions.json'));
		if (Files\Json::isJsonBug()) {
			Assert::same(Testinium\File::load('custom71.srlz'), serialize($list));	
		} else {
			Assert::same(Testinium\File::load('custom.srlz'), serialize($list));
		}
	}

}

/**
 * @property-read float $amount [1]
 * @property-read string $to_account [2]
 * @property-read string $bank_code [3]
 */
class MyTransactionTest extends TransactionAbstract
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

$fioFactory = new Test\FioFactory('h4kuna\Fio\Response\Read\MyTransactionTest');
(new JsonStatementFactoryTest($fioFactory))->run();
