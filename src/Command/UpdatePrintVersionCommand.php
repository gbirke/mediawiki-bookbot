<?php

namespace Birke\Mediawiki\Bookbot\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bridge\Monolog\Handler\ConsoleHandler;
use Monolog\Logger;

use Birke\Mediawiki\Api\MediawikiApiClient;
use Birke\Mediawiki\Api\Session;
use Birke\Mediawiki\Bookbot\ApiConnector;
use Birke\Mediawiki\Bookbot\PrintVersionGenerator;
use Birke\Mediawiki\Bookbot\BookMetadata;

/**
 * Create a print version of a book
 *
 * @author birkeg
 */
class UpdatePrintVersionCommand extends Command
{
    
    protected function configure()
    {
        $this
            ->setName('updateprintversion')
            ->setDescription('Update print version of books')
            ->addArgument(
                'page',
                InputArgument::OPTIONAL,
                'Wiki page name (use all pages in books category if no page name is given)'
            )
            
            ->addOption(
                "api_url",
                null,
                InputOption::VALUE_REQUIRED,
                'Wiki API URL',
                'http://10.0.3.5/w/api.php'
            )
            ->addOption(
                "username",
                null,
                InputOption::VALUE_REQUIRED,
                'Wiki API username',
                'TocBot'
            )
            ->addOption(
                "password",
                null,
                InputOption::VALUE_REQUIRED,
                'Wiki API password',
                'tb12345678'
            )
            ->addOption(
                "preamble",
                null,
                InputOption::VALUE_REQUIRED,
                'Include text for print version ',
                'none'
            )
            ->addOption(
                "print_title",
                null,
                InputOption::VALUE_REQUIRED,
                'Title of print page. If this is a subpage, it will be created as-is.',
                '_Print_Version'
            )
                
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->createLogger($output);
        $session = $this->createSession($input->getOption('api_url'));
        $session->login(
            $input->getOption('username'),
            $input->getOption('password')
        );
        $connector = new ApiConnector($session, $logger);
        $generator = new PrintVersionGenerator($connector, $logger);
        $preamble = $input->getOption('preamble');
        $bookPages = $this->getPageTitles($input->getArgument('page'), $connector);
        $printTitle = $input->getOption('print_title');
        
        if (count($bookPages) > 1 && strpos($printTitle, "/")) {
            $output->writeln("When specifying a subpage as print page title you can only export one book at a time.");
            exit(2);
        }
        
        foreach ($bookPages as $page) {
            $metadata = new BookMetadata($page, $printTitle);
            $generator->generatePrintpageForTitle($metadata, $preamble);
        }
        
        $session->logout();
    }
    
    protected function createLogger(OutputInterface $output)
    {
        // TODO create new logger according to verbose/quiet config
        $logger = new Logger('tocupdate');
        $logger->pushHandler(new ConsoleHandler($output));
        return $logger;
    }
    
    protected function createSession($baseUrl)
    {
        $client = MediawikiApiClient::factory(array('base_url' => $baseUrl));
        return new Session($client);
    }
    
    /**
     * Return array of page title for which a print page should be generated
     * 
     * @param string $pageTitle
     * @param \Birke\Mediawiki\Bookbot\ApiConnector $connector
     * @return type
     */
    protected function getPageTitles($pageTitle, ApiConnector $connector)
    {
        if ($pageTitle) {
            $titleArr = array($pageTitle);
        } else {
            $titleArr = $connector->getAllPageTitlesInCategory("Buch");
        }
        return $titleArr;
    }
}
