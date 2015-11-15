<?php

namespace h4kuna\Fio;

use h4kuna\Fio\Request\Read\IParser,
	h4kuna\Fio\Response\Read\TransactionList,
	h4kuna\Fio\Utils;

/**
 * Read from informtion Fio account
 */
class FioRead extends Fio
{

	/** @var string */
	private $requestUrl;

	/** @var IParser */
	private $parser;

	public function __construct(Utils\Context $context, Response\Read\IStatementFactory $statementFactory)
	{
		parent::__construct($context);
		$this->parser = $statementFactory->createParser();
	}

	/**
	 * Movements in date range.
	 * @param string|int|\DateTime $from
	 * @param string|int|\DateTime $to
	 * @return TransactionList
	 */
	public function movements($from = '-1 week', $to = 'now')
	{
		$data = $this->download('periods/%s/%s/%s/transactions.%s', Utils\String::date($from), Utils\String::date($to), $this->parser->getExtension());
		return $this->parser->parse($data);
	}

	/**
	 * List of movemnts.
	 * @param int $id
	 * @param int|string|NULL $year format YYYY, NULL is current
	 * @return IFile
	 */
	public function movementId($id, $year = NULL)
	{
		if ($year === NULL) {
			$year = date('Y');
		}
		$data = $this->download('by-id/%s/%s/%s/transactions.%s', $year, $id, $this->parser->getExtension());
		return $this->parser->parse($data);
	}

	/**
	 * Last movements from last breakpoint.
	 * @return IFile
	 */
	public function lastDownload()
	{
		$data = $this->download('last/%s/transactions.%s', $this->parser->getExtension());
		return $this->parser->parse($data);
	}

	/**
	 * Set break point to id.
	 * @param int $moveId
	 * @return void
	 */
	public function setLastId($moveId)
	{
		$this->download('set-last-id/%s/%s/', $moveId);
	}

	/**
	 * Set breakpoint to date.
	 * @param mixed $date
	 * @return void
	 */
	public function setLastDate($date)
	{
		$this->download('set-last-date/%s/%s/', Utils\String::date($date));
	}

	/**
	 * Last request url for read. This is for tests.
	 * @return string
	 */
	public function getRequestUrl()
	{
		return $this->requestUrl;
	}

	private function download($apiUrl /* ... params */)
	{
		$args = func_get_args();
		$args[0] = $this->context->getToken();
		$this->requestUrl = $this->context->getUrl() . vsprintf($apiUrl, $args);
		return $this->context->getQueue()->download($this->context->getToken(), $this->requestUrl);
	}

}
