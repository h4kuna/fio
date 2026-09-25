<?php declare(strict_types = 1);

namespace h4kuna\Fio\Tests\Fixtures;

use Tester\TestCase as TesterTestCase;
use function defined;

abstract class TestCase extends TesterTestCase
{

	public function run(): void
	{
		if (defined('__PHPSTAN_RUNNING__')) {
			return;
		}

		parent::run();
	}

}
