<?php

namespace h4kuna\Fio;

use h4kuna\Fio\Request\Pay,
    h4kuna\Fio\Utils;

class FioPay extends Fio
{

    /** @var string[] */
    private static $langs = array('en', 'cs', 'sk');

    /** @var string */
    private $uploadExtension;

    /** @var string */
    private $language = 'cs';

    /** @var Pay\XMLResponse */
    private $response;

    /** @var Pay\PaymentFactory */
    private $paymentFatory;

    /** @var Pay\XMLFile */
    private $xmlFile;

    /** @var Utils\Context */
    private $context;

    public function __construct(Utils\Context $context, Pay\PaymentFactory $paymentFactory, Pay\XMLFile $xmlFile)
    {
        $this->paymentFatory = $paymentFactory;
        $this->xmlFile = $xmlFile;
        $this->context = $context;
    }

    /** @return Pay\Payment\Euro */
    public function createEuro($amount, $accountTo, $bic, $name, $country)
    {
        return $this->paymentFatory->createEuro($amount, $accountTo, $bic, $name, $country);
    }

    /** @return Pay\Payment\National */
    public function createNational($amount, $accountTo, $bankCode = NULL)
    {
        return $this->paymentFatory->createNational($amount, $accountTo, $bankCode);
    }

    /** @return Pay\Payment\International */
    public function createInternational($amount, $accountTo, $bic, $name, $street, $city, $country, $info)
    {
        return $this->paymentFatory->createInternational($amount, $accountTo, $bic, $name, $street, $city, $country, $info);
    }

    /** @return Pay\IResponse */
    public function getUploadResponse()
    {
        return $this->response;
    }

    /**
     * @param Pay\Payment\Property $property
     * @return self
     */
    public function addPayment(Pay\Payment\Property $property)
    {
        $this->xmlFile->setData($property);
        return $this;
    }

    /**
     * @todo check filesize ??? 2MB in documentation
     * @param string|Pay\Payment\Property $filename
     * @return Pay\IResponse
     * @throws Utils\FioException
     */
    public function send($filename = NULL)
    {
        if ($filename instanceof Pay\Payment\Property) {
            $this->xmlFile->setData($filename);
        }

        if ($this->xmlFile->isReady()) {
            $this->setUploadExtenstion('xml');
            $filename = $this->xmlFile->getPathname();
        } elseif (is_file($filename)) {
            $this->setUploadExtenstion(pathinfo($filename, PATHINFO_EXTENSION));
        } else {
            throw new Utils\FioException('Is supported only filepath or Property object.');
        }

        $post = array(
            'type' => $this->uploadExtension,
            'token' => $this->context->getToken(),
            'lng' => $this->language,
        );

        return $this->response = $this->context->getQueue()->upload($this->getUrl(), $post, $filename);
    }

    /**
     * Response language.
     *
     * @param string $lang
     * @return self
     * @throws Utils\FioException
     */
    public function setLanguage($lang)
    {
        $lang = strtolower($lang);
        if (!in_array($lang, self::$langs)) {
            throw new Utils\FioException('Unsupported language: ' . $lang . ' avaible are ' . implode(', ', self::$langs));
        }
        $this->language = $lang;
        return $this;
    }

    /** @return string */
    private function getUrl()
    {
        return $this->context->getUrl() . 'import/';
    }

    /**
     * Set upload file extension.
     *
     * @param string $extension
     * @return self
     * @throws Utils\FioException
     */
    private function setUploadExtenstion($extension)
    {
        $extension = strtolower($extension);
        static $extensions = array('xml', 'abo');
        if (!in_array($extension, $extensions)) {
            throw new Utils\FioException('Unsupported file upload format: ' . $extension . ' avaible are ' . implode(', ', $extensions));
        }
        $this->uploadExtension = $extension;
        return $this;
    }

}
