<?php

namespace h4kuna;

use Nette\Object;
use Nette\DateTime;
use h4kuna\CUrl\CurlBuilder;
use h4kuna\CUrl\CUrl;
use h4kuna\Fio\FioException;
use h4kuna\Fio\IFile;
use h4kuna\Fio\XMLResponse;

/**
 * Začne li se sestavovat tělo požadavku je potřeba uzamknout extension a pokud se nastaví cesta k souboru tak zamknout přidávání do těla
 *
 * Read FIO account
 * @todo No tested for file ABO
 */
class Fio extends Object {

    /** @var string */
    const FIO_API_VERSION = '1.2.8';

    /** @var int [s] */
    const API_INTERVAL = 30;

    /** @var string */
    const REST_URL = 'https://www.fio.cz/ib_api/rest/';

    /** @var string */
    const REST_URL_WRITE = 'https://www.fio.cz/ib_api/rest/import/';

    /** @var string */
    private $uploadExtension;

    /** @var string */
    private $language = 'cs';

    /** @var string */
    private $requestUrl;

    /**
     * Secure token
     *
     * @var string
     */
    private $token;

    /** @var IFile */
    private $parser;

    /** @var XMLResponse */
    private $response;

    /** @var string */
    private $account;

    /** @var string */
    private $temp;

    /** @var string */
    private $tempFile;

    /**
     * @param string $token
     * @param string $account
     */
    public function __construct($token, $account, $temp) {
        $this->token = $token;
        $this->account = $account;
        @mkdir($temp, 0777, TRUE);
        $this->temp = realpath($temp);
        $this->tempFile = $this->temp . DIRECTORY_SEPARATOR . preg_replace('~[^0-9]~', '', $account);
    }

    /**
     * Pohyby na účtu za určené období
     *
     * @param string|int|DateTime $from
     * @param string|int|DateTime $to
     * @return IFile
     */
    public function movements($from = '-1 week', $to = 'now') {
        $this->requestUrl = self::REST_URL . sprintf('periods/%s/%s/%s/transactions.%s', $this->token, DateTime::from($from)->format('Y-m-d'), DateTime::from($to)->format('Y-m-d'), $this->getParser()->getExtension());
        $this->availableAnotherRequest();
        return $this->getParser()->parse(CurlBuilder::download($this->requestUrl));
    }

    /**
     * Oficiální výpisy pohybů z účtu
     *
     * @param int $id
     * @param int|string $year format YYYY
     * @return IFile
     */
    public function movementId($id, $year = NULL) {
        if ($year === NULL) {
            $year = date('Y');
        }
        $this->requestUrl = self::REST_URL . sprintf('by-id/%s/%s/%s/transactions.%s', $this->token, $year, $id, $this->getParser()->getExtension());
        $this->availableAnotherRequest();
        return $this->getParser()->parse(Curl::download($this->requestUrl));
    }

    /**
     * Pohyby na účtu od posledního stažení
     *
     * @return IFile
     */
    public function lastDownload() {
        $this->requestUrl = self::REST_URL . sprintf('last/%s/transactions.%s', $this->token, $this->getParser()->getExtension());
        $this->availableAnotherRequest();
        return $this->getParser()->parse(Curl::download($this->requestUrl));
    }

// <editor-fold defaultstate="collapsed" desc="Breakpoints">
    /**
     * Na ID posledního úspěšně staženého pohybu
     *
     * @param int $moveId
     * @return string
     */
    public function setLastId($moveId) {
        $this->requestUrl = self::REST_URL . sprintf('set-last-id/%s/%s/', $this->token, $moveId);
        $this->availableAnotherRequest();
        return CurlBuilder::download($this->requestUrl);
    }

    /**
     * Na datum posledního neúspěšně staženého dne
     *
     * @param mixed $date
     * @return string
     */
    public function setLastDate($date) {
        $this->requestUrl = self::REST_URL . sprintf('set-last-date/%s/%s/', $this->token, DateTime::from($date)->format('Y-m-d'));
        $this->availableAnotherRequest();
        return CurlBuilder::download($this->requestUrl);
    }

// </editor-fold>
    /**
     * Last Request url for read
     *
     * @return string
     */
    public function getRequestUrl() {
        return $this->requestUrl;
    }

