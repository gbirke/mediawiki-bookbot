<?php

/**
 * This file contains the class TableOfContentsTest
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot\Toc;


class TableOfContentsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetItemReturnsItemsPassedInTheConstructor()
    {
        $items = array(
            new TocItem("Foo", "Foo", 1),
            new TocItem("Bar", "Bar", 1),
            new TocItem("Baz", "Baz", 1)
        );
        $toc = new TableOfContents($items);
        $this->assertEquals($items, $toc->getItems());
    }
    
    public function testGetItemByIdReturnsItem()
    {
        $items = array(
            new TocItem("Foo", "Foo", 1),
            new TocItem("Bar", "Bar", 1),
            new TocItem("Baz", "Baz", 1)
        );
        $toc = new TableOfContents($items);
        $expectedItem = new TocItem("Baz", "Baz", 1);
        $this->assertEquals($expectedItem, $toc->getItemById("Baz"));
    }
    
    public function testGetItemByIdReturnsNullIfIdIsWrong()
    {
        $items = array(
            new TocItem("Foo", "Foo", 1)
        );
        $toc = new TableOfContents($items);
        $this->assertNull($toc->getItemById("Baz"));
    }
}
