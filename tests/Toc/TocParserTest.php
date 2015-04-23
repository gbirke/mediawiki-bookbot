<?php

/**
 * This file contains the class TocParserTest
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot\Toc;

/**
 * Description of TocParserTest
 *
 * @author birkeg
 */
class TocParserTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->itemParser = $this->getMockBuilder('Birke\Mediawiki\Bookbot\Toc\TocItemParser')
            ->getMock();
        $this->parser = new TocParser($this->itemParser);
    }
    
    public function testNoTableOfContentsIsCreatedOnEmptyText()
    {
        $this->assertNull($this->parser->parse(""));
    }
    
    public function testNoTableOfContentsIsCreatedIfEndMarkerIsMissing()
    {
        $this->assertNull($this->parser->parse('<div class="bookTOC">  '));
    }
    
    public function testTableOfContentsIsCreatedWhenMarkerIsFound()
    {
        $this->assertInstanceOf(
            'Birke\Mediawiki\Bookbot\Toc\TableOfContents',
            $this->parser->parse('<div class="bookTOC"> </div>')
        );
    }
    public function testTocItemParserIsCalledWithContentsOfToc()
    {
        $this->itemParser->expects($this->once())
                ->method('parse')
                ->with("abcd");
        $this->parser->parse("<div class=\"bookTOC\">abcd</div>");
    }
    
    public function testTocItemParserIsCalledWithMultipleLinesOfToc()
    {
        $this->itemParser->expects($this->exactly(3))
                ->method('parse')
                ->withConsecutive(array("Foo"), array("Bar x"), array(""));
        $this->parser->parse("<div class=\"bookTOC\">Foo \nBar x\n    </div>");
    }
}
