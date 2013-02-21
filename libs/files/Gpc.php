<?php

namespace h4kuna\fio\libs\files;

use h4kuna\fio\libs\File;

/**
 * Description of Csv
 *
 * @author h4kuna
 */
class Gpc extends File {

    public function getExtension() {
        return self::GPC;
    }

    public function parse($data) {
        var_dump($data);
        exit;
        require_once __DIR__ . '/../GpcParser.php';
        $data = new \h4kuna\GpcParser($data);
        pd($data->arrayCopy());
    }

}
