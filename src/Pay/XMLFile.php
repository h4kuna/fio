<?php declare(strict_types=1);

namespace h4kuna\Fio\Pay;

use h4kuna\Fio\Exceptions\InvalidState;
use Stringable;
use XMLWriter;

class XMLFile
{
	private ?XMLWriter $xml = null;


	public function setData(Payment\Property $data): self
	{
		if ($this->isReady() === false) {
			$this->createEmptyXml();
		}

		return $this->setBody($data);
	}


	public function getXml(): string
	{
		if ($this->isReady() === false) {
			throw new InvalidState('You can read only onetime.');
		}

		return $this->endDocument();
	}


	private function isReady(): bool
	{
		return $this->xml !== null;
	}


	private function createEmptyXml(): void
	{
		$this->xml = new XMLWriter;
		$this->xml->openMemory();
		$this->xml->startDocument('1.0', 'UTF-8');
		$this->xml->startElement('Import');
		$this->xml->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$this->xml->writeAttribute('xsi:noNamespaceSchemaLocation', 'http://www.fio.cz/schema/importIB.xsd');
		$this->xml->startElement('Orders');
	}


	private function setBody(Payment\Property $data): self
	{
		if ($this->xml === null) {
			throw self::exceptionFirstCallSetData();
		}
		$elements = $data->getExpectedProperty();
		$this->xml->startElement($data->getStartXmlElement());
		foreach ($data as $node => $value) {
			if ($value == false) { // intentionally ==
				if ($elements[$node] === false) {
					continue;
				}
				$value = '';
			}

			assert(is_scalar($value) || $value instanceof Stringable);
			$this->xml->startElement($node);
			$this->xml->text((string) $value);
			$this->xml->endElement();
		}
		$this->xml->endElement();

		return $this;
	}


	private function endDocument(): string
	{
		if ($this->xml === null) {
			throw self::exceptionFirstCallSetData();
		}
		$this->xml->endDocument();
		$xml = $this->xml->outputMemory();
		$this->xml = null;

		return $xml;
	}


	private static function exceptionFirstCallSetData(): InvalidState
	{
		return new InvalidState('First you must call setData().');
	}

}
