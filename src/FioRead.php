<?php

namespace h4kuna\Fio;

use h4kuna\Fio\Response\Read\TransactionList,
	h4kuna\Fio\Utils;

/**
 * Read from informtion Fio account
 */
class FioRead extends Fio
{

	/** @var string */
	private $requestUrl;

	/** @var Response\Read\IReader */
	private $readerFactory;

	public function __construct(Request\IQueue $queue, Account\Accounts $accounts, Request\Read\IReader $readerFactory)
	{
		parent::__construct($queue, $accounts);
		$this->readerFactory = $readerFactory;
	}

	/**
	 * Movements in date range.
	 * @param string|int|\DateTime $from
	 * @param string|int|\DateTime $to
	 * @return TransactionList
	 */
	public function movements($from = '-1 week', $to = 'now')
	{
		$data = $this->download('periods/%s/%s/%s/transactions.%s', Utils\Strings::date($from), Utils\Strings::date($to), $this->readerFactory->getExtension());
		return $this->readerFactory->create($data);
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
		$data = $this->download('by-id/%s/%s/%s/transactions.%s', $year, $id, $this->readerFactory->getExtension());
		return $this->readerFactory->create($data);
	}

	/**
	 * Last movements from last breakpoint.
	 * @return IFile
	 */
	public function lastDownload()
	{
		$data = $this->download('last/%s/transactions.%s', $this->readerFactory->getExtension());
		return $this->readerFactory->create($data);
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
		$this->download('set-last-date/%s/%s/', Utils\Strings::date($date));
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
		$args[0] = $token = $this->accounts->getActive()->getToken();
		$this->requestUrl = self::REST_URL . vsprintf($apiUrl, $args);
		return $this->queue->download($token, $this->requestUrl);
	}

}
