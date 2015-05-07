<?php

/**
 * This file contains the class BookMetadata
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot;

/**
 * Contains settings for generating a print page
 *
 * @author birkeg
 */
class BookMetadata
{
    /**
     *
     * @var string
     */
    protected $tocPageTitle;
    
    /**
     *
     * @var string
     */
    protected $printPageName;
    
    public function __construct($tocPageTitle, $printPageName)
    {
        $this->tocPageTitle = $tocPageTitle;
        $this->printPageName = $printPageName;
    }
    
    public function getBookTitle()
    {
        $subpageOffset = strpos($this->tocPageTitle, "/");
        if ($subpageOffset) {
            return substr($this->tocPageTitle, 0, $subpageOffset);
        }
        return $this->tocPageTitle;
    }
    
    public function getPrintPageTitle()
    {
        if (strpos($this->printPageName, "/")) {
            return $this->printPageName;
        }
        return $this->getBookTitle() . "/" . $this->printPageName;
    }
    
    public function getTocPageTitle()
    {
        return $this->tocPageTitle;
    }
}
