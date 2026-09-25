<?php declare(strict_types = 1);

namespace h4kuna\Fio\Tests;

use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Tester\Environment;
use Tracy\Debugger;
use function assert;
use function date_default_timezone_set;
use function defined;
use function file_get_contents;
use function file_put_contents;
use function pathinfo;
use function serialize;
use function str_starts_with;
use function substr;
use function unserialize;
use const PATHINFO_EXTENSION;

require __DIR__ . '/../../vendor/autoload.php';

if (defined('__PHPSTAN_RUNNING__')) {
	return;
}

date_default_timezone_set('Europe/Prague');

Environment::setup();

/**
 * @param mixed $save
 */
function loadResult(string $name, $save = null): mixed
{
	$raw = false;
	if (str_starts_with($name, 'raw://')) {
		$name = substr($name, 6);
		$raw = true;
	}

	$extension = pathinfo($name, PATHINFO_EXTENSION);

	$file = FileSystem::isAbsolute($name) ? $name : __DIR__ . "/../data/$name";

	if ($save !== null) {
		file_put_contents(
			$file,
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

Debugger::enable(false, __DIR__ . '/../temp');
