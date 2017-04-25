<?php

namespace h4kuna\Fio;

use Tester,
	Tester\Assert,
	Salamium\Testinium;

$container = require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @author Milan Matějček
 */
class XMLResponseTest extends Tester\TestCase
{

	public function testResponse()
	{
		$xml = Testinium\File::load('payment/response.xml');
		$xmlResponse = new Response\Pay\XMLResponse($xml);
		Assert::true($xmlResponse->isOk());
		Assert::equal('1247458', $xmlResponse->getIdInstruction());
	}

}

(new XMLResponseTest())->run();
