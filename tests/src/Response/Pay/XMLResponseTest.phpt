<?php

namespace h4kuna\Fio;

use Tester,
	Tester\Assert,
	h4kuna\Fio\Test;

$container = require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @author Milan Matějček
 */
class XMLResponseTest extends Tester\TestCase
{

	public function testResponse()
	{
		$xml = Test\Utils::getContent('payment/response.xml');
		$xmlResponse = new Response\Pay\XMLResponse($xml);
		Assert::true($xmlResponse->isOk());
		Assert::equal('1247458', $xmlResponse->getIdInstruction());
	}

}

(new XMLResponseTest())->run();
