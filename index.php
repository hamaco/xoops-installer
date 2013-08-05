<?php

require __DIR__.'/bootstrap.php';

$controller = new \XoopsCube\Installer\Controller\DefaultController($installerConfig, $container);
$controller->run();
$controller->respond();
