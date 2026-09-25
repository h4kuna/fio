<?php declare(strict_types = 1);

namespace h4kuna\Fio\Tests\Unit\Exceptions;

use h4kuna\Fio\Exceptions\InvalidArgument;
use h4kuna\Fio\Tests\Fixtures\TestCase;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class InvalidArgumentExceptionTest extends TestCase
{

	public function testCheck(): void
	{
		Assert::same('Č', InvalidArgument::check('Č', 1));
	}

	public function testCheckThrow(): void
	{
		Assert::exception(static fn () => InvalidArgument::check('Če', 1), InvalidArgument::class);
	}

	public function testCheckRange(): void
	{
		InvalidArgument::checkRange(10, 99);
		Assert::true(true);
	}

	public function testCheckRangeThrow(): void
	{
		Assert::exception(static fn () => InvalidArgument::checkRange(10, 1), InvalidArgument::class);
	}

	public function testCheckIsInList(): void
	{
		Assert::same('foo', InvalidArgument::checkIsInList('foo', ['foo']));
	}

	public function testCheckIsInListThrow(): void
	{
		Assert::exception(static fn () => InvalidArgument::checkIsInList('bar', ['foo']), InvalidArgument::class);
	}

	public function testCheckLength(): void
	{
		Assert::same('foo', InvalidArgument::checkLength('foo', 3));
	}

	public function testCheckLengthThrow(): void
	{
		Assert::exception(static fn () => InvalidArgument::checkLength('bar', 4), InvalidArgument::class);
	}

}

(new InvalidArgumentExceptionTest())->run();
