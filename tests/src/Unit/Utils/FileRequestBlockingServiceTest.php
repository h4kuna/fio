<?php declare(strict_types=1);

namespace h4kuna\Fio\Tests\Unit\Utils;

use Closure;
use GuzzleHttp\Psr7\Response;
use h4kuna;
use h4kuna\Fio\Tests\Fixtures\TestCase;
use h4kuna\Fio\Utils\FileRequestBlockingService;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class FileRequestBlockingServiceTest extends TestCase
{
	/**
	 * @return array<string|int, array{0: Closure(static):void}>
	 */
	public static function data(): array
	{
		return [
			[static function (self $self) {
				$self->assert();
			}],
		];
	}


	/**
	 * @param Closure(static):void $assert
	 *
	 * @dataProvider data
	 */
	public function testBasic(Closure $assert): void
	{
		$assert($this);
	}

	public function assert(): void
	{
		@unlink(__DIR__ . '/../../../temp/blocking/d41d8cd98f00b204e9800998ecf8427e');
		$waitTime = 2;
		$start = time();
		$fileRequest = new FileRequestBlockingService((new h4kuna\Dir\Dir(__DIR__ . '/../../../temp/blocking'))->create(), $waitTime);
		$fileRequest->synchronize('0123456789abcd', fn () => new Response());
		$fileRequest->synchronize('0123456789abcd', fn () => new Response(201));
		$end = time();

		Assert::same($waitTime, $end - $start);
	}
}


(new FileRequestBlockingServiceTest())->run();
