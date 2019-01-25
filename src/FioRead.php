<?php declare(strict_types=1);

namespace h4kuna\Fio;

use h4kuna\Fio\Exceptions\ServiceUnavailable;
use h4kuna\Fio\Response\Read\TransactionList;
use h4kuna\Fio\Utils;

/**
 * Read from information Fio account
 */
class FioRead extends Fio
{

	/** @var string */
	private $requestUrl;

	/** @var Request\Read\IReader */
	private $readerFactory;

	public function __construct(Request\IQueue $queue, Account\FioAccount $account, Request\Read\IReader $readerFactory)
	{
		parent::__construct($queue, $account);
		$this->readerFactory = $readerFactory;
	}

	/**
	 * Movements in date range.
	 * @param int|string|\DateTimeInterface $from
	 * @param int|string|\DateTimeInterface $to
	 * @throws ServiceUnavailable
	 */
	public function movements($from = '-1 week', $to = 'now'): TransactionList
	{
		$data = $this->download('periods/%s/%s/%s/transactions.%s', Utils\Strings::date($from), Utils\Strings::date($to), $this->readerFactory->getExtension());
		return $this->readerFactory->create($data);
	}

	/**
	 * List of movemnts.
	 * @param int $moveId
	 * @param int $year format YYYY, empty string is current
	 * @throws ServiceUnavailable
	 */
	public function movementId(int $moveId, int $year = 0): TransactionList
	{
		if ($year === 0) {
			$year = (int) date('Y');
		}
		$data = $this->download('by-id/%s/%s/%s/transactions.%s', (string) $year, (string) $moveId, $this->readerFactory->getExtension());
		return $this->readerFactory->create($data);
	}


	/**
	 * Last movements from last breakpoint.
	 * @throws ServiceUnavailable
	 */
	public function lastDownload(): TransactionList
	{
		$data = $this->download('last/%s/transactions.%s', $this->readerFactory->getExtension());
		return $this->readerFactory->create($data);
	}


	/**
	 * Set break point to id.
	 * @throws ServiceUnavailable
	 */
	public function setLastId(int $moveId): void
	{
		$this->download('set-last-id/%s/%s/', (string) $moveId);
	}


	/**
	 * Set breakpoint to date.
	 * @param int|string|\DateTimeInterface $date
	 * @throws ServiceUnavailable
	 */
	public function setLastDate($date)
	{
		$this->download('set-last-date/%s/%s/', Utils\Strings::date($date));
	}


	/**
	 * Last request url for read. This is for tests.
	 */
	public function getRequestUrl(): string
	{
		return $this->requestUrl;
	}


	/**
	 * @throws ServiceUnavailable
	 */
	private function download(string $apiUrl, string ...$args): string
	{
		$token = $this->account->getToken();
		$this->requestUrl = self::REST_URL . sprintf($apiUrl, $token, ...$args);
		return $this->queue->download($token, $this->requestUrl);
	}

}
