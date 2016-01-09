<?php

namespace h4kuna\Fio\Response\Read;

use h4kuna\Fio,
	h4kuna\Fio\Utils;

/**
 * @author Milan Matějče
 */
class JsonStatementFactory implements IStatementFactory
{

	/** @var string[] */
	private static $property;

	/** @var string */
	private $transactionClass;

	public function __construct($transactionClass = NULL)
	{
		if ($transactionClass === NULL) {
			$transactionClass = __NAMESPACE__ . '\Transaction';
		}
		$this->transactionClass = $transactionClass;
	}

	public function createInfo($data, $dateFormat)
	{
		$data->dateStart = Utils\Strings::createFromFormat($data->dateStart, $dateFormat);
		$data->dateEnd = Utils\Strings::createFromFormat($data->dateEnd, $dateFormat);
		return $data;
	}

	public function createTransaction($data, $dateFormat)
	{
		$transaction = $this->createTransactionObject($dateFormat);
		foreach (self::metaProperty($transaction) as $id => $meta) {
			$value = isset($data->{'column' . $id}) ? $data->{'column' . $id}->value : NULL;
			$transaction->bindProperty($meta['name'], $meta['type'], $value);
		}
		return $transaction;
	}

	/** @return TransactionList */
	public function createTransactionList($info)
	{
		return new TransactionList($info);
	}

	public function createParser()
	{
		return new Fio\Request\Read\Files\Json($this);
	}

	protected function createTransactionObject($dateFormat)
	{
		if (is_string($this->transactionClass)) {
			$class = $this->transactionClass;
			$this->transactionClass = new $class($dateFormat);
			if (!$this->transactionClass instanceof ATransaction) {
				throw new Utils\FioException('Transaction class must extends ATransation.');
			}
		}

		return clone $this->transactionClass;
	}

	private static function metaProperty($class)
	{
		if (self::$property !== NULL) {
			return self::$property;
		}
		$reflection = new \ReflectionClass($class);
		if (!preg_match_all('/@property-read (?P<type>[\w|]+) \$(?P<name>\w+).*\[(?P<id>\d+)\]/', $reflection->getDocComment(), $find)) {
			throw new Utils\FioException('Property not found you have bad syntax.');
		}

		self::$property = array();
		foreach ($find['name'] as $key => $property) {
			self::$property[$find['id'][$key]] = array('type' => strtolower($find['type'][$key]), 'name' => $property);
		}
		return self::$property;
	}

}
