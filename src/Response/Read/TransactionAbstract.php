<?php declare(strict_types=1);

namespace h4kuna\Fio\Response\Read;

use h4kuna\Fio\Exceptions;
use h4kuna\Fio\Utils;

/**
 * @implements \Iterator<string, mixed>
 */
abstract class TransactionAbstract implements \Iterator
{
	/** @var array<string, mixed> */
	private array $properties = [];

	protected string $dateFormat;


	public function __construct(string $dateFormat)
	{
		$this->dateFormat = $dateFormat;
	}


	/**
	 * @return mixed
	 */
	public function __get(string $name)
	{
		if (array_key_exists($name, $this->properties)) {
			return $this->properties[$name];
		}
		throw new Exceptions\Runtime('Property does not exists. ' . $name);
	}


	public function clearTemporaryValues(): void
	{
		$this->dateFormat = '';
	}


	public function bindProperty(string $name, string $type, mixed $value): void
	{
		$method = 'set' . ucfirst($name);
		if (method_exists($this, $method)) {
			$value = $this->{$method}($value);
		} elseif ($value !== null) {
			$value = $this->checkValue($value, $type);
		}
		$this->properties[$name] = $value;
	}


	#[\ReturnTypeWillChange]
	public function current(): mixed
	{
		return current($this->properties);
	}


	/**
	 * @return string
	 */
	#[\ReturnTypeWillChange]
	public function key()
	{
		$key = key($this->properties);
		if ($key === NULL) {
			throw new Exceptions\InvalidState('Key cold\'nt be null.');
		}
		return $key;
	}


	#[\ReturnTypeWillChange]
	public function next()
	{
		next($this->properties);
	}


	#[\ReturnTypeWillChange]
	public function rewind()
	{
		reset($this->properties);
	}


	#[\ReturnTypeWillChange]
	public function valid()
	{
		$key = key($this->properties);
		if ($key === null) {
			return false;
		}

		return array_key_exists($key, $this->properties);
	}


	/** @return array<string, mixed> */
	public function getProperties(): array
	{
		return $this->properties;
	}


	protected function checkValue(mixed $value, string $type): mixed
	{
		return match ($type) {
			'datetime' => is_string($value) ? Utils\Strings::createFromFormat($value, $this->dateFormat) : $value,
			'float' => floatval($value),
			'string' => is_string($value) ? trim($value) : $value,
			'int' => intval($value),
			'string|null' => is_string($value) ? (trim($value) ?: null) : $value,
			default => $value,
		};
	}

}
