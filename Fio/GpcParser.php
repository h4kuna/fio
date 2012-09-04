<?php

namespace Fio;

/**
 * return utf-8 array
 */
class GpcParser extends \Nette\Object implements \Iterator
{
	/**
	 * @var \Iterator
	 */
	private $data;
	private $filter;
	private $current;

	const BOTH = NULL;
	const REPORT = '074';
	const ITEM = '075';

	public function __construct($source, $filter = self::BOTH)
	{
		$this->filter = $filter;

		if (file_exists($source)) {
			$this->data = new \SplFileObject($source);
			$this->data->setFlags(6); //bug konstanta \SplFileObject::SKIP_EMPTY vraci 4 uz to bylo reportovane
		} else {
			$this->data = new \Utility\TextIterator($source);
		}
	}

	public function rewind()
	{
		$this->current = NULL;
		$this->data->rewind();
	}

	public function next()
	{
		$this->current = NULL;
		return $this->data->next();
	}

	public function valid()
	{
		do {
			$valid = $this->data->valid();
			if ($valid && $this->current() === NULL) {
				$this->next();
			} else {
				break;
			}
		} while ($valid);

		return $valid;
	}

	public function key()
	{
		return $this->data->key();
	}

	public function current()
	{
		if ($this->current === NULL) {
			$this->current = $this->parseLine(iconv('windows-1250', 'utf-8', trim($this->data->current())));
		}
		return $this->current;
	}

	public function arrayCopy()
	{
		$a = array();
		foreach ($this as $k => $v) {
			$a[$k] = $v;
		}
		return $a;
	}

	private function parseLine($data)
	{
		$type = mb_substr($data, 0, 3);

		if ($type == $this->filter || $this->filter === self::BOTH) {
			if ($type == self::REPORT) {
				return $this->report($data);
			} elseif ($type == self::ITEM) {
				return $this->item($data);
			}
		}

		return NULL;// sem by se to nemělo dostat
	}

	private function report($data)
	{
		return array(
				'type' => self::REPORT,
				'accountNumber' => $this->ltrim(mb_substr($data, 3, 16)),
				'accountName' => rtrim(mb_substr($data, 19, 20)),
				'oldBalanceDate' => $this->dateTime(mb_substr($data, 39, 6)),
				'oldBalanceValue' => $this->floatVal(mb_substr($data, 45, 14), mb_substr($data, 59, 1)),
				'newBalanceValue' => $this->floatVal(mb_substr($data, 60, 14), mb_substr($data, 74, 1)),
				'debitValue' => $this->floatVal(mb_substr($data, 75, 14), mb_substr($data, 89, 1)),
				'creditValue' => $this->floatVal(mb_substr($data, 90, 14), mb_substr($data, 104, 1)),
				'sequenceNumber' => intval(mb_substr($data, 105, 3)),
				'date' => $this->dateTime(mb_substr($data, 108, 6)),
				'note' => substr($data, 116, 12),
				'checkSum' => $this->sum($data),
		);
	}

	private function item($data)
	{
		return array(
				'type' => self::ITEM,
				'accountNumber' => $this->ltrim(mb_substr($data, 3, 16)),
				'offsetAccount' => $this->ltrim(rtrim(mb_substr($data, 19, 16))),
				'recordNumber' => $this->ltrim(mb_substr($data, 35, 13)),
				'value' => $this->floatVal(mb_substr($data, 48, 12)),
				'code' => mb_substr($data, 60, 1),
				'variableSymbol' => intval((mb_substr($data, 61, 10))),
				'bankCode' => mb_substr($data, 73, 4), //zkontrolovat
				'constantSymbol' => $this->ltrim(mb_substr($data, 77, 4)),
				'specificSymbol' => intval(mb_substr($data, 81, 10)),
				'valut' => mb_substr($data, 91, 6),
				'clientName' => rtrim(mb_substr($data, 97, 20)),
				//'zero' => mb_substr($data, 117, 1),
				'currencyCode' => mb_substr($data, 118, 4),
				'dueDate' => $this->dateTime(mb_substr($data, 122, 6)),
				'checkSum' => $this->sum($data),
		);
	}

	private function dateTime($s)
	{
		$d = str_split($s, 2);
		$dt = new \DateTime('midnight');
		$dt->setDate(2000 + $d[2], $d[1], $d[0]);
		return $dt;
	}

	private function floatVal($num, $mark = '+')
	{
		$v = intval($num);
		if ($mark == '-') {
			$v *= -1;
		}
		return $v / 100;
	}

	private function sum($data)
	{
		return sha1(md5($data) . $data);
	}

	private function ltrim($s)
	{
		return ltrim($s, '0');
	}

}
