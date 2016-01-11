<?php

namespace h4kuna\Fio\Test;

/**
 * @author Milan Matějček
 */
class FioFactory extends \h4kuna\Fio\Utils\FioFactory
{

	public function createQueue()
	{
		return new Queue;
	}

}
