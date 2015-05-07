<?php

/**
 * This file contains the class PageTitleConverter
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot;

/**
 * Convert a TocItem page title into a heading for use in a generated print page
 *
 * @author birkeg
 */
class PageTitleGenerator
{
    public function generatePageTitle(Toc\TocItem $item, $bookTitle = "")
    {
        $pageName = $item->getTitleWithoutAnchor();
        $cleanPageName = $this->removeBookTitleFromPageName($pageName, $bookTitle);
        $headingMarkup = substr(str_repeat("=", $item->getAbsoluteLevel()), 0, 6); // No more than 6 levels deep
        return sprintf("%s %s %s", $headingMarkup, $cleanPageName, $headingMarkup);
    }
    
    protected function removeBookTitleFromPageName($pageName, $bookTitle)
    {
        $quotedBookTitle = preg_quote($bookTitle, "/");
        $bookTitlePath = preg_replace("/[_ ]/", "[_ ]", $quotedBookTitle)."\\/";
        return preg_replace("/^".$bookTitlePath."/", "", $pageName);
        
    }
}
