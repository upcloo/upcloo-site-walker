<?php 

class PageTest extends PHPUnit_Framework_TestCase
{
    public function testValidBase()
    {
        $html = file_get_contents(__DIR__ . '/stuffs/little.html');
        
        $page = new Walk\Site\Page($html);
        $id = $page->parse()->getId();
        
        $this->assertEquals('post_1088', $id);
    }
    
    public function testRealPage()
    {
        $html = file_get_contents(__DIR__ . '/stuffs/single-content.html');
    
        $page = new Walk\Site\Page($html);

        $id = $page->parse()->getId();
        $this->assertEquals('post_1088', $id);
        
        $title = $page->getTitle();
        $this->assertEquals('Zend\Di impressioni - ZF2', $title);
        
        $publishDate = $page->getPublishDate();
        $this->assertEquals('2012-01-08T15:25:42Z', $publishDate);
    }
}