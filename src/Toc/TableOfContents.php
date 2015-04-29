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
    private $items;
    
    public function __construct($items = array())
    {
        $this->items = array_reduce($items, function ($collection, $item) {
            $collection[$item->getTitle()] = $item;
            return $collection;
        }, array());
    }

    public function getItems()
    {
        return array_values($this->items);
    }

    public function addItem(TocItem $item)
    {
        $this->items[$item->getTitle()] = $item;
    }
    
    public function getItemById($itemId)
    {
        if (isset($this->items[$itemId])) {
            return $this->items[$itemId];
        } else {
            return null;
        }
    }
}
