<?php

namespace h4kuna\Fio;

/**
 * Description of XMLResponse
 *
 * @author Milan Matějček
 */
class XMLResponse {

    /** @var \SimpleXMLElement */
    private $xml;

    /**
     *
     * @param string $xml
     */
    public function __construct($xml) {
        $this->xml = new \SimpleXMLElement($xml);
    }

    public function isOk() {
        return $this->getStatus() == 'ok' && $this->getError() == 0;
    }

    /**
     * READ XML ****************************************************************
     * *************************************************************************
     */
    public function getXml() {
        return $this->xml;
    }

    /** @return int */
    public function getError() {
        return $this->getValue('result/errorCode');
    }

    /** @return string */
    public function getStatus() {
        return $this->getValue('result/status');
    }

    private function getValue($path) {
        $val = $this->xml->xpath($path . '/text()');
        return (string) $val[0];
    }

}