    /** @return IFile */
    public function getParser() {
        return $this->getLastResponse();
    }

    /** @return IFile */
    public function getLastResponse() {
        if ($this->parser === NULL) {
            $this->loadParser(IFile::JSON);
        }
        return $this->parser;
    }

    /**
     *
     * @param string $file
     */
    public function setDownloadFile($file) {
        $this->parser = NULL;
        $this->loadParser($file);
        return $this;
    }

    /**
     * Prepare object for parse data
     *
     * @param string|IFile $parser
     * @return Fio
     * @throws FioException
     */
    private function loadParser($parser) {
        if ($parser instanceof IFile) {
            $this->parser = $parser;
        } elseif (is_string($parser)) {
            $class = __NAMESPACE__ . '\Fio\Files\\' . ucfirst($parser);
            $this->parser = new $class;
            return $this;
        }
        throw new FioException('Parser is\'t supported. Must be Instance of IFile or string as constant from IFile.');
    }

    /**
     * Interval between requests is 30s, import / read
     */
    private function availableAnotherRequest() {
        if (file_exists($this->tempFile)) {
            $diff = (file_get_contents($this->tempFile) + self::API_INTERVAL) - time();
            if ($diff > 0) {
                sleep($diff);
            }
        }
        file_put_contents($this->tempFile, time());
    }

    /** @return string */
    public function getAccount() {
        return $this->account;
    }

    /**
     * WRITE *******************************************************************
     * *************************************************************************
     */

    /**
     * Factory for Fio\XMLFio
     *
     * @return Fio\XMLFio
     */
    public function createXmlFio() {
        return new Fio\XMLFio($this->account, $this->temp);
    }

    /**
     * Set upload file extension
     *
     * @param string $str
     * @return Fio
     * @throws FioException
     */
    protected function setUploadExtenstion($str) {
        $str = strtolower($str);
        $extension = array('xml', 'abo');
        if (!in_array($str, $extension)) {
            throw new FioException('Unsupported file upload format: ' . $str . ' avaible are ' . implode(', ', $extension));
        }
        $this->uploadExtension = $str;
        return $this;
    }

    /**
     * Respons language
     *
     * @param string $str
     * @return Fio
     * @throws FioException
     */
    public function setLanguage($str) {
        $str = strtolower($str);
        $extension = array('en', 'cs', 'sk');
        if (!in_array($str, $extension)) {
            throw new FioException('Unsupported language: ' . $str . ' avaible are ' . implode(', ', $extension));
        }
        $this->language = $str;
        return $this;
    }

// <editor-fold defaultstate="collapsed" desc="Payment orders by file">

    /**
     * Read from path
     *
     * @param string $filename
     * @return XMLResponse
     */
    public function uploadFile($filename) {
        $this->setUploadExtenstion(pathinfo($filename, PATHINFO_EXTENSION));
        return $this->send($filename);
    }

    /**
     *
     * @param Fio\XMLFio $xml
     * @return XMLResponse
     */
    public function uploadXmlFio(Fio\XMLFio $xml) {
        $this->setUploadExtenstion('xml');
        return $this->send($xml->getPathname());
    }

    /** @return XMLResponse */
    public function getUploadResponse() {
        return $this->response;
    }

// </editor-fold>

    /**
     * @todo check filesize ??? 2MB in documentation
     * @param string $filename
     * @return XMLResponse
     * @throws FioException
     */
    private function send($filename) {
        $curl = new CUrl(self::REST_URL_WRITE);
        $curl->setOptions(array(
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_POST => 1,
            CURLOPT_VERBOSE => 0,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTPHEADER => array('Content-Type: multipart/form-data; charset=utf-8;'),
            CURLOPT_POSTFIELDS => array(
                'type' => $this->uploadExtension,
                'token' => $this->token,
                'lng' => $this->language,
                'file' => $curl->fileCreate($filename)
            ))
        );
        $this->availableAnotherRequest();
        $xml = trim($curl->exec());
        if (!$xml) {
            throw new FioException('FIO server is not responding.', 500);
        }

        return $this->response = new XMLResponse($xml);
    }

}
