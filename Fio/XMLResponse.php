<?php

namespace h4kuna\Fio;

/**
 * Description of XMLResponse
 *
 * @author Milan Matějček
 */
class XMLResponse {

    public $content;

    public function __construct($content) {
        $this->content = new \SimpleXMLIterator($content);
    }

}
