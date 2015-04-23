<?php

namespace Birke\Mediawiki\Tocbot;

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
 * Put headlines on the correct level (observing the TOC)
 * 
 * @author birkeg
 */
class CleanHeadlinesCommand extends Command {
    
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
            ->setName('cleanheadlines')
            ->setDescription('Set headlines of subpages to correct level')
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
        
        $connector = new ApiConnector($session, $logger);
        $cleaner = new HeadlineCleaner($connector, $logger);
        //$cleaner->updateSubpage("Handbuch CoScience/Publikation von Forschungsdaten", 2);
        
        
        $title = $input->getArgument('page');
        
        if ($title) {
            $titleArr = array($title);
        }
        else {
            $titleQuery = new TitlesInCategoryQuery($this->client);
            $titleArr = $titleQuery->getAllTitles("Buch");
        }
        
        foreach ($titleArr as $title) {
            $cleaner->cleanupSubpages($title);
        }
        
        $session->logout();
        
    }
   
}
