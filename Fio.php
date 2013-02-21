<?php

namespace h4kuna\fio;

use Nette;
use h4kuna\fio\libs;

require_once 'FioException.php';
require_once 'libs/File.php';

class Fio extends Nette\Object {

    const FIO_API_VERSION = '1.0.5';
    const REST_URL = 'https://www.fio.cz/ib_api/rest/';

    /**
     * secure token
     * @var string
     */
    private $token;

    /**
     * @var IFile
     */
    private $parser;

    /** @var libs\Data */
    private $lastResponse;

    /**
     *
     * @param type $token
     * @param string|libs\IFile $parser
     */
    public function __construct($token, $parser = libs\IFile::GPC) {
        $this->token = $token;
        $this->loadParser($parser);
    }

    /**
     *
     * @param mixed $from
     * @param mixed $to
     * @return libs\Data
     */
    public function movements($from = '-1 month', $to = 'now') {
        $url = self::REST_URL . sprintf('periods/%s/%s/%s/transactions.%s', $this->token, \Nette\DateTime::from($from)->format('Y-m-d'), \Nette\DateTime::from($to)->format('Y-m-d'), $this->parser->getExtension());
        return $this->lastResponse = $this->parser->parse(\h4kuna\CUrl::download($url));
    }

    /** @return libs\Data */
    public function getLastResponse() {
        return $this->lastResponse;
    }

    /**
     * prepare object for parse data
     * @param \h4kuna\fio\libs\IFile $parser
     * @throws \RuntimeException
     */
    private function loadParser($parser) {
        if ($parser instanceof libs\IFile) {
            $this->parser = $parser;
        } elseif (is_string($parser)) {
            $class = '\libs\files\\' . ucfirst($parser);
            $file = __DIR__ . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
            if (file_exists($file)) {
                require_once $file;
                $class = __NAMESPACE__ . $class;
                $this->parser = new $class;
                return $this;
            }
            throw new FioException('File not found: ' . $file);
        }
        throw new FioException('Parser is\'t supported. Must be Instance of IFile or string as constant from IFile.');
    }

}
