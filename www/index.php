<?php

use App\Bootstrap;
use Nette\Application\Application;

require __DIR__ . '/../app/Bootstrap.php';

$bootstrap = new Bootstrap();
$container = $bootstrap->bootDefault();

$container->getByType(Application::class)
	->run();
