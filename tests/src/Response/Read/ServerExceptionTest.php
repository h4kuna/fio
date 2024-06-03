<?php declare(strict_types=1);

namespace h4kuna\Fio\Response\Read;

use h4kuna\Fio\Response\Pay\XMLResponse;
use h4kuna\Fio\Test;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @author Martin Pecha
 * @testCase
 */
final class ServerExceptionTest extends Test\TestCase
{

	public function testResponse(): void
	{
		$xml = file_get_contents(__DIR__ . '/../../../data/tests/server-exception.xml');
		$xmlResponse = new XMLResponse($xml);
		Assert::false($xmlResponse->isOk());
	}

}

(new ServerExceptionTest())->run();
