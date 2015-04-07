<?php

namespace h4kuna\Fio;

use h4kuna\Fio\Request\Read\IFile;
use h4kuna\Fio\Request\Read\IFileFactory;
use Nette\Utils\DateTime;

/**
 * Read from informtion Fio account
 */
class FioRead extends Fio
{

    /** @var string */
    private $requestUrl;

    /** @var IFile */
    private $parser;

    /** @var Utils\Context */
    private $context;

    public function __construct(Utils\Context $context)
    {
        $this->context = $context;
    }

    /**
     * Movements in date range.
     *
     * @param string|int|DateTime $from
     * @param string|int|DateTime $to
     * @return Response\Read\TransactionList
     */
    public function movements($from = '-1 week', $to = 'now')
    {
        $data = $this->download('periods/%s/%s/%s/transactions.%s', DateTime::from($from)->format('Y-m-d'), DateTime::from($to)->format('Y-m-d'), $this->getParser()->getExtension());
        return $this->parseData($data);
    }

    /**
     * List of movemnts.
     *
     * @param int $id
     * @param int|string|NULL $year format YYYY, NULL is current
     * @return IFile
     */
    public function movementId($id, $year = NULL)
    {
        if ($year === NULL) {
            $year = date('Y');
        }
        $data = $this->download('by-id/%s/%s/%s/transactions.%s', $year, $id, $this->getParser()->getExtension());
        return $this->parseData($data);
    }

    /**
     * Last movements from last breakpoint.
     *
     * @return IFile
     */
    public function lastDownload()
    {
        $data = $this->download('last/%s/transactions.%s', $this->getParser()->getExtension());
        return $this->parseData($data);
    }

    /**
     * Set break point to id.
     *
     * @param int $moveId
     * @return string
     */
    public function setLastId($moveId)
    {
        return $this->download('set-last-id/%s/%s/', $moveId);
    }

    /**
     * Set breakpoint to date.
     *
     * @param mixed $date
     * @return string
     */
    public function setLastDate($date)
    {
        return $this->download('set-last-date/%s/%s/', DateTime::from($date)->format('Y-m-d'));
    }

    /**
     * Last request url for read. This is for tests.
     *
     * @return string
     */
    public function getRequestUrl()
    {
        return $this->requestUrl;
    }

    /**
     *
     * @param IFile $file
     * @return \h4kuna\Fio\FioRead
     */
    public function setParser(IFile $file)
    {
        $this->parser = $file;
        return $this;
    }

    /** @return IFile */
    private function getParser()
    {
        if ($this->parser === NULL) {
            $this->parser = new Request\Read\Files\Json();
        }
        return $this->parser;
    }

    private function parseData($data)
    {
        return $this->getParser()->parse($data);
    }

    private function download($apiUrl /* ... params */)
    {
        $args = func_get_args();
        $args[0] = $this->context->getToken();
        $this->requestUrl = $this->context->getUrl() . vsprintf($apiUrl, $args);
        return $this->context->getQueue()->download($this->context->getToken(), $this->requestUrl);
    }

}
