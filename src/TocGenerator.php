<?php

/**
 * This file contains the class TocGenerator
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot;

use \Monolog\Logger;

/**
 * Update TOC and print version from an existing TOC list
 *
 * @author birkeg
 */
class TocGenerator
{
    
    /**
     *
     * @var ApiConnector
     */
    protected $conn;
    
    /**
     *
     * @var \Monolog\Logger
     */
    protected $logger;
    
    public function __construct(ApiConnector $conn, Logger $logger)
    {
        $this->conn = $conn;
        $this->logger = $logger;
    }

    public function generateTocForTitle($title)
    {
        // Download page text
        $text = $this->conn->downloadPageText($title);
        $tocData = new TocText($text);
        if (!$tocData->tocExists()) {
            $this->logger->notice("Page '$title' has no TOC.");
            return;
        }
        $this->booktitle = $title;
        // get title and headings from each page
        $toc = $this->getTocFromTitles($tocData);
        
        // Build wikitext from toc (indent list according to heading level)
        $toctext = "";
        foreach ($toc as $entry) {
            $toctext .= str_repeat("#", $entry['level']);
            $toctext .= " [[" . $entry['link'] . '|' . $entry['title'] . "]]\n";
        }
        $this->logger->debug("Created new TOC.", array('toctext' => $toctext));
        
        // Edit page - replace TOC div contents with contents of new list, if new toc is different
        if (!$tocData->tocHasChanged($toctext)) {
            $this->logger->info("TOC for page '$title' has not changed.");
            return;
        }
        $this->conn->editPage($title, $tocData->getNewTocPage($toctext), 'Updated book table of contents');
    }
    
    protected function getTocFromTitles(TocText $tocData)
    {
        $titles = $tocData->getTitlesAndLevels();
        $toc = array();
        foreach ($titles as $pagedata) {
            $this->logger->info("LABElTitelLevel", $pagedata);
            /* don't go through tocs from sections here again*/
            if (strpos($pagedata['full_title'], "#") !== false) {
                continue;
            }
            $toc[] = array(
                'title' => $pagedata['label'],
                'link'  => $pagedata['title'],
                'level' => $pagedata['level']
            );
            
            foreach ($this->conn->getSectionsForTitle($pagedata['title']) as $section) {
                $toc_array = array(
                    'title' => $section['line'],
                    'link'  => $pagedata['title'].'#'.$section['anchor'],
                    'level' => $section['level']  //  +1 // after updated headline cmd this looks better(again)
                );
                $toc[] = $toc_array;
            }
            
        }
        $this->logger->debug(sprintf("Generated %d TOC entries for %d titles.", count($toc), count($titles)));
        return $toc;
    }
    
    protected function editPage($title, $content, $summary)
    {
        $result = $this->conn->editPage($title, $content, $summary);
        if ($result == 'Success') {
            $this->logger->info("Page '$title' was updated.");
        } else {
            $this->logger->warn("Update for page '$title' failed.");
        }
    }
}
