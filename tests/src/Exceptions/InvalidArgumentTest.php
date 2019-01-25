<?php declare(strict_types=1);

namespace h4kuna\Fio\Exceptions;

use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

class InvalidArgumentTest extends \Tester\TestCase
{

	public function testCheck()
	{
		Assert::same('Č', InvalidArgument::check('Č', 1));
	}


	/**
	 * @throws \h4kuna\Fio\Exceptions\InvalidArgument
	 */
	public function testCheckThrow()
	{
		Assert::same('Č', InvalidArgument::check('Če', 1));
	}


	public function testCheckRange()
	{
		Assert::same(10, InvalidArgument::checkRange(10, 99));
	}


	/**
	 * @throws \h4kuna\Fio\Exceptions\InvalidArgument
	 */
	public function testCheckRangeThrow()
	{
		Assert::same(10, InvalidArgument::checkRange(10, 1));
	}


	public function testCheckIsInList()
	{
		Assert::same('foo', InvalidArgument::checkIsInList('foo', ['foo']));
	}


	/**
	 * @throws \h4kuna\Fio\Exceptions\InvalidArgument
	 */
	public function testCheckIsInListThrow()
	{
		InvalidArgument::checkIsInList('bar', ['foo']);
	}


	public function testCheckLength()
	{
		Assert::same('foo', InvalidArgument::checkLength('foo', 3));
	}


	/**
	 * @throws \h4kuna\Fio\Exceptions\InvalidArgument
	 */
	public function testCheckLengthThrow()
	{
		InvalidArgument::checkLength('bar', 4);
	}

}

(new InvalidArgumentTest())->run();
