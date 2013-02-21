<?php

namespace h4kuna;

use Nette\Object;

/**
 * return utf-8 array
 */
class GpcParser extends Object implements \Iterator {

    /**
     * @var \Iterator
     */
    private $data;
    private $filter;
    private $current;

    const BOTH = NULL;
    const REPORT = '074';
    const ITEM = '075';

    public function __construct($source, $filter = self::BOTH) {
        $this->filter = $filter;

        if (file_exists($source)) {
            $this->data = new \SplFileObject($source);
            $this->data->setFlags(6); //bug konstanta \SplFileObject::SKIP_EMPTY vraci 4 uz to bylo reportovane
        } else {
            $this->data = new \h4kuna\TextIterator($source);
        }
    }

    public function rewind() {
        $this->current = NULL;
        $this->data->rewind();
    }

    public function next() {
        $this->current = NULL;
        return $this->data->next();
    }

    public function valid() {
        do {
            $valid = $this->data->valid();
            if ($valid && $this->current() === NULL) {
                $this->next();
            } else {
                break;
            }
        } while ($valid);

        return $valid;
    }

    public function key() {
        return $this->data->key();
    }

    public function current() {
        if ($this->current === NULL) {
            $this->current = $this->parseLine(iconv('windows-1250', 'utf-8', trim($this->data->current())));
        }
        return $this->current;
    }

    public function arrayCopy() {
        $a = array();
        foreach ($this as $k => $v) {
            $a[$k] = $v;
        }
        return $a;
    }



}
