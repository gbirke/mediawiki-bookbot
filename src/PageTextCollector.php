<?php

/**
 * This file contains the class PageCollector
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot;

/**
 * This class downloads the page text of unique pages in a table of contents
 *
 * @author birkeg
 */
class PageTextCollector
{
    /**
     *
     * @var ApiConnector
     */
    protected $conn;
    
    public function __construct(ApiConnector $conn)
    {
        $this->conn = $conn;
    }

    /**
     * 
     * @param \Birke\Mediawiki\Bookbot\Toc\TableOfContents $toc
     * @return array
     */
    public function getPages(Toc\TableOfContents $toc)
    {
        $pages = array();
        foreach ($toc->getItems() as $item) {
            $pageId = $item->getTitleWithoutAnchor();
            if (isset($pages[$pageId])) {
                continue;
            }
            $pages[$pageId] = $this->conn->downloadPageText($pageId);
        }
        return $pages;
    }
}
