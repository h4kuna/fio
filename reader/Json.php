<?php

namespace h4kuna\fio\reader;

require_once 'File.php';

class Json extends File implements IFile {

    public function getExtension() {
        return self::JSON;
    }

}