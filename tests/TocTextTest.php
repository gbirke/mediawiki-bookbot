<?php

class TocTextTest extends PHPUnit_Framework_TestCase {
    
    /**
     *
     * @var Birke\Mediawiki\Bookbot\TocText
     */
    protected $text;
    
    public function setUp() {
        $this->text = new Birke\Mediawiki\Bookbot\TocText(file_get_contents(__DIR__ . '/fixtures/test_text.txt'));
    } 
    
    public function testGetTocTextHasCorrectOffset() {
        $tt = $this->text->getTocText();
        $expected = "# [[Seite eins";
        $this->assertEquals($expected, substr($tt, 0, strlen($expected)));
    }
    
    public function testGetTocTextHasCorrectEnd() {
        $tt = $this->text->getTocText();
        $expected = "## [[Seite zwo]]";
        $this->assertEquals($expected, substr($tt, -strlen($expected)));
    }
    
    public function testGetTitlesAndLevels() {
        $expected = array(
            "Seite eins|Eins" => array(
                'title' => "Seite eins",
                'label' => "Eins",
                'level' => 1
            ),
            "Seite zwo|" => array(
                'title' => "Seite zwo",
                'label' => "",
                'level' => 2
            ),
        );
        $this->assertEquals($expected, $this->text->getTitlesAndLevels());
    }
    
    public function testTocHasChangedWhenTocIsIdentical() {
        $this->assertFalse($this->text->tocHasChanged(file_get_contents(__DIR__ . '/fixtures/new_toc_1.txt')));
    }
    
    public function testTocHasChangedWhenTocIsDifferent() {
        $this->assertTrue($this->text->tocHasChanged(file_get_contents(__DIR__ . '/fixtures/new_toc_2.txt')));
    }
    
    
}