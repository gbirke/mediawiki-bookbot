<?php

namespace Birke\Mediawiki\Bookbot;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Bridge\Monolog\Handler\ConsoleHandler;
use Monolog\Logger;

use Birke\Mediawiki\Api\MediawikiApiClient;
use Birke\Mediawiki\Api\Session;

/**
 * ConvertCommand does a standalone conversion of a wiki page
 * 
 * It's mostly for testing the conversion chain
 *
 * @author birkeg
 */
class CleanUpdateCommand extends Command {
    
    /**
     *
     * @var MediawikiApiClient
     */
    protected $client;
    
    /**
     *
     * @var Monolog/Logger
     */
    protected $logger;
    
    protected function configure()
    {
        $this
            ->setName('cleanupdate')
            ->setDescription('updatetoc and cleanheadlines')
            ->addArgument(
                'page',
                InputArgument::OPTIONAL,
                'Wiki page name (use all pages in books category if no page name is given)'
            )
            
            ->addOption(
                    "api_url",
                    NULL,
                    InputOption::VALUE_REQUIRED,
                    'Wiki API URL',
                    'http://10.0.3.5/w/api.php'
            )
            ->addOption(
                    "username",
                    NULL,
                    InputOption::VALUE_REQUIRED,
                    'Wiki API username',
                    'TocBot'
            )
            ->addOption(
                    "password",
                    NULL,
                    InputOption::VALUE_REQUIRED,
                    'Wiki API password',
                    'tb12345678'
            )
            ->addOption(
					"preamble",
            		NULL,
            		InputOption::VALUE_REQUIRED,
            		'Include text for printeversion ',
            		'none'
    	    )
                
        ;
    }
	protected function execute(InputInterface $input, OutputInterface $output) {
		
		$cmds = array('cleanheadlines', 'updatetoc');
		//todo? get this infos from the command
		$shared_args = array(
			'page' => $input->getArgument('page'),
		);
		$shared_opt_names = array('api_url', 'username', 'password');
		foreach($shared_opt_names as $name){
			$shared_args["--$name"] = $input->getOption($name);
		}
		
		$extraopts = array(
			'updatetoc'=>array('preamble'),
		);
		
		foreach($cmds as $cmd){
			$command = $this->getApplication ()->find($cmd);
			$arguments = array_merge(
				$shared_args,
				array('command'=>$cmd)
			);
			if (isset($extraopts[$cmd])){
				foreach($extraopts[$cmd] as $opt){
					$arguments["--$opt"] = $input->getOption($opt);
				}
			}
			$inputcpy = new ArrayInput($arguments);
			
			$returnCode = $command->run ( $inputcpy, $output );
		}
	}
   
}
