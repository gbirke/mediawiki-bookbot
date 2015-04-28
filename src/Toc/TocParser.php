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
    
    public function tocExists($text)
    {
        list($startMarkerText, $startMarkerOffset) = $this->getMarkerAndOffset($text, $this->startMarkerRx);
        list($endMarkerText, $endMarkerOffset) = $this->getMarkerAndOffset($text, $this->endMarkerRx, $startMarkerOffset);
        return $startMarkerText && $endMarkerText;
    }
    
    /**
     * Create a table of contents object with items from text
     * @param string $text
     * @return \Birke\Mediawiki\Bookbot\Toc\TableOfContents
     */
    public function parse($text)
    {
        $toc = new TableOfContents();
        try {
            $tocLines = $this->getTocLines($text);
        } catch (TocException $ex) {
            return null;
        }
        foreach ($tocLines as $line) {
            $this->addTocItem($toc, $line);
        }
        return $toc;
    }
     
    /**
     * Get text between TOC markers
     * 
     * @param string $text
     * @return string
     */
    protected function getTocText($text)
    {
        list($startMarkerText, $startMarkerOffset) = $this->getMarkerAndOffset($text, $this->startMarkerRx);
        list($endMarkerText, $endMarkerOffset) = $this->getMarkerAndOffset($text, $this->endMarkerRx, $startMarkerOffset);
        $marker = new TocMarker($startMarkerText, $startMarkerOffset, $endMarkerText, $endMarkerOffset);
        return substr($text, $marker->getTocStart(), $marker->getTocLength());
    }
     
    /**
     * Get lines between TOC markers as array
     * 
     * @param string $text
     * @return array
     */
    protected function getTocLines($text)
    {
        return explode("\n", $this->getTocText($text));
    }
   
    /**
     * If the ItemParser detects a TocItem in the line, add it to the toc object.
     * @param TableOfContents $toc
     * @param string $line
     */
    protected function addTocItem($toc, $line)
    {
        $tocItem = $this->itemParser->parse(trim($line));
        if (!is_null($tocItem)) {
            $toc->addItem($tocItem);
        }
    }
    
    /**
     * Get an array of start/end marker and its offset
     * @param string $text Text to search
     * @param string $pattern Marker search pattern
     * @param int $offset Where to start searching
     * @return array
     */
    protected function getMarkerAndOffset($text, $pattern, $offset = 0)
    {
        if (!preg_match($pattern, $text, $matches, PREG_OFFSET_CAPTURE, $offset)) {
            return array("", 0);
        } else {
            return $matches[0];
        }
    }
}
