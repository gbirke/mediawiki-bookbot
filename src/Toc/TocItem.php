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
     * If the generated wikitext should contain a new list level (separately numbered
     * @var boolean
     */
    private $indentWithNumbering;
    
    public function __construct($title, $label, $level, $indentWithNumbering = true)
    {
        if ($level < 1) {
            throw new TocException("Toc Item level must be at least 1");
        }
        $this->title = $title;
        $this->label = $label;
        $this->level = $level;
        $this->indentWithNumbering = $indentWithNumbering;
    }
    
    public function getTitle()
    {
        return $this->title;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getLevel()
    {
        return $this->level;
    }
    
    public function __toString()
    {
        if ($this->indentWithNumbering) {
            $indent = str_repeat("#", $this->level);
        } else {
            $indent = "#" . str_repeat(":", $this->level-1);
        }
        return sprintf("%s [[%s|%s]]", $indent, $this->title, $this->label);
    }
}
