<?php

namespace Fio;

use Nette;

class Fio extends Nette\Object
{
	private $account;
	private $userName;
	private $password;
	private $userAgent = 'PHP Script';
	private $filter = GpcParser::BOTH;

	public function __construct($account, $password, $userName)
	{
		$this->account = urlencode($account);
		$this->password = urlencode($password);
		$this->userName = urlencode($userName);
	}

	public function setFilter($v)
	{
		$this->filter = $v;
		return $this;
	}

	public function setUserAgent($v)
	{
		$this->userAgent = $v;
		return $this;
	}

	/**
	 * @param string|int|\Datetime $from
	 * @param string|int|\Datetime $to
	 * @return \Fio\GpcParser
	 */
	public function import($from = '-1 month', $to = 'now')
	{
		$format = 'd.m.Y';
		$from = Nette\DateTime::from($from);
		$to = Nette\DateTime::from($to);

		$requestURL = "https://www.fio.cz/scgi-bin/hermes/dz-pohyby.cgi?ID_ucet={$this->account}" .
						"&LOGIN_USERNAME={$this->userName}&SUBMIT=Odeslat&LOGIN_TIME=" . time() .
						"&LOGIN_PASSWORD={$this->password}&pohyby_DAT_od={$from->format($format)}" .
						"&pohyby_DAT_do={$to->format($format)}&export_gpc=1";
		$curl = new \Utility\CUrl($requestURL, array(
								CURLOPT_USERAGENT => $this->userAgent,
								CURLOPT_RETURNTRANSFER => TRUE,
								CURLOPT_HEADER => FALSE,
								CURLOPT_SSL_VERIFYPEER => FALSE,
								CURLOPT_HTTPGET => TRUE,
								CURLOPT_HTTPHEADER => array('Content-Type: text/plain', 'Connection: Close')
						));

		return new GpcParser($curl->exec(), $this->filter);
	}

}
