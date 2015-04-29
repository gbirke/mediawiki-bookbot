<?php

/**
 * This file contains the class TocItemTest
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot\Toc;

class TocItemTest extends \PHPUnit_Framework_TestCase
{
    public function testToStringGeneratesWikiText()
    {
        $item = new TocItem("Foo_Page", "Foo", 1);
        $this->assertEquals("# [[Foo_Page|Foo]]", (string) $item);
    }

    public function testToStringIndentsCorrectlyForLevel2()
    {
        $item = new TocItem("Foo_Page", "Foo", 2);
        $this->assertEquals("## [[Foo_Page|Foo]]", (string) $item);
    }

    public function testToStringIndentsCorrectlyForLevel3()
    {
        $item = new TocItem("Foo_Page", "Foo", 3);
        $this->assertEquals("### [[Foo_Page|Foo]]", (string) $item);
    }

    /**
     * @expectedException Birke\Mediawiki\Bookbot\Toc\TocException
     */
    public function testZeroLevelGeneratesException()
    {
        new TocItem("Foo_Page", "Foo", 0);
    }

    public function testIndentOneLevelAddsCorrectPrefix()
    {
        $item = new TocItem("Foo_Page", "Foo", 1, 1);
        $this->assertEquals("#: [[Foo_Page|Foo]]", (string) $item);
    }

    public function testIndentTwoLevelsAddsCorrectPrefix()
    {
        $item = new TocItem("Foo_Page", "Foo", 1, 2);
        $this->assertEquals("#:: [[Foo_Page|Foo]]", (string) $item);
    }

    public function testGetTitleWithoutAnchorRemovesAnchorPartOfTitle()
    {
         $item = new TocItem("Foo_Page#anchor", "Foo", 1, 2);
         $this->assertEquals("Foo_Page", $item->getTitleWithoutAnchor());
    }

    public function testAbsoluteLevelAddsLevelAndIndent()
    {
        $item = new TocItem("Foo_Page#anchor", "Foo", 1, 2);
        $this->assertEquals(3, $item->getAbsoluteLevel());
    }
}
