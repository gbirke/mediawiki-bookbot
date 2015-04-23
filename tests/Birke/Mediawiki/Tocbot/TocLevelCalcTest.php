<?php

/**
 * This file contains the class TocLevelCalcTest
 * 
 * @author birkeg
 */

/**
 * 
 *
 * @author birkeg
 */
class TocLevelCalcTest extends PHPUnit_Framework_TestCase {
    
    protected function getNewLevels($levels, $parentLevel) {
        $calc = new Birke\Mediawiki\Tocbot\TocLevelCalc();
        $newLevels = array();
        foreach($levels as $l) {
            $newLevels[] = $calc->getNewTocLevel($l, $parentLevel);
        }
        return $newLevels;
    }
    
    public function testLevelsAreUnchangedIfTheyAreLowerThanDesired() {
        $levels = array(2,2,3,3,2);
        $this->assertEquals($levels, $this->getNewLevels($levels, 1));
    }
    
    public function testLevelsAreLoweredIfHigherThanDesired() {
        $expectedLevelsForOne = array(2,2,2);
        $expectedLevelsForTwo = array(3,3,3);
        $expectedLevelsForThree = array(4,4,4);
        $levels = array(1,1,1);
        $this->assertEquals($expectedLevelsForOne, $this->getNewLevels($levels, 1));
        $this->assertEquals($expectedLevelsForTwo, $this->getNewLevels($levels, 2));
        $this->assertEquals($expectedLevelsForThree, $this->getNewLevels($levels, 3));
    }
    
    public function testChildLevelsAreLoweredIfHigherThanDesired() {
        $expectedLevelsForOne = array(2,3,3,4,2,2);
        $expectedLevelsForTwo = array(3,4,4,5,3,3);
        
        $levels = array(1,2,2,3,1,1);
        $this->assertEquals($expectedLevelsForOne, $this->getNewLevels($levels, 1));
        $this->assertEquals($expectedLevelsForTwo, $this->getNewLevels($levels, 2));
    }
    
    
}
