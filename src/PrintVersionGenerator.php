<?php

/**
 * This file contains the class PrintVersionGenerator
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot;

/**
 * Description of PrintVersionGenerator
 *
 * @author birkeg
 */
class PrintVersionGenerator
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
    
    public function generatePrintpageForTitle($title, $preamble = "none", $printpageName = "_Druckversion")
    {
        $printTitle = $title.'/'.$printpageName;
                
        // Download page text
        $text = $this->conn->downloadPageText($title);
        $tocParser = new Toc\TocParser(new Toc\TocItemParser());
        if (!$tocParser->tocExists($text)) {
            $this->logger->notice("Page '$title' has no TOC.");
            return;
        }
        $toc = $tocParser->parse($text);
        
        $printText = $this->generateTextPrefix($title, $preamble);
        $printText .= $this->generatePageText($toc, $title);
        
        $this->logger->info("Created new print version.", array('printtext' => $printText));
        // Edit page - replace TOC div contents with contents of new list, if new toc is different
        if ($this->pageHasChanged($printTitle, $printText)) {
            $this->conn->editPage($printTitle, $printText, 'Updated print version after TOC change');
        }
    }
    
    protected function generateTextPrefix($title, $preamble)
    {
        $printText = "= $title =";
        if ($preamble !== "none") {
            $printText .= "<div class=\"preamble print-only\">$preamble</div>\n";
        }
        $printText .= "__TOC__\n";
        return $printText;
    }
    
    protected function generatePageText(Toc\TableOfContents $toc, $bookTitle)
    {
        $titleGenerator = new PageTitleGenerator();
        $textCollector = new PageTextCollector($conn);
        $pageTexts = $textCollector->getPages($toc);
        foreach ($pageTexts as $pageId => $text) {
            $printText .= sprintf("\n%s\n", $titleGenerator->generatePageTitle($toc->getItemById($pageId, $bookTitle)));
            $printText .= $text;
        }
        
        return $this->putReferencesAtTheBotton($printText);
    }
    
    protected function putReferencesAtTheBotton($printText)
    {
        $rx = "(=+\s*Einzelnachweise?\s*=+|<references\s*/>";
        $cleanPrintText = preg_replace($rx, '', $printText);
        $cleanPrintText .= "\n== Einzelnachweise ==\n" ;
        $cleanPrintText .='<references />';
        return $cleanPrintText;
    }
    
    protected function pageHasChanged($printTitle, $printText)
    {
        $oldPrintText = $this->conn->downloadPageText($printTitle);
        $hasChanged = $oldPrintText === $printText;
        if ($hasChanged) {
            $this->logger->info("Print version for page '$title' has not changes.");
        }
        return $hasChanged;
    }
}
