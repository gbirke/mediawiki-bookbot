<?php

/**
 * This file contains the class PageTitleGeneratorTest
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot;

/**
 * Description of PageTitleGeneratorTest
 *
 * @author birkeg
 */
class PageTitleGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testPageTitleAddsCorrectHeadingLevel()
    {
        $generator = new PageTitleGenerator();
        $item = new Toc\TocItem("Foo", "Link to foo", 1);
        $this->assertEquals("= Foo =", $generator->generatePageTitle($item));
        $item = new Toc\TocItem("Foo", "Link to foo", 2);
        $this->assertEquals("== Foo ==", $generator->generatePageTitle($item));
        $item = new Toc\TocItem("Bar", "Link to bar", 1, 1);
        $this->assertEquals("== Bar ==", $generator->generatePageTitle($item));
    }
    
    public function testPageTitleRemovesBookTitle()
    {
        $generator = new PageTitleGenerator();
        $item = new Toc\TocItem("Book/Foo", "Link to foo", 1);
        $this->assertEquals("= Foo =", $generator->generatePageTitle($item, "Book"));
    }
    
    public function testHeadingLevelCannotExceedSix()
    {
        $generator = new PageTitleGenerator();
        $item = new Toc\TocItem("Foo", "Link to foo", 3, 4);
        $this->assertEquals("====== Foo ======", $generator->generatePageTitle($item));
    }
}
