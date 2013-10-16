<?php

namespace h4kuna\Fio\Files;

use h4kuna\Fio\File;

/**
 * @author Milan Matejcek
 */
class Csv extends File {

    public function getExtension() {
        return self::CSV;
    }

    public function getDateFormat() {
        return 'd.m.Y';
    }

    public function parse($data) {
        $text = new \h4kuna\TextIterator($data);
        $text->setCsv(';');
        $header = array();
        $headerComplete = 0;

        foreach ($text as $line) {
            if (!$line || $headerComplete === 1) {
                ++$headerComplete;
            } elseif ($headerComplete) {
                $this->append(array_combine($this->getDataKeys(), $line));
            } else {
                $header[$line[0]] = $line[1];
            }
        }

        $this->setHeader($header);
        return $this;
    }

}
