<?php

/**
 * This file contains the class TocMarker
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Bookbot\Toc;

/**
 * The Marker stores the contents and offsets of the Tags delimiting the table of contents
 *
 * @author birkeg
 */
class TocMarker
{
    /**
     *
     * @var string
     */
    protected $startMarkerText;
    
    /**
     *
     * @var int
     */
    protected $startMarkerOffset;
    
    /**
     *
     * @var string
     */
    protected $endMarkerText;
    
    /**
     *
     * @var int
     */
    protected $endMarkerOffset;
    
    public function __construct($startMarkerText, $startMarkerOffset, $endMarkerText, $endMarkerOffset)
    {
        $this->startMarkerText = $startMarkerText;
        $this->startMarkerOffset = $startMarkerOffset;
        $this->endMarkerText = $endMarkerText;
        $this->endMarkerOffset = $endMarkerOffset;
        
        if (!$startMarkerText || !$endMarkerText) {
            throw new TocException("Start marker must contain text");
        }
        if ($endMarkerOffset <= $startMarkerOffset) {
            throw new TocException("Start marker offset must be smaller than end marker offset");
        }
    }
    
    public function getStartMarker()
    {
        return $this->startMarkerText;
    }

    public function getEndMarker()
    {
        return $this->endMarkerText;
    }

    public function getTocStart()
    {
        return $this->startMarkerOffset + strlen($this->startMarkerText);
    }
    
    public function getTocEnd()
    {
        return $this->endMarkerOffset;
    }
    
    public function getTocMarkupStart()
    {
        return $this->startMarkerOffset;
    }
    
    public function getTocMarkupEnd()
    {
        return $this->endMarkerOffset + strlen($this->endMarkerText);
    }
    
    public function getTocLength()
    {
        return $this->getTocEnd() - $this->getTocStart();
    }
    
}
