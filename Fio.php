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
     * @var libs\IFile
     */
    private $parser;

    /**
     *
     * @param type $token
     * @param string|libs\IFile $parser
     */
    public function __construct($token, $parser = libs\IFile::CSV) {
        $this->token = $token;
        $this->loadParser($parser);
    }

    /**
     * download variable range
     * @param mixed $from
     * @param mixed $to
     * @return libs\IFile
     */
    public function movements($from = '-1 month', $to = 'now') {
        $url = self::REST_URL . sprintf('periods/%s/%s/%s/transactions.%s', $this->token, \Nette\DateTime::from($from)->format('Y-m-d'), \Nette\DateTime::from($to)->format('Y-m-d'), $this->parser->getExtension());
        return $this->parser->parse(\h4kuna\CUrl::download($url));
    }

    /**
     * ???
     * @param int $id
     * @param int|string $year format YYYY
     * @return libs\IFile
     */
    public function movementId($id, $year = NULL) {
        if ($year === NULL) {
            $year = date('Y');
        }
        $url = self::REST_URL . sprintf('by-id/%s/%s/%s/transactions.%s', $this->token, $year, $id, $this->parser->getExtension());
        return $this->parser->parse(\h4kuna\CUrl::download($url));
    }

    /**
     * this method download a new movements and create breakpoint
     * @return type
     */
    public function lastDownload() {
        $url = self::REST_URL . sprintf('last/%s/transactions.%s', $this->token, $this->parser->getExtension());
        return $this->parser->parse(\h4kuna\CUrl::download($url));
    }

    /**
     * set brakepoint to know moveId
     * @param int $moveId
     * @return string
     */
    public function setLastId($moveId) {
        $url = self::REST_URL . sprintf('set-last-id/%s/%s/', $this->token, $moveId);
        return \h4kuna\CUrl::download($url);
    }

    /**
     * set breakpoint to date
     * @param mixed $date
     * @return string
     */
    public function setLastDate($date) {
        $url = self::REST_URL . sprintf('set-last-date/%s/%s/', $this->token, \Nette\DateTime::from($date)->format('Y-m-d'));
        return \h4kuna\CUrl::download($url);
    }

    /** @return libs\Data */
    public function getLastResponse() {
        return $this->parser;
    }

    /**
     * prepare object for parse data
     * @param string|libs\IFile $parser
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
