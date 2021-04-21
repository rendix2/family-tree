<?php

namespace App;

use Nette\Configurator;
use Tracy\Debugger;

/**
 * Class Bootstrap
 *
 * @package App
 */
class Bootstrap
{
    /**
     * @var Configurator $configurator
     */
    private $configurator;

    public function __construct()
    {
        require __DIR__ . '/../vendor/autoload.php';

        $this->configurator = new Configurator();
    }

    public function __destruct()
    {
        $this->configurator = null;
    }

    public function bootDefault()
    {
        // $configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
        $this->configurator->enableTracy(__DIR__ . '/../log');

        $this->configurator->setTimeZone('Europe/Prague');
        $this->configurator->setTempDirectory(__DIR__ . '/../temp');

        $this->configurator->createRobotLoader()
            ->addDirectory(__DIR__)
            ->register();

        $this->configurator->addConfig(__DIR__ . '/config/config.neon');

        if (Debugger::$productionMode === Debugger::DEVELOPMENT) {
            $this->configurator->addConfig(__DIR__ . '/config/config.local.neon');
        } elseif (Debugger::$productionMode === Debugger::PRODUCTION) {
            $this->configurator->addConfig(__DIR__ . '/config/config.production.neon');
        }

        return $this->configurator->createContainer();
    }
}
