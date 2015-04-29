<?php

/**
 * This file contains the class PageCollectorTest
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot;

/**
 * Description of PageCollectorTest
 *
 * @author birkeg
 */
class PageTextCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->apiConnection = $this->getMockBuilder('Birke\Mediawiki\Bookbot\ApiConnector')
            ->disableOriginalConstructor()
            ->getMock();
        $this->apiConnection->method("downloadPageText")
                ->will($this->returnValueMap(array(
                    array("Foo", true, "Foo Text"),
                    array("Bar", true,"Bar Text"),
                    array("Baz", true, "Baz Text")
                )));
        $this->pageCollector = new PageTextCollector($this->apiConnection);
    }
    
    public function testGetPagesReturnsArrayOfPages()
    {
        $testToc = new Toc\TableOfContents(array(
            new Toc\TocItem("Foo", "Foo", 1),
            new Toc\TocItem("Bar", "Bar", 1),
            new Toc\TocItem("Baz", "Baz", 1)
        ));
        $expectedPageArray = array(
            "Foo" => "Foo Text",
            "Bar" => "Bar Text",
            "Baz" => "Baz Text"
        );
        $this->assertEquals($expectedPageArray, $this->pageCollector->getPages($testToc));
    }
    
    public function testAnchorsInTitlesAreRemoved()
    {
        $testToc = new Toc\TableOfContents(array(
            new Toc\TocItem("Foo", "Foo", 1),
            new Toc\TocItem("Bar#anchor", "Bar", 1),
            new Toc\TocItem("Baz", "Baz", 1)
        ));
        $expectedPageArray = array(
            "Foo" => "Foo Text",
            "Bar" => "Bar Text",
            "Baz" => "Baz Text"
        );
        $this->assertEquals($expectedPageArray, $this->pageCollector->getPages($testToc));
    }
}
