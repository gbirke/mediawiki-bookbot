<?php

/* 
 */

use Symfony\Component\Console\Application;
use Birke\Mediawiki\Bookbot\Command\UpdatePrintVersionCommand;

require_once 'vendor/autoload.php';

$application = new Application();
$application->add(new UpdatePrintVersionCommand);
$application->run();
