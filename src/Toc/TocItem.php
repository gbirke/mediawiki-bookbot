<?php

/**
 * This file contains the class TocItem
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot\Toc;

/**
 * A TocItem represents a single entry (link) in a table of contents.
 *
 * A TocItem can have a level which decides the numbering level in the hierarchy of entries.
 * A TocItem can also have an indentation which is a visual indent without changing the numbering level.
 *
 * @author birkeg
 */
class TocItem
{
    /**
     * Wiki page title
     * 
     * @var string
     */
    private $title;
    
    /**
     * Link label
     * @var string
     */
    private $label;
    
    /**
     * Indentation level in table of contents
     * 
     * @var int
     */
    private $level;
    
    /**
     * Indent item, additionally to level
     * @var boolean
     */
    private $indent;
    
    public function __construct($title, $label, $level, $indent = 0)
    {
        if ($level < 1) {
            throw new TocException("Toc Item level must be at least 1");
        }
        $this->title = $title;
        $this->label = $label;
        $this->level = $level;
        $this->indent = $indent;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function getTitleWithoutAnchor()
    {
        return preg_replace("/#.*$/", "", $this->title);
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getLevel()
    {
        return $this->level;
    }
    
    public function getIndent()
    {
        return $this->indent;
    }

    public function getAbsoluteLevel()
    {
        return $this->level + $this->indent;
    }

    public function __toString()
    {
        $prefix = str_repeat("#", $this->level) . str_repeat(":", ($this->indent));
        return sprintf("%s [[%s|%s]]", $prefix, $this->title, $this->label);
    }
}
