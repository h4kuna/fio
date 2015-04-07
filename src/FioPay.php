<?php

namespace h4kuna\Fio;

use h4kuna\Fio\Request\Pay\Payment\National;
use h4kuna\Fio\Request\Pay\Payment\Property;
use h4kuna\Fio\Request\Pay\PaymentFactory;
use h4kuna\Fio\Request\Pay\XMLFile;
use h4kuna\Fio\Response\Pay\IResponse;
use h4kuna\Fio\Response\Pay\XMLResponse;
use h4kuna\Fio\Utils\FioException;
use Kdyby\Curl\CurlSender;
use Kdyby\Curl\Request;

class FioPay extends Fio
{

    /** @var string[] */
    private static $langs = array('en', 'cs', 'sk');

    /** @var string */
    private $uploadExtension;

    /** @var string */
    private $language = 'cs';

    /** @var XMLResponse */
    private $response;

    /** @var PaymentFactory */
    private $paymentFatory;

    /** @var XMLFile */
    private $xmlFile;

    /** @var Utils\Context */
    private $context;

    public function __construct(Utils\Context $context, PaymentFactory $paymentFactory, XMLFile $xmlFile)
    {
        $this->paymentFatory = $paymentFactory;
        $this->xmlFile = $xmlFile;
        $this->context = $context;
    }

    /** @return IResponse */
    public function getUploadResponse()
    {
        return $this->response;
    }

    /** @return National */
    public function createNational($amount, $accountTo, $bankCode = NULL)
    {
        return $this->paymentFatory->createNational($amount, $accountTo, $bankCode);
    }

    /**
     * Response language.
     *
     * @param string $lang
     * @return self
     * @throws FioException
     */
    public function setLanguage($lang)
    {
        $lang = strtolower($lang);
        if (!in_array($lang, self::$langs)) {
            throw new FioException('Unsupported language: ' . $lang . ' avaible are ' . implode(', ', self::$langs));
        }
        $this->language = $lang;
        return $this;
    }

    /**
     * @todo check filesize ??? 2MB in documentation
     * @param string $filename
     * @return IResponse
     * @throws FioException
     */
    public function send($filename)
    {
        if ($filename instanceof Property) {
            $this->setUploadExtenstion('xml');
            $filename = $this->xmlFile->setData($filename)->getPathname();
        } elseif (is_file($filename)) {
            $this->setUploadExtenstion(pathinfo($filename, PATHINFO_EXTENSION));
        } else {
            throw new FioException('Is supported only filepath or Property object.');
        }

        return $this->response = $this->context->getQueue()->upload($this->context->getToken(), $this->createCurl($filename));
    }

    /**
     * Set upload file extension.
     *
     * @param string $extension
     * @return self
     * @throws FioException
     */
    private function setUploadExtenstion($extension)
    {
        $extension = strtolower($extension);
        static $extensions = array('xml', 'abo');
        if (!in_array($extension, $extensions)) {
            throw new FioException('Unsupported file upload format: ' . $extension . ' avaible are ' . implode(', ', $extensions));
        }
        $this->uploadExtension = $extension;
        return $this;
    }

    /** @return CUrl */
    private function createCurl($filename)
    {
        $request = new Request($this->getUrl());
        $request->setPost(array(
            'type' => $this->uploadExtension,
            'token' => $this->context->getToken(),
            'lng' => $this->language,
                ), array(
            'file' => $filename
        ));

        $curl = new CurlSender();
        $curl->setTimeout(30);
        $curl->options = array(
            'verbose' => 0,
            'ssl_verifypeer' => 0,
            'ssl_verifyhost' => 2,
                # 'httpheader' => 'Content-Type: multipart/form-data; charset=utf-8;'
                ) + $curl->options;
        $request->setSender($curl);
        return $request;
    }

    protected function getUrl()
    {
        return $this->context->getUrl() . 'import/';
    }

}
