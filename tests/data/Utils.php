<?php

namespace h4kuna\Fio\Test;

class Utils
{

	public static function getPathData($file)
	{
		return __DIR__ . '/tests/' . $file;
	}

	public static function getContent($file)
	{
		return file_get_contents(self::getPathData($file));
	}

	public static function saveFile($name, $content)
	{
		file_put_contents(self::getPathData($name), $content);
	}

}
