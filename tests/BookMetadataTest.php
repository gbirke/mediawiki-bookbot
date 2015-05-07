<?php

/**
 * This file contains the class BookMetadataTest
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot;

/**
 * Description of BookMetadataTest
 *
 * @author birkeg
 */
class BookMetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testTocTitleIsReturnedAsBookTitle()
    {
        $meta = new BookMetadata("1984", "_Print_Version");
        $this->assertEquals("1984", $meta->getBookTitle());
    }
    
    public function testBookTitleIsReturnedCorrectlyIfTocPageIsSubpage()
    {
        $meta = new BookMetadata("1984/_Table_of_Contents", "_Print_Version");
        $this->assertEquals("1984", $meta->getBookTitle());
    }
    
    public function testPrintVersionTitleConsistsOfBookTitleAndPrintVersionName()
    {
        $meta = new BookMetadata("1984", "_Print_Version");
        $this->assertEquals("1984/_Print_Version", $meta->getPrintPageTitle());
        $meta = new BookMetadata("1984/_Table_of_Contents", "_Print_Version");
        $this->assertEquals("1984/_Print_Version", $meta->getPrintPageTitle());
    }
    
    public function testPrintPageNameIsNotChangedIfItIsASubpage()
    {
        $meta = new BookMetadata("1984", "Full_Texts/1984");
        $this->assertEquals("Full_Texts/1984", $meta->getPrintPageTitle());
    }
}
