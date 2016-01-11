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

Tester\Environment::setup();