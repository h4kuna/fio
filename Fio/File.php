<?php

namespace h4kuna\Fio;

use \Nette\Object;

abstract class File extends Object implements IFile, \Iterator {

    /** @var array */
    protected static $dataKeys;

    /** @var array */
    protected static $headerKeys;

    /** @var array */
    private $header = array();
    private $data = array();

    /** @var array */
    private $headerKeyMap = array();

    /** @var array */
    private $dataKeyMap = array();

    public function __construct($headerKeyMap = array(), $dataKeyMap = array()) {
        if (!isset(self::$dataKeys[$this->getExtension()])) {
            self::$dataKeys[$this->getExtension()] = $this->dataKeys();
            self::$headerKeys[$this->getExtension()] = $this->headerKeys();
        }

        if ($headerKeyMap) {
            if (key($headerKeyMap) === 0) {
                $this->headerKeyMap = array_combine($this->getHeaderKeys(), $headerKeyMap);
            } else {
                $this->headerKeyMap = $headerKeyMap;
            }
        }

        if ($dataKeyMap) {
            if (key($dataKeyMap) === 0) {
                $this->dataKeyMap = array_combine($this->getDataKeys(), $dataKeyMap);
            } else {
                $this->dataKeyMap = $dataKeyMap;
            }
        }
    }

    /**
     * Setup heades
     *
     * @param array $data
     * @return File
     */
    protected function setHeader(array $data) {
        if ($this->headerKeyMap) {
            $map = $this->headerKeyMap;
        } else {
            $map = array_combine($this->getHeaderKeys(), $this->getHeaderKeys());
        }

        foreach ($data as $k => $v) {
            if (isset($map[$k])) {
                $value = $this->prepareHeader($k, $v);
                $this->header[$map[$k]] = $value;
            }
        }

        return $this;
    }

    /**
     * Same format for all files
     *
     * @param string|int $key
     * @param mixed $value
     * @return mixed
     */
    protected function prepareHeader($key, $value) {
        switch ($key) {
            case 'openingBalance':
            case 'closingBalance':
                $value = new \h4kuna\Float($value);
                break;
            case 'dateStart':
            case 'dateEnd':
                $value = $this->createDateTime($value);
                break;
        }
        return $value;
    }

    /**
     * Same format for all files
     *
     * @param string|int $key
     * @param mixed $value
     * @return mixed
     */
    protected function prepareData($key, $value) {
        switch ($key) {
            case 'amount':
                $value = new \h4kuna\Float($value);
                break;
            case 'comment':
            case 'userNote':
                $value = trim($value);
                break;
            case 'moveDate':
            case 'dueDate':
                $value = $this->createDateTime($value);
                break;
        }
        return $value;
    }

    /**
     * @return array
     */
    public function getHeader() {
        return $this->header;
    }

    /**
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Convert string to \DateTime
     *
     * @param string $value
     * @param bool $midnight
     * @return \DateTime
     */
    final protected function createDateTime($value, $midnight = TRUE) {
        $dt = date_create_from_format($this->getDateFormat(), $value);
        if ($midnight) {
            $dt->setTime(0, 0, 0);
        }
        return $dt;
    }

    /**
     * Fill data
     * 
     * @param array $data
     * @return \h4kuna\fio\File
     */
    final protected function append(array $data) {
        if ($this->dataKeyMap) {
            $map = $this->dataKeyMap;
        } else {
            $map = array_combine($this->getDataKeys(), $this->getDataKeys());
        }

        $append = array();
        foreach ($data as $k => $v) {
            if (isset($map[$k])) {
                $value = $this->prepareData($k, $v);
                $append[$map[$k]] = $value;
            }
        }

        $this->data[] = $append;

        return $this;
    }

    protected function dataKeys() {
        return array('moveId', 'moveDate', 'amount', 'currency',
            'toAccount', 'toAccountName', 'bankCode', 'bankName', 'constantSymbol',
            'variableSymbol', 'specificSymbol', 'userNote', 'message', 'type',
            'performed', 'specification', 'comment', 'bic', 'instructionId',
        );
    }

    protected function headerKeys() {
        return array('accountId', 'bankId', 'currency',
            'iban', 'bic', 'openingBalance', 'closingBalance', 'dateStart',
            'dateEnd', 'idFrom', 'idTo');
    }

    protected function getHeaderKeys() {
        return self::$headerKeys[$this->getExtension()];
    }

    protected function getDataKeys() {
        return self::$dataKeys[$this->getExtension()];
    }

//----------------- implements iterator ----------------------------------------
    public function current() {
        return current($this->data);
    }

    public function next() {
        return next($this->data);
    }

    public function key() {
        return key($this->data);
    }

    public function valid() {
        return array_key_exists($this->key(), $this->data);
    }

    public function rewind() {
        reset($this->data);
        return $this;
    }

    /**
     *
     * @return string
     */
    public function __toString() {
        return (string) $this->getExtension();
    }

}
