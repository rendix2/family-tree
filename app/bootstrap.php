<?php

use Tracy\Debugger;

require __DIR__ . '/../vendor/autoload.php';

RadekDostal\NetteComponents\DateTimePicker\TbDatePicker::register();

$configurator = new Nette\Configurator;

//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
$configurator->enableTracy(__DIR__ . '/../log');

$configurator->setTimeZone('Europe/Prague');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');

if (Debugger::$productionMode === Debugger::DEVELOPMENT) {
    $configurator->addConfig(__DIR__ . '/config/config.local.neon');
} elseif (Debugger::$productionMode === Debugger::PRODUCTION) {
    $configurator->addConfig(__DIR__ . '/config/config.production.neon');
}

$container = $configurator->createContainer();

return $container;
