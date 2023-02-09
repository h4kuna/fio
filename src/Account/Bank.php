<?php declare(strict_types=1);

namespace h4kuna\Fio\Account;

use h4kuna\Fio\Exceptions\InvalidArgument;
use Nette\Utils\Strings;

class Bank
{

	private function __construct(private string $account, private string $bankCode, private string $prefix)
	{
	}


	public function getAccount(): string
	{
		return $this->prefix() . $this->account;
	}


	public function getBankCode(): string
	{
		return $this->bankCode;
	}


	public function getPrefix(): string
	{
		return $this->prefix;
	}


	public function getAccountAndCode(): string
	{
		return $this->getAccount() . $this->bankCode();
	}


	public function __toString()
	{
		return $this->getAccount();
	}


	private function bankCode(): string
	{
		if ($this->bankCode !== '') {
			return '/' . $this->bankCode;
		}

		return $this->bankCode;
	}


	private function prefix(): string
	{
		if ($this->prefix !== '') {
			return $this->prefix . '-';
		}

		return $this->prefix;
	}


	public static function createInternational(string $account): self
	{
		$find = Strings::match($account, '~^(?P<account>[a-z0-9]{1,34})(?P<code>/[a-z0-9]{11})?$~i');
		if ($find === null) {
			throw new InvalidArgument('Account must have format account[/code].');
		}

		$bankCode = '';
		if (isset($find['code']) && $find['code'] !== '') {
			$bankCode = substr($find['code'], 1);
		}

		return new self($find['account'], $bankCode, '');
	}


	public static function createNational(string $account): self
	{
		$find = Strings::match($account, '~^(?P<prefix>\d+-)?(?P<account>\d+)(?P<code>/\d+)?$~');
		if ($find === null) {
			throw new InvalidArgument('Account must have format [prefix-]account[/code].');
		}

		if (strlen($find['account']) > 16) {
			throw new InvalidArgument('Account max length is 16 chars.');
		}

		$account = $find['account'];
		$prefix = $bankCode = '';
		if (isset($find['code']) && $find['code'] !== '') {
			$bankCode = substr($find['code'], 1);
			if (strlen($bankCode) !== 4) {
				throw new InvalidArgument('Code must have 4 chars length.');
			}
		}

		if (isset($find['prefix']) && $find['prefix'] !== '') {
			$prefix = substr($find['prefix'], 0, -1);
		}

		return new self($account, $bankCode, $prefix);
	}

}
