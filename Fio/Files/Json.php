<?php

namespace h4kuna\Fio\Files;

use h4kuna\Fio\File;

/**
 * @author Milan Matejcek
 */
class Json extends File {

    /**
     *
     * @return string
     */
    public function getExtension() {
        return self::JSON;
    }

    public function parse($data) {
        $json = json_decode($data);

        if (!$json) {
            // There are JSON data
            return $this;
        }

        $this->setHeader((array) $json->accountStatement->info);
        static $mapper = array(22, 0, 1, 14, 2, 10, 3, 12, 4, 5, 6, 7, 16, 8, 9, 18, 25, 26, 17);
        $combine = array_combine($mapper, $this->getDataKeys());

        if (!$json->accountStatement->transactionList) {
            // There are no transactions
            return $this;
        }

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

    /**
     *
     * @return string
     */
    public function getDateFormat() {
        return 'Y-m-dO';
    }

    /**
     *
     * @return array
     */
    public function headerKeys() {
        return array_merge(parent::headerKeys(), array('yearList', 'idList', 'idLastDownload'));
    }

}
