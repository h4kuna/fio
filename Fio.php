<?php

namespace h4kuna\fio;

use Nette;
use h4kuna\fio\reader\IFile;

require_once 'reader/File.php';

class Fio extends Nette\Object {

    const FIO_API_VERSION = '1.0.5';

    private $token;
    private $reader;

    public function __construct($token, $reader = IFile::GPC) {
        $this->token = $token;
        $this->loadReader($reader);
    }

    private function loadReader($reader) {
        if ($reader instanceof IFile) {
            $this->reader = $reader;
        } else {
            $class = ucfirst($reader);
            $file = __DIR__ . '/reader/' . $class . '.php';
            if (file_exists($file)) {
                require_once $file;
                $this->reader = new $class;
            } else {
                throw new \RuntimeException('File no found: ' . $file);
            }
        }
        return $this;
    }

    /**
     * @param string|int|\Datetime $from
     * @param string|int|\Datetime $to
     * @return \Fio\GpcParser
     */
    public function import($from = '-1 month', $to = 'now') {
        $format = 'd.m.Y';

        dump($fio);
        vsprintf('https://www.fio.cz/ib_api/rest/periods/%s/%s/%s/transactions.%s');


        $from = Nette\DateTime::from($from);
        $to = Nette\DateTime::from($to);

        $requestURL = "https://www.fio.cz/scgi-bin/hermes/dz-pohyby.cgi?ID_ucet={$this->account}" .
                "&LOGIN_USERNAME={$this->userName}&SUBMIT=Odeslat&LOGIN_TIME=" . time() .
                "&LOGIN_PASSWORD={$this->password}&pohyby_DAT_od={$from->format($format)}" .
                "&pohyby_DAT_do={$to->format($format)}&export_gpc=1";
        $curl = new CUrl($requestURL, array(
                    CURLOPT_USERAGENT => $this->userAgent,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_HEADER => FALSE,
                    CURLOPT_SSL_VERIFYPEER => FALSE,
                    CURLOPT_HTTPGET => TRUE,
                    CURLOPT_HTTPHEADER => array('Content-Type: text/plain', 'Connection: Close')
                ));

        return new GpcParser($curl->exec(), $this->filter);
    }

}
