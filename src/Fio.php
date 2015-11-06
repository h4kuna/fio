<?php

namespace h4kuna\Fio;

use Nette\Object;

/**
 * @author Milan Matějček
 */
class Fio extends Object
{

	/** @var string url Fio REST API */
	const REST_URL = 'https://www.fio.cz/ib_api/rest/';

	/** @var string */
	const FIO_API_VERSION = '1.4.1';

	/** @var Utils\Context */
	protected $context;

	private function __construct(Utils\Context $context)
	{
		$this->context = $context;
	}

	/**
	 * @param atring $alias
	 * @return self
	 */
	public function setAccount($alias)
	{
		$this->context->getAccounts()->setActive($alias);
		return $this;
	}

}
