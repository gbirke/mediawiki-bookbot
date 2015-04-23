<?php

/* 
 */

use Symfony\Component\Console\Application;
use Birke\Mediawiki\Bookbot\Command\UpdateCommand;
use Birke\Mediawiki\Bookbot\Command\CleanHeadlinesCommand;
use Birke\Mediawiki\Bookbot\Command\CleanUpdateCommand;

require_once 'vendor/autoload.php';

$application = new Application();
$application->add(new UpdateCommand);
$application->add(new CleanHeadlinesCommand);
$application->add(new CleanUpdateCommand);
$application->run();
