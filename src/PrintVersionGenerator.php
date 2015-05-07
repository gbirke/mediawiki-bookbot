<?php

/**
 * This file contains the class PrintVersionGenerator
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot;

use Monolog\Logger;

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
    
    public function generatePrintpageForTitle(BookMetadata $metadata, $preamble = "none")
    {
        $printTitle = $metadata->getPrintPageTitle();
        $title = $metadata->getBookTitle();
                
        // Download page text
        $text = $this->conn->downloadPageText($metadata->getTocPageTitle());
        $tocParser = new Toc\TocParser(new Toc\TocItemParser());
        if (!$tocParser->tocExists($text)) {
            $this->logger->notice("Page '$title' has no TOC.");
            return;
        }
        $toc = $tocParser->parse($text);
        
        $printText = $this->generateTextPrefix($title, $preamble);
        $printText .= $this->generatePageText($toc, $title);
        
        //$this->logger->info("Created new print version.", array('printtext' => $printText));
        // Edit page - replace TOC div contents with contents of new list, if new toc is different
        if ($this->pageHasChanged($printTitle, $printText)) {
            $this->conn->editPage($printTitle, $printText, 'Updated print version after TOC change');
            $this->logger->info("Updated print page");
        }
    }
    
    protected function generateTextPrefix($title, $preamble)
    {
        $printText = "= $title =\n";
        if ($preamble !== "none") {
            $printText .= "\n<div class=\"preamble print-only\">$preamble</div>\n";
        }
        $printText .= "\n__TOC__\n\n";
        return $printText;
    }
    
    protected function generatePageText(Toc\TableOfContents $toc, $bookTitle)
    {
        $printText = "";
        $titleGenerator = new PageTitleGenerator();
        $textCollector = new PageTextCollector($this->conn);
        $pageTexts = $textCollector->getPages($toc);
        foreach ($pageTexts as $pageId => $text) {
            $chapterTitle = $titleGenerator->generatePageTitle($toc->getItemById($pageId, $bookTitle));
            $printText .= sprintf("\n\n%s\n\n", $chapterTitle);
            $printText .= $text;
        }
        
        return $this->removeCategories(
            $this->putReferencesAtTheBotton($printText)
        );
    }
    
    protected function putReferencesAtTheBotton($printText)
    {
        $rx = "/(=+\s*Einzelnachweise?\s*=+|<references\s*\/>)/";
        $cleanPrintText = preg_replace($rx, '', $printText);
        $cleanPrintText .= "\n== Einzelnachweise ==\n" ;
        $cleanPrintText .='<references />';
        return $cleanPrintText;
    }
    
    protected function removeCategories($printText)
    {
        $rx = "/\[\[(Kategorie|Category):[^\]]+\]\]/";
        return preg_replace($rx, '', $printText);
    }
    
    protected function pageHasChanged($printTitle, $printText)
    {
        $oldPrintText = $this->conn->downloadPageText($printTitle);
        $isTheSame = $oldPrintText === $printText;
        if ($isTheSame) {
            $this->logger->info("Print version for page '$printTitle' has not changed.");
        }
        return !$isTheSame;
    }
}
