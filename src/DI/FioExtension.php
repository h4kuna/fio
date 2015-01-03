<?php

namespace h4kuna\Fio\DI;

use Nette\Configurator;
use Nette\DI\CompilerExtension;
use Nette\DI\Compiler;

if (defined('\Nette\Framework::VERSION_ID') || Framework::VERSION_ID < 20100) {
    if (!class_exists('Nette\DI\CompilerExtension')) {
        class_alias('Nette\Config\CompilerExtension', 'Nette\DI\CompilerExtension');
    }

    if (!class_exists('Nette\DI\Compiler')) {
        class_alias('Nette\Config\Compiler', 'Nette\DI\Compiler');
    }
}

class FioExtension extends CompilerExtension {

    public $defaults = array(
        'account' => NULL,
        'token' => NULL,
        'temp' => '%tempDir%/fio'
    );

    public function loadConfiguration() {
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig($this->defaults);

        if (!$config['token']) {
            throw new \h4kuna\Fio\FioException('Token is required');
        }

        if (!$config['account']) {
            throw new \h4kuna\Fio\FioException('Account is required');
        }

        $builder->addDefinition($this->prefix('fio'))
                ->setClass('h4kuna\Fio')
                ->setArguments(array($config['token'], $config['account'], $config['temp']));
    }

    /**
     * @param \Nette\Configurator $configurator
     */
    public static function register(Configurator $configurator) {
        $that = new static;
        $configurator->onCompile[] = function ($config, Compiler $compiler) use ($that) {
            $compiler->addExtension('fioExtension', $that);
        };
    }

}
