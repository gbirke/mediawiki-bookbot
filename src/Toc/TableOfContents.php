<?php

/**
 * This file contains the class TableOfContents
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot\Toc;

/**
 * The table of contents holds several TocItems
 *
 * @author birkeg
 */
class TableOfContents
{
    /**
     * Start marker
     * @var string
     */
    public $startMarker = '<div class="BookTOC">';
    
    /**
     * End marker
     * @var string
     */
    public $endMarker = '</div>';
    
    private $items;
    
    public function __construct($items = array())
    {
        $this->items = $items;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function addItem(TocItem $item)
    {
        $this->items[] = $item;
    }
}
