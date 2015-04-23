<?php

/**
 * This file contains the class HeadlineCleaner
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot;

use Monolog\Logger;


/**
 * Description of HeadlineCleaner
 *
 * @author birkeg
 */
class HeadlineCleaner {
    
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
    
    function __construct(ApiConnector $conn, Logger $logger) {
        $this->conn = $conn;
        $this->logger = $logger;
    }

    /**
     * Cleanup headlines of TOC
     * 
     * @param string $title Name of page containing the TOC
     * @return type
     */
    public function cleanupSubpages($title) {
        // Download page text
        $text = $this->conn->downloadPageText($title);
        $tocData = new TocText($text);
        if (!$tocData->tocExists()) {
            $this->logger->notice("Page '$title' has no TOC.");
            return;
        }
        
        $titles = $tocData->getTitlesAndLevels();
        foreach ($titles as $pagedata) {

        	/* don't go through tocs from sections here again*/
        	if (strpos($pagedata['full_title'], "#") !== false){
        		continue;
        	}
        	$this->updateSubpage($pagedata['title'], $pagedata['level']);
        }
    }
    
    /**
     * 
     * @param string $title Title of the subpage
     * @param int $toclevel Level in TOC ("Parent" level)
     */
    public function updateSubpage($title, $toclevel) {
        $textChanged = false;
        $sections = $this->conn->getSectionsForTitle($title);
        $ptext = $this->conn->downloadPageText($title);
        //$this->logger->debug("SECTIONS: ".var_export($sections, true));
        $levelCalculator = new TocLevelCalc();
        foreach($sections as $section) {

            $newLevel = $levelCalculator->getNewTocLevel($section['level'], $toclevel);
            if ($newLevel != $section['level']) {
                $textChanged = true;
                $newHeadline = $this->getHeadlineString($section['line'], $newLevel);
                $rx = "/".  str_repeat("=",$section['level'])."\\s*".preg_quote($section['line'], '/')."\\s*=+/";
                $this->logger->debug("Replacing $rx with $newHeadline\n", array("newlevel" => $newLevel, "oldlevel" => $section['level']));
                $ptext = preg_replace($rx, $newHeadline, $ptext, 1);
            }
            else {
                $this->logger->debug("Keeping {$section['line']} at level {$section['level']}");
            }
        }
        if ($textChanged) {
            $this->logger->info("now i'd be updating'");
            $this->editPage($title, $ptext, "Updated headline levels according to table of contents");
        }
    }
    
    protected function getHeadlineString($text, $level) {
        $hltag = str_repeat("=", $level);
        return sprintf("%s %s %s", $hltag, $text, $hltag);
    }
    
    protected function editPage($title, $content, $summary) {
        $result = $this->conn->editPage($title, $content, $summary);
        if ($result == 'Success') {
            $this->logger->info("Page '$title' was updated.");
        }
        else {
            $this->logger->warn("Update for page '$title' failed.");
        }
    }
}
