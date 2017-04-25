<?php

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/data/Queue.php';
require __DIR__ . '/data/FioFactory.php';

Salamium\Testinium\File::setRoot(__DIR__ . '/data/tests');

date_default_timezone_set('Europe/Prague');
Tester\Environment::setup();
\Tracy\Debugger::enable(FALSE, __DIR__ . '/temp');
