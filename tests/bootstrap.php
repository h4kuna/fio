<?php

include __DIR__ . "/../vendor/autoload.php";

require_once __DIR__ . '/data/Queue.php';

function dd($var /* ... */)
{
    \Tracy\Debugger::enable(FALSE);
    foreach (func_get_args() as $arg) {
        \Tracy\Debugger::dump($arg);
    }
    exit;
}

Tester\Environment::setup();

// 2# Create Nette Configurator
$configurator = new Nette\Configurator;

$tmp = __DIR__ . '/temp/' . php_sapi_name();
Nette\Utils\FileSystem::createDir($tmp, 0755);
$configurator->enableDebugger($tmp);
$configurator->setTempDirectory($tmp);
$configurator->setDebugMode(FALSE);
$configurator->addConfig(__DIR__ . '/test.neon');
$local = __DIR__ . '/test.local.neon';
if (is_file($local)) {
    $configurator->addConfig($local);
}
$container = $configurator->createContainer();

return $container;



