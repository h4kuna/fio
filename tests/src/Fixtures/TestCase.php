<?php declare(strict_types=1);

namespace h4kuna\Fio\Tests\Fixtures;

use Tester;

abstract class TestCase extends Tester\TestCase
{

	public function run(): void
	{
		if (\defined('__PHPSTAN_RUNNING__')) {
			return;
		}

		parent::run();
	}

}
