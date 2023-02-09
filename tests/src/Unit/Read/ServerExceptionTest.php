<?php declare(strict_types=1);

namespace h4kuna\Fio\Tests\Unit\Read;

use h4kuna\Fio\Pay\XMLResponse;
use h4kuna\Fio\Tests\Fixtures\TestCase;
use Tester\Assert;
use function h4kuna\Fio\Tests\loadResult;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class ServerExceptionTest extends TestCase
{

	public function testResponse(): void
	{
		$xml = loadResult('server-exception.xml');
		assert(is_string($xml));
		$xmlResponse = new XMLResponse($xml);
		Assert::false($xmlResponse->isOk());
	}

}

(new ServerExceptionTest())->run();
