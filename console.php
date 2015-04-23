<?php

/* 
 */

use Symfony\Component\Console\Application;
use Birke\Mediawiki\Tocbot\UpdateCommand;
use Birke\Mediawiki\Tocbot\CleanHeadlinesCommand;
use Birke\Mediawiki\Tocbot\CleanUpdateCommand;

require_once 'vendor/autoload.php';

$application = new Application();
$application->add(new UpdateCommand);
$application->add(new CleanHeadlinesCommand);
$application->add(new CleanUpdateCommand);
$application->run();
