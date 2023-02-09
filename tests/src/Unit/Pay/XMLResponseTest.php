<?php declare(strict_types=1);

namespace h4kuna\Fio\Tests\Unit\Pay;

use h4kuna\Fio\Pay\XMLResponse;
use h4kuna\Fio\Tests\Fixtures\TestCase;
use Tester\Assert;
use function h4kuna\Fio\Tests\loadResult;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class XMLResponseTest extends TestCase
{

	public function testResponse(): void
	{
		$xml = loadResult('payment/response.xml');
		assert(is_string($xml));
		$xmlResponse = new XMLResponse($xml);
		Assert::true($xmlResponse->isOk());
		Assert::equal('1247458', $xmlResponse->getIdInstruction());
	}


	public function testErrorResponse(): void
	{
		$xml = loadResult('payment/response-error.xml');
		assert(is_string($xml));
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
