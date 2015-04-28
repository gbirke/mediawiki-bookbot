<?php

/**
 * This file contains the class TocItemParserTest
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot\Toc;

/**
 * Description of TocItemParserTest
 *
 * @author birkeg
 */
class TocItemParserTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->parser = new TocItemParser();
    }
    
    public function testParserReturnsNullWhenItemCannotBeParsed()
    {
        $this->assertNull($this->parser->parse(""));
        $this->assertNull($this->parser->parse("foo"));
    }
    
    public function testParserRecognizes1stLevelItems()
    {
        $expectedItem = new TocItem("Foo_Page", "Foo", 1);
        $resultItem = $this->parser->parse("# [[Foo_Page|Foo]]");
        $this->assertEquals($expectedItem, $resultItem);
    }
    
    public function testParserRecognizes2ndLevelItems()
    {
        $expectedItem = new TocItem("Foo_Page", "Foo", 2);
        $resultItem = $this->parser->parse("## [[Foo_Page|Foo]]");
        $this->assertEquals($expectedItem, $resultItem);
    }
    
    public function testParserRecognizes1stLevelIndentedItems()
    {
        $expectedItem = new TocItem("Foo_Page", "Foo", 1, 1);
        $resultItem = $this->parser->parse("#: [[Foo_Page|Foo]]");
        $this->assertEquals($expectedItem, $resultItem);
    }
    
    public function testParserRecognizes2stLevelIndentedItems()
    {
        $expectedItem = new TocItem("Foo_Page", "Foo", 2, 2);
        $resultItem = $this->parser->parse("##:: [[Foo_Page|Foo]]");
        $this->assertEquals($expectedItem, $resultItem);
    }
}
