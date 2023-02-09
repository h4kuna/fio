<?php declare(strict_types=1);

namespace h4kuna\Fio\Tests\Fixtures;

use h4kuna\Fio\Pay\XMLFile;
use h4kuna\Fio\Read\Json;

class FioFactory extends \h4kuna\Fio\FioFactory
{

	public function __construct()
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
		parent::__construct($accounts);
	}


	public function getXmlFile(): XMLFile
	{
		return $this->createXmlFile();
	}


	public function getReader(): Json
	{
		return $this->createReader();
	}


	protected function createQueue(): Queue
	{
		return new Queue();
	}

}
