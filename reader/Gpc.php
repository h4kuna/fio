<?php

namespace h4kuna\fio\reader;

require_once __DIR__ . '/File.php';

class Gpc extends File {

    public function getExtension() {
        return self::GPC;
    }

}
