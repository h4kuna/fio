<?php declare(strict_types=1);

namespace h4kuna\Fio\Response\Read;

use h4kuna\Fio\Exceptions;
use h4kuna\Fio\Utils;

class JsonTransactionFactory implements ITransactionListFactory
{
	/** @var string[][] */
	private $property;

	/** @var string */
	private $transactionClass;

	/** @var TransactionAbstract */
	private $transactionObject;


	public function __construct(string $transactionClass)
	{
		$this->transactionClass = $transactionClass;
	}


	public function createInfo(\stdClass $data, string $dateFormat): \stdClass
	{
		$data->dateStart = Utils\Strings::createFromFormat($data->dateStart, $dateFormat);
		$data->dateEnd = Utils\Strings::createFromFormat($data->dateEnd, $dateFormat);
		return $data;
	}


	public function createTransaction(\stdClass $data, string $dateFormat): TransactionAbstract
	{
		$transaction = $this->createTransactionObject($dateFormat);
		foreach ($this->metaProperty($transaction) as $id => $meta) {
			$value = isset($data->{'column' . $id}) ? $data->{'column' . $id}->value : null;
			$transaction->bindProperty($meta['name'], $meta['type'], $value);
		}
		return $transaction;
	}


	public function createTransactionList(\stdClass $info): TransactionList
	{
		return new TransactionList($info);
	}


	protected function createTransactionObject(string $dateFormat): TransactionAbstract
	{
		if ($this->transactionObject === null) {
			$class = $this->transactionClass;
			$this->transactionObject = new $class($dateFormat);

			if (!$this->transactionObject instanceof TransactionAbstract) {
				throw new Exceptions\Runtime(sprintf('Transaction class must extends "%s".', TransactionAbstract::class));
			}
		}

		return clone $this->transactionObject;
	}


	/**
	 * @return string[][]
	 */
	private function metaProperty(TransactionAbstract $class): array
	{
		if ($this->property !== null) {
			return $this->property;
		}
		$reflection = new \ReflectionClass($class);
		if ($reflection->getDocComment() === false || !preg_match_all('/@property-read (?P<type>[\w|]+) \$(?P<name>\w+).*\[(?P<id>\d+)\]/', $reflection->getDocComment(), $find)) {
			throw new Exceptions\Runtime('Property has bad annotation syntax.');
		}

		$this->property = [];
		foreach ($find['name'] as $key => $property) {
			$this->property[$find['id'][$key]] = ['type' => strtolower($find['type'][$key]), 'name' => $property];
		}
		return $this->property;
	}

}
