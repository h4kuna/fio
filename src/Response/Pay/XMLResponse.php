<?php

namespace h4kuna\Fio\Response\Pay;

use SimpleXMLElement;

/**
 * @author Milan Matějček
 */
class XMLResponse implements IResponse
{

    /** @var SimpleXMLElement */
    private $xml;

    /** @param string $xml */
    public function __construct($xml)
    {
        $this->xml = new SimpleXMLElement($xml);
    }

    public function isOk()
    {
        return $this->getError() == 'ok' && $this->getErrorCode() == 0;
    }

    /**
     * READ XML ****************************************************************
     * *************************************************************************
     */

    /** @return SimpleXMLElement */
    public function getXml()
    {
        return $this->xml;
    }

    /** @return int */
    public function getErrorCode()
    {
        return $this->getValue('result/errorCode');
    }

    /** @return string */
    public function getError()
    {
        return $this->getValue('result/status');
    }

    /**
     *
     * @param string $path
     * @return string
     */
    private function getValue($path)
    {
        $val = $this->xml->xpath($path . '/text()');
        return (string) $val[0];
    }

    /**
     *
     * @param string $fileName
     */
    public function saveXML($fileName)
    {
        $this->xml->saveXML($fileName);
    }

}
