<?php declare(strict_types=1);

namespace h4kuna\Fio\Read;

use h4kuna\Fio\Exceptions\InvalidArgument;
use h4kuna\Fio\Utils\Fio;
use h4kuna\Memoize\MemoryStorage;

class TransactionFactory
{
	use MemoryStorage;

	public function create(\stdClass $source): object
	{
		$transaction = $this->createTransaction();

		// keep original data
		if (property_exists($transaction, 'original')) {
			$transaction->original = $source;
		}

		/** @var array<string, \ReflectionProperty> $map */
		$map = $this->memoize($transaction::class, static fn (): array => self::createMapping($transaction::class));

		foreach ($map as $column => $property) {
			$propertyName = $property->getName();
			$type = $property->getType();
			assert($type instanceof \ReflectionNamedType);

			$value = $source->$column->value ?? null;
			$method = 'set' . ucfirst($propertyName);
			if (method_exists($transaction, $method)) {
				$transaction->$method($value);
			} elseif (property_exists($transaction, $propertyName)) {
				$transaction->$propertyName = $this->castValue($value, $type);
			} else {
				throw new InvalidArgument(sprintf('Missing property "%s" or method "%s" for set value.', $propertyName, $method));
			}
		}

		return $this->backCompatibility($transaction);
	}


	protected function createTransaction(): object
	{
		return new Transaction();
	}


	/**
	 * @param class-string $transaction
	 * @return array<string, \ReflectionProperty>
	 */
	private static function createMapping(string $transaction): array
	{
		$class = new \ReflectionClass($transaction);
		$properties = $class->getProperties(\ReflectionProperty::IS_PUBLIC);
		$map = [];
		foreach ($properties as $property) {
			$attribute = $property->getAttributes(Column::class, \ReflectionAttribute::IS_INSTANCEOF);
			if ($attribute === []) {
				continue;
			}
			$id = $attribute[0]->getArguments()['id'];
			$map["column$id"] = $property;
		}

		return $map;
	}


	/**
	 * @param scalar|null $value
	 */
	private function castValue($value, \ReflectionNamedType $type): mixed
	{
		if ($type->allowsNull() && $value === null) {
			return null;
		}

		if ($type->isBuiltin()) {
			settype($value, $type->getName());

			return $value;
		}

		if ($type->getName() === \DateTimeImmutable::class) {
			return Fio::toDate(strval($value));
		}

		return $this->customFormat($value, $type);
	}


	/**
	 * @param scalar|null $value
	 */
	protected function customFormat($value, \ReflectionNamedType $type): mixed
	{
		throw new InvalidArgument(sprintf('Values "%s" does not have support type "%s".', strval($value), $type->getName()));
	}


	/**
	 * @template T of object
	 * @param T $transaction
	 * @return T
	 */
	protected function backCompatibility(object $transaction): object
	{
		assert($transaction instanceof Transaction);
		$transaction->volume = $transaction->amount;

		return $transaction;
	}

}
