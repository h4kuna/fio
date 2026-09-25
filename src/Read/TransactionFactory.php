<?php declare(strict_types = 1);

namespace h4kuna\Fio\Read;

use DateTimeImmutable;
use h4kuna\Fio\Exceptions\InvalidArgument;
use h4kuna\Fio\Utils\Fio;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use stdClass;
use function assert;
use function get_debug_type;
use function is_scalar;
use function method_exists;
use function property_exists;
use function settype;
use function sprintf;
use function strval;
use function ucfirst;

class TransactionFactory
{

	/**
	 * @var array<class-string, array<string, ReflectionProperty>>
	 */
	private array $mapping = [];


	public function create(stdClass $source): object
	{
		$transaction = $this->createTransaction();

		// keep original data
		if (property_exists($transaction, 'original')) {
			$transaction->original = $source;
		}

		$map = $this->mapping[$transaction::class] ??= self::createMapping($transaction::class);

		foreach ($map as $column => $property) {
			$propertyName = $property->getName();
			$type = $property->getType();
			assert($type instanceof ReflectionNamedType);

			$value = self::columnValue($source, $column);
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
	 * @return array<string, ReflectionProperty>
	 */
	private static function createMapping(string $transaction): array
	{
		$class = new ReflectionClass($transaction);
		$properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);
		$map = [];
		foreach ($properties as $property) {
			$attribute = $property->getAttributes(Column::class, ReflectionAttribute::IS_INSTANCEOF);
			if ($attribute === []) {
				continue;
			}
			$column = $attribute[0]->newInstance();
			$map["column$column->id"] = $property;
		}

		return $map;
	}

	/**
	 * @return scalar|null
	 */
	private static function columnValue(
		stdClass $source,
		string $column,
	): mixed
	{
		$data = $source->$column ?? null;
		if (!$data instanceof stdClass || !isset($data->value)) {
			return null;
		}

		$value = $data->value;
		if (!is_scalar($value)) {
			throw new InvalidArgument(sprintf('Column "%s" has unsupported value type "%s".', $column, get_debug_type($value)));
		}

		return $value;
	}

	/**
	 * @param scalar|null $value
	 */
	private function castValue(
		$value,
		ReflectionNamedType $type,
	): mixed
	{
		if ($type->allowsNull() && $value === null) {
			return null;
		}

		if ($type->isBuiltin()) {
			settype($value, $type->getName());

			return $value;
		}

		if ($type->getName() === DateTimeImmutable::class) {
			return Fio::toDate(strval($value));
		}

		return $this->customFormat($value, $type);
	}

	/**
	 * @param scalar|null $value
	 */
	protected function customFormat(
		$value,
		ReflectionNamedType $type,
	): mixed
	{
		throw new InvalidArgument(sprintf('Values "%s" does not have support type "%s".', strval($value), $type->getName()));
	}

	/**
	 * @param T $transaction
	 * @return T
	 *
	 * @template T of object
	 */
	protected function backCompatibility(object $transaction): object
	{
		if ($transaction instanceof Transaction) {
			$transaction->volume = $transaction->amount; // @phpstan-ignore property.deprecated (kept for backward compatibility)
		}

		return $transaction;
	}

}
