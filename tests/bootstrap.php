<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('Europe/Prague');

if (!\defined('__PHPSTAN_RUNNING__')) {
	Tester\Environment::setup();
}

\Tracy\Debugger::enable(false, __DIR__ . '/temp');
