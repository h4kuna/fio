<?php

namespace h4kuna\Fio\Response\Read;

use h4kuna\Fio\Utils;

/**
 * @author Milan Matějček
 */
abstract class ATransaction implements \Iterator
{

	/** @var array */
	private $properties = array();

	/** @var string */
	private static $dateFormat;

	public function __construct($dateFormat)
	{
		self::$dateFormat = $dateFormat;
	}

	public function __get($name)
	{
		if (array_key_exists($name, $this->properties)) {
			return $this->properties[$name];
		}
		throw new Utils\FioException('Property does not exists. ' . $name);
	}

	public function bindProperty($name, $type, $value)
	{
		$method = 'set' . ucfirst($name);
		if (method_exists($this, $method)) {
			$value = $this->{$method}($value);
		} elseif ($value !== NULL) {
			$value = $this->checkValue($value, $type);
		}
		$this->properties[$name] = $value;
	}

	public function current()
	{
		return current($this->properties);
	}

	public function key()
	{
		return key($this->properties);
	}

	public function next()
	{
		next($this->properties);
	}

	public function rewind()
	{
		reset($this->properties);
	}

	public function valid()
	{
		return array_key_exists($this->key(), $this->properties);
	}

	/** @return array */
	public function getProperties()
	{
		return $this->properties;
	}

	/** @return string */
	protected function getDateFormat()
	{
		return self::$dateFormat;
	}

	protected function checkValue($value, $type)
	{
		switch ($type) {
			case 'int':
				if(PHP_VERSION_ID < 54000) {
					return $value + 0;
				}
				return intval($value);
			case 'datetime':
				return Utils\String::createFromFormat($value, $this->getDateFormat());
			case 'float':
				return floatval($value);
			case 'string':
				return trim($value);
			case 'string|null':
				return trim($value) ? : NULL;
		}
		return $value;
	}

}
