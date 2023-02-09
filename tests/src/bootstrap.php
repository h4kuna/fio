<?php declare(strict_types=1);

namespace h4kuna\Fio\Tests;

use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Nette\Utils\Strings;
use Tracy;
use Tester;

require __DIR__ . '/../../vendor/autoload.php';

if (defined('__PHPSTAN_RUNNING__')) {
	return;
}

date_default_timezone_set('Europe/Prague');

Tester\Environment::setup();

/**
 * @param mixed $save
 * @return mixed
 */
function loadResult(string $name, $save = null)
{
	$raw = false;
	if (Strings::startsWith($name, 'raw://')) {
		$name = substr($name, 6);
		$raw = true;
	}

	$extension = pathinfo($name, PATHINFO_EXTENSION);

	$file = FileSystem::isAbsolute($name) ? $name : __DIR__ . "/../data/$name";

	if ($save !== null) {
		file_put_contents($file,
			match ($extension) {
				'json' => Json::encode($save),
				'srlz' => serialize($save),
				default => $save,
			},
		);
	}

	$content = file_get_contents($file);
	assert($content !== false);
	if ($raw) {
		return $content;
	}

	return match ($extension) {
		'json' => Json::decode($content),
		'srlz' => unserialize($content),
		default => $content,
	};
}

Tracy\Debugger::enable(false, __DIR__ . '/../temp');
