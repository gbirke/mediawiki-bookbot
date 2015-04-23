<?php

namespace Birke\Mediawiki\Bookbot;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
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
class UpdateCommand extends Command {
    
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
            ->setName('updatetoc')
            ->setDescription('Update book TOCs')
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Initialize Logger
        // TODO create new logger according to config
        $this->logger = $logger = new Logger('tocupdate');
        $this->logger->pushHandler(new ConsoleHandler($output));
        
        // Initialize client
        $this->client =  MediawikiApiClient::factory(array('base_url' => $input->getOption('api_url')));
        $session = new Session($this->client);
        $session->login($input->getOption('username'), $input->getOption('password'));
        
        $title = $input->getArgument('page');
        
        if ($title) {
            $titleArr = array($title);
        }
        else {
            $titleQuery = new TitlesInCategoryQuery($this->client);
            $titleArr = $titleQuery->getAllTitles("Buch");
        }
        
        $connector = new ApiConnector($session, $logger);
        $generator = new TocGenerator($connector, $logger);
        foreach ($titleArr as $title) {
            // TODO: Cleanup headlines of subpages
            $generator->generateTocForTitle($title);
            $generator->generatePrintpageForTitle($title, $preamble=$input->getOption('preamble'));
        }
        
        $session->logout();
        
    }
   
}
