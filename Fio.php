<?php

namespace h4kuna;

use Nette\Object;
use Nette\DateTime;
use h4kuna\CUrl;
use h4kuna\Fio\FioException;
use h4kuna\Fio\IFile;
use h4kuna\Fio\XMLResponse;

/**
 * Začne li se sestavovat tělo požadavku je potřeba uzamknout extension a pokud se nastaví cesta k souboru tak zamknout přidávání do těla
 *
 * Read FIO account
 */
class Fio extends Object {

    /** @var string */
    const FIO_API_VERSION = '1.2.6';

    /** @var string */
    const REST_URL = 'https://www.fio.cz/ib_api/rest/';

    /** @var string */
    const REST_URL_WRITE = 'https://www.fio.cz/ib_api/rest/import/';

    /** @var string */
    private $uploadExtension;

    /** @var string */
    private $language = 'en';

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

    /**
     * @param string $token
     * @param string|IFile $parser
     */
    public function __construct($token, $parser = IFile::JSON) {
        $this->token = $token;
        $this->loadParser($parser);
    }

    /**
     * Pohyby na účtu za určené období
     *
     * @param string|int|DateTime $from
     * @param string|int|DateTime $to
     * @return IFile
     */
    public function movements($from = '-1 month', $to = 'now') {
        $url = self::REST_URL . sprintf('periods/%s/%s/%s/transactions.%s', $this->token, DateTime::from($from)->format('Y-m-d'), DateTime::from($to)->format('Y-m-d'), $this->parser->getExtension());
        return $this->parser->parse(CUrl::download($url));
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
        $url = self::REST_URL . sprintf('by-id/%s/%s/%s/transactions.%s', $this->token, $year, $id, $this->parser->getExtension());
        return $this->parser->parse(Curl::download($url));
    }

    /**
     * Pohyby na účtu od posledního stažení
     *
     * @return IFile
     */
    public function lastDownload() {
        $url = self::REST_URL . sprintf('last/%s/transactions.%s', $this->token, $this->parser->getExtension());
        return $this->parser->parse(Curl::download($url));
    }

// <editor-fold defaultstate="collapsed" desc="Breakpoints">
    /**
     * Na ID posledního úspěšně staženého pohybu
     *
     * @param int $moveId
     * @return string
     */
    public function setLastId($moveId) {
        $url = self::REST_URL . sprintf('set-last-id/%s/%s/', $this->token, $moveId);
        return CUrl::download($url);
    }

    /**
     * Na datum posledního neúspěšně staženého dne
     *
     * @param mixed $date
     * @return string
     */
    public function setLastDate($date) {
        $url = self::REST_URL . sprintf('set-last-date/%s/%s/', $this->token, DateTime::from($date)->format('Y-m-d'));
        return CUrl::download($url);
    }

// </editor-fold>

    /** @return IFile */
    public function getLastResponse() {
        return $this->parser;
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
     * WRITE *******************************************************************
     * *************************************************************************
     */

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
        return $this->uploadFileContent(file_get_contents($filename));
    }

    /**
     *
     * @param string $str
     * @return XMLResponse
     */
    public function uploadFileContent($str) {
        $this->setUploadExtenstion(preg_match('~^\<\?xml~', $str) ? 'xml' : 'abo');
        return $this->send($str);
    }

    /**
     *
     * @param Fio\XMLFio $xml
     * @return XMLResponse
     */
    public function uploadXmlFio(Fio\XMLFio $xml) {
        return $this->uploadFileContent((string) $xml);
    }

    /** @return XMLResponse */
    public function getUploadResponse() {
        return $this->response;
    }

// </editor-fold>

    /**
     *
     * @param string $content
     * @return XMLResponse
     */
    protected function send($content) {
        $data = array(
            'type' => $this->uploadExtension,
            'token' => $this->token,
            'lng' => $this->language,
            'file' => array('content' => $content, 'name' => 'generated', 'type' => 'text/plain')
        );

        $curl = Curl::postUploadFile(self::REST_URL_WRITE, $data);
        return $this->response = new XMLResponse($curl->exec());
    }

}
