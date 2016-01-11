<?php

namespace h4kuna\Fio\Utils;

use h4kuna\Fio,
	Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

class FioFactoryTest extends \Tester\TestCase
{

	/** @var FioFactory */
	private $fioFactory;

	public function __construct(FioFactory $fioFactory)
	{
		$this->fioFactory = $fioFactory;
	}

	public function testInstanceAll()
	{
		Assert::true($this->fioFactory->createDateFormatOriginal() instanceof Date\DateFormatOriginal);
		Assert::true($this->fioFactory->createQueue() instanceof Fio\Request\IQueue);
		Assert::true($this->fioFactory->createTransactionListFactory() instanceof Fio\Response\Read\ITransactionListFactory);
		Assert::true($this->fioFactory->createAccounts(['foo' => ['token' => 'a654s', 'account' => '123456']]) instanceof Fio\Account\Accounts);
	}

}

(new FioFactoryTest(new FioFactory()))->run();
