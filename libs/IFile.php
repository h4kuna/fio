<?php

namespace h4kuna\fio\libs;

/**
 *
 * @author Milan Matějček
 */
interface IFile {

    const XML = 'xml';
    const OFX = 'ofx';
    const GPC = 'gpc';
    const CSV = 'csv';
    const HTML = 'html';
    const JSON = 'json';
    const STA = 'sta';

    /**
     * @return string file extension
     */
    function getExtension();

    /**
     *
     * @param string $data
     * @return \h4kuna\fio\libs\Data Description
     */
    function parse($data);
}

