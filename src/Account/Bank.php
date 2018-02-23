<?php

namespace h4kuna\Fio\Account;

use h4kuna\Fio\AccountException;

/**
 * @author Milan Matějček
 */
class Bank
{

	/** @var int */
	private $account;

	/** @var int */
	private $bankCode = '';

	/** @var int */
	private $prefix = '';

	/**
	 * @param string $account [prefix-]account[/code] no whitespace
	 * @throws AccountException
	 */
	public function __construct($account)
	{
		if (!preg_match('~^(?P<prefix>\d+-)?(?P<account>\d+)(?P<code>/\d+)?$~', $account, $find)) {
			throw new AccountException('Account must have format [prefix-]account[/code].');
		}

		if (strlen($find['account']) > 16) {
			throw new AccountException('Account max length is 16 chars.');
		}

		$this->account = $find['account'];

		if (!empty($find['code'])) {
			$this->bankCode = $find['code'];
			if (strlen($this->getBankCode()) !== 4) {
				throw new AccountException('Code must have 4 chars length.');
			}
		}

		if (!empty($find['prefix'])) {
			$this->prefix = $find['prefix'];
		}
	}

	/** @return string */
	public function getAccount()
	{
		return $this->prefix . $this->account;
	}

	/** @return string */
	public function getBankCode()
	{
		if ($this->bankCode) {
			return substr($this->bankCode, 1);
		}
		return '';
	}

	/** @return string */
	public function getPrefix()
	{
		if ($this->prefix) {
			return substr($this->prefix, 0, -1);
		}
		return '';
	}

	public function getAccountAndCode()
	{
		return $this->getAccount() . $this->bankCode;
	}

	public function __toString()
	{
		return (string) $this->getAccount();
	}

}
