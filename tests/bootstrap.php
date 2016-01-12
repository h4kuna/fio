<?php

include __DIR__ . "/../vendor/autoload.php";

require_once __DIR__ . '/data/Queue.php';
require_once __DIR__ . '/data/Utils.php';
require_once __DIR__ . '/data/FioFactory.php';

function dd($var /* ... */)
{
	foreach (func_get_args() as $arg) {
		\Tracy\Debugger::dump($arg);
	}
	exit;
}

date_default_timezone_set('Europe/Prague');
Tester\Environment::setup();
\Tracy\Debugger::enable(FALSE, __DIR__ . '/temp');
