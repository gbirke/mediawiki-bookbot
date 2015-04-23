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
    
    public function parse($text)
    {
        if (!preg_match($this->startMarkerRx, $text, $matches, PREG_OFFSET_CAPTURE)) {
            return null;
        }
        $startMarker = $matches[0][0];
        $startMarkerOffset = $matches[0][1];
        $startMarkerLength = strlen($startMarker);
        if (!preg_match($this->endMarkerRx, $text, $matches, PREG_OFFSET_CAPTURE)) {
            return null;
        }
        $endMarkerOffset = $matches[0][1];
        $tocLength = $endMarkerOffset - $startMarkerOffset - $startMarkerLength;
        $toc = substr($text, $startMarkerOffset + $startMarkerLength, $tocLength);
        $tocLines = explode("\n", $toc);
        foreach ($tocLines as $tocLine) {
            $this->itemParser->parse(trim($tocLine));
        }
        return new TableOfContents();
    }
}
