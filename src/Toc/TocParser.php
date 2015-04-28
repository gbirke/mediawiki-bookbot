<?php

/**
 * This file contains the class TocParser
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot\Toc;

/**
 * Create a TableOfContents object from wiki text
 *
 * @author birkeg
 */
class TocParser
{
    /**
     * Regular expression to find the start marker of the Table of contents
     * @var string
     */
    public $startMarkerRx = '/<div[^>]+class="([^"]*)bookTOC([^"]*)"[^>]*>/i';
    
    /**
     * Regular expression to find the end marker of the Table of contents
     * @var string
     */
    public $endMarkerRx = '/<\/div>/i';
    
    /**
     *
     * @var TocItemParser
     */
    private $itemParser;
    
    public function __construct(TocItemParser $itemParser)
    {
        $this->itemParser = $itemParser;
    }
    
    /**
     * Create a table of contents object with items from text
     * @param string $text
     * @return \Birke\Mediawiki\Bookbot\Toc\TableOfContents
     */
    public function parse($text)
    {
        list($startMarkerText, $startMarkerOffset) = $this->getMarkerAndOffset($text, $this->startMarkerRx);
        list($endMarkerText, $endMarkerOffset) = $this->getMarkerAndOffset($text, $this->endMarkerRx, $startMarkerOffset);
        if (!$startMarkerText || !$endMarkerText) {
            return null;
        }
        $marker = new TocMarker($startMarkerText, $startMarkerOffset, $endMarkerText, $endMarkerOffset);
        $tocText = substr($text, $marker->getTocStart(), $marker->getTocLength());
        $tocLines = explode("\n", $tocText);
        $toc = new TableOfContents();
        foreach ($tocLines as $tocLine) {
            $tocItem = $this->itemParser->parse(trim($tocLine));
            if (!is_null($tocItem)) {
                $toc->addItem($tocItem);
            }
        }
        return $toc;
    }
    
    protected function getMarkerAndOffset($text, $pattern, $offset = 0)
    {
        if (!preg_match($pattern, $text, $matches, PREG_OFFSET_CAPTURE, $offset)) {
            return array("", 0);
        } else {
            return $matches[0];
        }
    }
}
