<?php

/**
 * This file contains the class TocText
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot;

/**
 * Description of TocText
 *
 * @author birkeg
 */
class TocText {
    
    /**
     * CSS class that signifies a TOC element
     */
    const CLASS_NAME = "BookTOC";
    
    /**
     * HTML tag that signifies a TOC element
     */
    const TAG_NAME = "div";
    
    /**
     * Page text
     * @var string
     */
    protected $text;
    /**
     * Start of TOC in the text
     * @var int
     */
    protected $tocStart;
    
    /**
     * End of TOC in the text
     * @var int
     */
    protected $tocEnd;
    
    /**
     * String that contains the TOC div
     * @var string
     */
    protected $startMarker;
    
    protected $endMarker;
    
    /**
     * 
     * @param string $text Text of a page containing the TOC
     */
    public function __construct($text) {
        $this->text = $text;
        $this->endMarker = '</' . self::TAG_NAME . '>';
        $rx = '/<' . self::TAG_NAME . '[^>]+class="([^"]*)'. self::CLASS_NAME .'([^"]*)"[^>]*>/i';
        if (!preg_match($rx, $text, $matches, PREG_OFFSET_CAPTURE)) {
            return;
        }
        
        $this->tocStart = $matches[0][1];
        $this->startMarker = $matches[0][0];
        
        $endRx = '/' . preg_quote($this->endMarker, '/') .  '/i';
        if (!preg_match($endRx, $text, $matches, PREG_OFFSET_CAPTURE, $this->tocStart)) {
            $this->tocEnd = strlen($text);
        }
        else {
            $this->tocEnd = $matches[0][1] + strlen($this->endMarker);
        }
        
    }
    
    /**
     * Collect unique page links (unique = page names without fragment identifier) and their level
     * 
     * Only pages starting with '#' and containing links are counted.
     * 
     * @return array Page name => array
     */
    public function getTitlesAndLevels() {
        $titles = array();
        $lines = explode("\n", $this->getTocText());
        foreach ($lines as $l) {
            if (!preg_match('/^(#+)\s*\[\[([^\]]+)\]\]/', $l, $matches)) {
                continue;
            }
            $linkparts = explode("|", $matches[2]);
            $title = array_shift($linkparts);
            $label = implode("|", $linkparts);
            $page = preg_replace('/#.*$/', '', $title);
            $key = $page."|".$label;
            if (isset($titles[$key])) {
                continue;
            }
            $titles[$key] = array(
                'title' => $page,
                'label' => $label,
                'level' => strlen($matches[1]),
            	'full_title' => $title // need to know if it is an anchor
            );
            
        }
        return $titles;
    }
    
    
    /**
     * Check if the TOC text has changed
     * @param string $newToc New Toc Text
     * @return boolean
     */
    public function tocHasChanged($newToc) {
        $oldToc = preg_replace('/\s+/', ' ', $this->getTocText());
        $newToc = trim(preg_replace('/\s+/', ' ', $newToc));
        return $oldToc != $newToc;
    }
    
    public function getNewTocPage($newToc) {
        $newText = substr($this->text, 0, $this->getTocStart());
        $newText .= $this->getStartMarker() . "\n";
        $newText .= $newToc;
        $newText .= $this->getEndMarker();
        $newText .= substr($this->text, $this->getTocEnd());
        return $newText;
    }
    
    /**
     * Check if page has a TOC
     * @return boolean
     */
    public function tocExists() {
        return !empty($this->startMarker);
    }
    
    /**
     * Return TOC lines without start and end marker
     * @return string
     */
    public function getTocText() {
        $offset = $this->tocStart + strlen($this->startMarker);
        return trim(substr($this->text, $offset, $this->tocEnd - $offset - strlen($this->endMarker)));
    }
    
    public function getTocStart() {
        return $this->tocStart;
    }

    public function getTocEnd() {
        return $this->tocEnd;
    }

    public function getStartMarker() {
        return $this->startMarker;
    }
    public function getEndMarker() {
        return $this->endMarker;
    }
}
