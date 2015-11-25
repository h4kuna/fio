<?php

namespace h4kuna\Fio\Nette\DI;

use Nette\DI\CompilerExtension,
	Nette\Utils;

class FioExtension extends CompilerExtension
{

	public $defaults = array(
		'account' => NULL, // @deprecated
		'token' => NULL, // @deprecated
		'accounts' => array(),
		'temp' => '%tempDir%/fio',
		'transactionClass' => '\h4kuna\Fio\Response\Read\Transaction'
	);

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		if (!$config['accounts']) { // back compatibility
			$config['accounts']['default'] = array(
				'account' => $config['account'],
				'token' => $config['token']
			);
		}
		unset($config['account'], $config['token']);

		Utils\FileSystem::createDir($config['temp']);

		// Accounts
		$builder->addDefinition($this->prefix('accounts'))
				->setClass('h4kuna\Fio\Security\Accounts')
				->setFactory('h4kuna\Fio\Security\AccountsFactory::create', array($config['accounts']));

		// XMLFile
		$builder->addDefinition($this->prefix('xmlFile'))
				->setClass('h4kuna\Fio\Request\Pay\XMLFile')
				->setArguments(array($config['temp']));

		// PaymentFactory
		$builder->addDefinition($this->prefix('paymentFactory'))
				->setClass('h4kuna\Fio\Request\Pay\PaymentFactory')
				->setArguments(array($config['account']));

		// Queue
		$builder->addDefinition($this->prefix('queue'))
				->setClass('h4kuna\Fio\Request\Queue')
				->setArguments(array($config['temp']));

		// Context
		$builder->addDefinition($this->prefix('context'))
				->setClass('h4kuna\Fio\Utils\Context')
				->setArguments(array($this->prefix('queue'), $this->prefix('accounts')));

		// StatementFactory
		$builder->addDefinition($this->prefix('statementFactory'))
				->setClass('h4kuna\Fio\Response\Read\JsonStatementFactory')
				->setArguments(array($config['transactionClass']));

		// FioPay
		$builder->addDefinition($this->prefix('fioPay'))
				->setClass('h4kuna\Fio\FioPay');

		// FioRead
		$builder->addDefinition($this->prefix('fioRead'))
				->setClass('h4kuna\Fio\FioRead')
				->setArguments(array($this->prefix('@context')));
	}

}
