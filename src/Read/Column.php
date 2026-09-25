<?php declare(strict_types = 1);

namespace h4kuna\Fio\Read;

use Attribute;

#[Attribute]
final class Column
{

	public function __construct(public int $id)
	{
	}

}
