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
     * @return string file extension
     */
    function getExtension();

    /**
     *
     * @param string $data
     * @return self
     */
    function parse($data);

    /**
     * @return string
     */
    function getDateFormat();

    /**
     * @return array
     */
    function getDataKeys();

    /**
     * @return array
     */
    function getHeaderKeys();
}

