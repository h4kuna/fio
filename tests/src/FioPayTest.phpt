<?php

namespace h4kuna\Fio;

use Tester,
    Tester\Assert,
	h4kuna\Fio\Test;

$container = require_once __DIR__ . '/../bootstrap.php';

/**
 * @author Milan Matějček
 */
class FioPayTest extends Tester\TestCase
{

    /** @var FioPay */
    private $fioPay;

    /** @var \Nette\DI\Container */
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    protected function setUp()
    {
        $this->fioPay = $this->container->createService('fioExtension.fioPay');
    }

    public function testSend()
    {
		$xml = Test\Utils::getContent('payment/response.xml');
        $xmlResponse = new Response\Pay\XMLResponse($xml);
		Assert::true($xmlResponse->isOk());
		Assert::equal('1247458', $xmlResponse->getIdInstruction());
    }

}

$test = new FioPayTest($container);
$test->run();
