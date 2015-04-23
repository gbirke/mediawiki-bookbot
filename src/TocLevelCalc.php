<?php

/**
 * This file contains the class TocLevelCalc
 * 
 * @author birkeg
 */

namespace Birke\Mediawiki\Tocbot;

/**
 * Calculate the correct level for headings
 *
 * @author birkeg
 */
class TocLevelCalc {
    
    protected $lastDifference = 0;
    
    protected $lastLevel = -1;
    
    
    public function getNewTocLevel($oldlevel, $parentLevel) {
        if ($oldlevel > $this->lastLevel && $this->lastLevel > -1) {
            return $oldlevel + $this->lastDifference;
        }
        if ($oldlevel <= $parentLevel) {
            $this->lastLevel = $oldlevel;
            $this->lastDifference = ($parentLevel - $oldlevel) + 1;
            return $oldlevel + $this->lastDifference;
        }
        
        return $oldlevel;
    }
    
    
}
