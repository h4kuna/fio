<?php declare(strict_types=1);

namespace h4kuna\Fio\Response\Pay;

use Salamium\Testinium;
use Tester;
use Tester\Assert;

require __DIR__ . '/../../../bootstrap.php';

class XMLResponseTest extends Tester\TestCase
{

	public function testResponse()
	{
		$xml = Testinium\File::load('payment/response.xml');
		$xmlResponse = new XMLResponse($xml);
		Assert::true($xmlResponse->isOk());
		Assert::equal('1247458', $xmlResponse->getIdInstruction());
	}


	public function testErrorResponse()
	{
		$xml = Testinium\File::load('payment/response-error.xml');
		$xmlResponse = new XMLResponse($xml);
		Assert::false($xmlResponse->isOk());

		Assert::equal('error', $xmlResponse->status());
		Assert::equal(1, $xmlResponse->code());

		Assert::equal([
			303 => 'Chybný formát zadaného IBAN.',
			323 => 'Adresa majitele účtu není kompletní',
			'foo',
		], $xmlResponse->errorMessages());

		$xmlFile = __DIR__ . '/../../../temp/out-test-xml';
		$xmlResponse->saveXML($xmlFile);
		Assert::same($xml, file_get_contents($xmlFile));
	}

}

(new XMLResponseTest())->run();
