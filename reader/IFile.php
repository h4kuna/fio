<?php

namespace h4kuna\fio\reader;

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
}

