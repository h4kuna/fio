<?php

namespace h4kuna\Fio\Request\Pay;

use h4kuna\Fio\Request\Pay\Payment,
	XMLWriter;

/**
 * @author Milan Matějček
 */
class XMLFile
{

	/** @var XMLWriter */
	private $xml;

	/** @var string */
	private $content = TRUE;

	/** @var string */
	private $temp;

	public function __construct($temp)
	{
		$this->temp = $temp;
	}

	/**
	 * @param Payment\Property $data
	 * @return self
	 */
	public function setData(Payment\Property $data)
	{
		if ($this->content) {
			$this->createEmptyXml();
		}
		return $this->setBody($data);
	}

	/** @return string  */
	public function getPathname()
	{
		$filename = $this->temp . DIRECTORY_SEPARATOR . md5(microtime(TRUE)) . '.xml';
		file_put_contents($filename, $this->getXml());
		register_shutdown_function(function() use ($filename) {
			@unlink($filename);
		});
		return $filename;
	}

	/** @return string XML */
	public function getXml()
	{
		if ($this->content) {
			return $this->content;
		}

		return $this->content = $this->endDocument();
	}

	/** @return bool */
	public function isReady()
	{
		return $this->content === NULL;
	}

	/**
	 * Prepare XML.
	 */
	private function createEmptyXml()
	{
		$this->xml = new XMLWriter;
		$this->xml->openMemory();
		$this->xml->startDocument('1.0', 'UTF-8');
		$this->xml->startElement('Import');
		$this->xml->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$this->xml->writeAttribute('xsi:noNamespaceSchemaLocation', 'http://www.fio.cz/schema/importIB.xsd');
		$this->xml->startElement('Orders');
		$this->content = NULL;
	}

	private function setBody(Payment\Property $data)
	{
		$this->xml->startElement($data->getStartXmlElement());
		foreach ($data as $node => $value) {
			if ($value === FALSE) {
				continue;
			}

			$this->xml->startElement($node);
			$this->xml->text((string) $value);
			$this->xml->endElement();
		}
		$this->xml->endElement();
		return $this;
	}

	private function endDocument()
	{
		$this->xml->endDocument();
		return $this->xml->outputMemory();
	}

}
