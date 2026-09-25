<?php declare(strict_types = 1);

namespace h4kuna\Fio;

use DateTimeInterface;
use h4kuna\Fio\Account\FioAccount;
use h4kuna\Fio\Read\Reader;
use h4kuna\Fio\Read\TransactionList;
use h4kuna\Fio\Utils\Fio;
use h4kuna\Fio\Utils\Queue;
use Psr\Http\Message\ResponseInterface;
use function date;
use function vsprintf;

/**
 * Read from information Fio account
 */
class FioRead
{

	public function __construct(
		private Queue $queue,
		private FioAccount $account,
		private Reader $reader,
	)
	{
	}

	/**
	 * Movements in date range.
	 */
	public function movements(
		int|string|DateTimeInterface $from = '-1 week',
		int|string|DateTimeInterface $to = 'now',
	): TransactionList
	{
		$data = $this->download('periods/%s/%s/%s/transactions.%s', Fio::date($from), Fio::date($to), $this->reader->getExtension());

		return $this->reader->create($data);
	}

	/**
	 * List of movements.
	 *
	 * @param int $year format YYYY, empty string is current
	 */
	public function movementId(
		int $moveId,
		int $year = 0,
	): TransactionList
	{
		if ($year === 0) {
			$year = (int) date('Y');
		}
		$data = $this->download('by-id/%s/%s/%s/transactions.%s', (string) $year, (string) $moveId, $this->reader->getExtension());

		return $this->reader->create($data);
	}

	/**
	 * Last movements from last breakpoint.
	 */
	public function lastDownload(): TransactionList
	{
		$data = $this->download('last/%s/transactions.%s', $this->reader->getExtension());

		return $this->reader->create($data);
	}

	/**
	 * Set break point to id.
	 */
	public function setLastId(int $moveId): ResponseInterface
	{
		return $this->download('set-last-id/%s/%s/', (string) $moveId);
	}

	/**
	 * Set breakpoint to date.
	 */
	public function setLastDate(int|string|DateTimeInterface $date): ResponseInterface
	{
		return $this->download('set-last-date/%s/%s/', Fio::date($date));
	}

	public function getAccount(): FioAccount
	{
		return $this->account;
	}

	private function download(
		string $apiUrl,
		string ...$args,
	): ResponseInterface
	{
		$token = $this->account->getToken();
		$requestUrl = Fio::REST_URL . vsprintf($apiUrl, [$token, ...$args]);

		return $this->queue->download($token, $requestUrl);
	}

}
