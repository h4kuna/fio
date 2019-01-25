<?php declare(strict_types=1);

namespace h4kuna\Fio\Test;

use h4kuna\Fio\Request\IQueue;
use h4kuna\Fio\Request\Pay\XMLFile;
use h4kuna\Fio\Request\Read\Files\Json;
use h4kuna\Fio\Response\Read\Transaction;

class FioFactory extends \h4kuna\Fio\Utils\FioFactory
{

	public function __construct(string $transactionClass = Transaction::class)
	{
		$accounts = [
			'foo' => [
				'account' => '123456789',
				'token' => 'abcdefgh',
			],
			'bar' => [
				'account' => '987654321',
				'token' => 'hgfedcba',
			],
		];
		parent::__construct($accounts, $transactionClass);
	}


	public function getXmlFile(): XMLFile
	{
		return $this->createXmlFile();
	}


	public function getReader(): Json
	{
		return $this->createReader();
	}


	protected function createQueue(): IQueue
	{
		return new Queue;
	}

}
