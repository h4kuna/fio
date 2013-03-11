<?php

namespace h4kuna\fio\files;

use h4kuna\fio\File;

/**
 * Description of Csv
 *
 * @author h4kuna
 */
class Json extends File {

    public function getExtension() {
        return self::JSON;
    }

    public function parse($data) {
        $json = json_decode($data);
        $this->setHeader((array) $json->accountStatement->info);
        static $mapper = array(22, 0, 1, 14, 2, 10, 3, 12, 4, 5, 6, 7, 16, 8, 9, 18, 25, 26, 17);
        $combine = array_combine($mapper, $this->getDataKeys());

        foreach ($json->accountStatement->transactionList->transaction as $row) {
            $out = array();
            foreach ($row as $column) {
                if ($column) {
                    $out[$combine[$column->id]] = $column->value;
                }
            }
            $this->append($out);
        }
        return $this;
    }

    public function getDateFormat() {
        return 'Y-m-dO';
    }

    public function headerKeys() {
        return array_merge(parent::headerKeys(), array('yearList', 'idList', 'idLastDownload'));
    }

}