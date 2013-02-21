<?php

namespace h4kuna\fio\libs\files;

use h4kuna\fio\libs\File;

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
        var_dump($data);
        exit;
    }

}