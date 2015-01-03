<?php

namespace h4kuna\Fio;

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
     * File extension
     *
     * @return string
     */
    function getExtension();

    /**
     * Prepare downloaded data before append
     *
     * @param string $data
     * @return IFile
     */
    function parse($data);

    /**
     * Date format
     *
     * @return string
     */
    function getDateFormat();
}
