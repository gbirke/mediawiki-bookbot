<?php

/**
 * This file contains the class TocItemParser
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot\Toc;

/**
 * Parse a line of text into a TocItem if the pattern macthes
 *
 * @author birkeg
 */
class TocItemParser
{
    protected $itemRx = '/^(#+)(:*)\s*\[\[([^\]]+)\]\]/';
    
    /**
     * 
     * @param string $line
     * @return TocItem
     */
    public function parse($line)
    {
        if (!preg_match($this->itemRx, $line, $matches)) {
            return null;
        }
        $linkparts = explode("|", $matches[3]);
        $title = array_shift($linkparts);
        $label = implode("|", $linkparts);
        return new TocItem($title, $label, strlen($matches[1]), strlen($matches[2]));
    }
}
