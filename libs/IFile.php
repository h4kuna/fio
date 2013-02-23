<?php

namespace h4kuna\fio\libs;

/**
 *
 * @author Milan Matějček
 */
interface IFile {
    /** supported */

    const GPC = 'gpc';
    const CSV = 'csv';
    const JSON = 'json';

    /** not supported */
    const XML = 'xml';
    const OFX = 'ofx';
    const HTML = 'html';
    const STA = 'sta';

    /**
     * file extension
     * @return string
     */
    function getExtension();

    /**
     * prepare downloaded data before append
     * @param string $data
     * @return self
     */
    function parse($data);
}

