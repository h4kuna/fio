<?php

namespace h4kuna\fio\libs\files;

use h4kuna\fio\libs\File;

/**
 * Description of Csv
 *
 * @author h4kuna
 */
class Csv extends File {

    public function getExtension() {
        return self::CSV;
    }

    public function parse($data) {
        var_dump($data);
        exit;
    }

}

