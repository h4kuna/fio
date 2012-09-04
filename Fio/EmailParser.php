<?php

namespace Fio;

class EmailParser extends \Utility\TextIterator
{

	public function __construct($text)
	{
		$transform = array('Protiúčet' => 'offsetAccount', 'Částka' => 'value',
				'VS' => 'variableSymbol', 'KS' => 'constantSymbol', 'SS' => 'specificSymbol');
		$new = array('dueDate' => new \DateTime(), 'checkSum' => NULL);

		$sum = NULL;
		foreach ($this->text2Array(\Utility\FileTools::autoUTF(imap_qprint($text))) as $v) {
			$key = strstr($v, ':', TRUE);
			if (isset($transform[$key])) {
				$v = trim(str_replace($key . ':', '', $v));
				$sum .= $v;
				if ($key == 'Protiúčet') {
					list($new[$transform[$key]], $new['bankCode']) = explode('/', $v);
				}
				else {
					$new[$transform[$key]] = $v;
				}
			}
		}
		$new['checkSum'] = hash('sha256', $sum);
		parent::__construct($new);
	}

}
