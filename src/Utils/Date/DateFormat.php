<?php

namespace h4kuna\Fio\Utils\Date;

use h4kuna\Fio\Utils;

/**
 * @author Milan Matějček
 */
class DateFormat
{

	/** @var string */
	private $format;

	public function __construct($format)
	{
		$this->format = $format;
	}

	/** @return string */
	public function getFormat()
	{
		return $this->format;
	}

	/** @return \DateTime */
	public function createDateTime($value)
	{
		return Utils\String::createFromFormat($value, $this->getFormat());
	}

}
