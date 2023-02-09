<?php declare(strict_types=1);

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


	/**
	 * @throws \h4kuna\Fio\Exceptions\InvalidArgument
	 */
	public function testCheckThrow(): void
	{
		Assert::same('Č', InvalidArgument::check('Če', 1));
	}


	public function testCheckRange(): void
	{
		Assert::same(10, InvalidArgument::checkRange(10, 99));
	}


	/**
	 * @throws \h4kuna\Fio\Exceptions\InvalidArgument
	 */
	public function testCheckRangeThrow(): void
	{
		Assert::same(10, InvalidArgument::checkRange(10, 1));
	}


	public function testCheckIsInList(): void
	{
		Assert::same('foo', InvalidArgument::checkIsInList('foo', ['foo']));
	}


	/**
	 * @throws \h4kuna\Fio\Exceptions\InvalidArgument
	 */
	public function testCheckIsInListThrow(): void
	{
		InvalidArgument::checkIsInList('bar', ['foo']);
	}


	public function testCheckLength(): void
	{
		Assert::same('foo', InvalidArgument::checkLength('foo', 3));
	}


	/**
	 * @throws \h4kuna\Fio\Exceptions\InvalidArgument
	 */
	public function testCheckLengthThrow(): void
	{
		InvalidArgument::checkLength('bar', 4);
	}

}

(new InvalidArgumentExceptionTest())->run();
