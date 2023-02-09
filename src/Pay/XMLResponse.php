<?php declare(strict_types=1);

namespace h4kuna\Fio\Pay;

use SimpleXMLElement;

class XMLResponse implements Response
{
	private SimpleXMLElement $xml;


	public function __construct(string $xml)
	{
		$this->xml = new SimpleXMLElement($xml);
	}


	public function isOk(): bool
	{
		return $this->status() === 'ok' && $this->code() === 0;
	}


	/**
	 * READ XML ****************************************************************
	 * *************************************************************************
	 */

	public function getXml(): SimpleXMLElement
	{
		return $this->xml;
	}


	public function code(): int
	{
		return (int) $this->getValue('result/errorCode');
	}


	public function getIdInstruction(): string
	{
		return $this->getValue('result/idInstruction');
	}


	public function status(): string
	{
		return $this->getValue('result/status');
	}


	/**
	 * @return array<int, string>
	 */
	public function errorMessages(): array
	{
		$errorMessages = [];
		/** @var array<SimpleXMLElement> $messages */
		$messages = $this->getXml()->xpath('ordersDetails/detail/messages');
		foreach ($messages as $message) {
			foreach ($message as $item) {
				if (isset($item['errorCode'])) {
					$errorMessages[(int) $item['errorCode']] = (string) $item;
				} else {
					$errorMessages[] = (string) $item;
				}
			}
		}

		return $errorMessages;
	}


	private function getValue(string $path): string
	{
		$val = $this->getXml()->xpath($path . '/text()');
		if ($val === false || !isset($val[0])) {
			return '';
		}

		return (string) $val[0];
	}


	public function saveXML(string $fileName): void
	{
		$this->getXml()->saveXML($fileName);
	}


	public function __toString()
	{
		return (string) $this->xml->asXML();
	}

}
