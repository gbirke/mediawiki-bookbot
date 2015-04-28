<?php

/**
 * This file contains the class TocMarkerTest
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot\Toc;

/**
 * Description of TocMarkerTest
 *
 * @author birkeg
 */
class TocMarkerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->marker = new TocMarker("<div>", 100, "</div>", 200);
    }
    
    /**
     * @expectedException Birke\Mediawiki\Bookbot\Toc\TocException
     */
    public function testEmptStartMarkerThrowsTocException()
    {
        new TocMarker("", 100, "</div>", 200);
    }
    
    /**
     * @expectedException Birke\Mediawiki\Bookbot\Toc\TocException
     */
    public function testEmpytEndMarkerThrowsTocException()
    {
        new TocMarker("<div>", 100, "", 200);
    }
    
    /**
     * @expectedException Birke\Mediawiki\Bookbot\Toc\TocException
     */
    public function testThrowTocExceptionWhenEndOffsetIsBiggerThanStartOffset()
    {
        new TocMarker("<div>", 200, "</div>", 100);
    }

    public function testConstructorAssignsTextValues()
    {
        $this->assertEquals($this->marker->getStartMarker(), "<div>");
        $this->assertEquals($this->marker->getEndMarker(), "</div>");
    }
    
    public function testStartMarkerLengthIsAddedToOffsetOfTocText()
    {
        $this->assertEquals($this->marker->getTocStart(), 105);
    }
    
    public function testEndMarkerOffsetIsEndOfToc()
    {
        $this->assertEquals($this->marker->getTocEnd(), 200);
    }
    
    public function testEndMarkerLengthIsAddedToOffsetOfTocMarkup()
    {
        $this->assertEquals($this->marker->getTocMarkupEnd(), 206);
    }
    
    public function testStartMarkerOffsetIsEndOfTocMarkup()
    {
        $this->assertEquals($this->marker->getTocMarkupStart(), 100);
    }
    
    public function testTocLengthIsCalculatedFromStartOffsetAndStartMarkerLengthAndEndMarkerOffset()
    {
        $this->assertEquals($this->marker->getTocLength(), 95);
    }
}
