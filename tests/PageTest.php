<?php 

class PageTest extends PHPUnit_Framework_TestCase
{
    public function testValidBase()
    {
        $html = file_get_contents(__DIR__ . '/stuffs/little.html');
        
        $page = new Walk\Site\Page("http://test.local", $html);
        $id = $page->parse()->getId();
        
        $this->assertEquals('post_1088', $id);
    }
    
    public function testRealPage()
    {
        $html = file_get_contents(__DIR__ . '/stuffs/single-content.html');
    
        $page = new Walk\Site\Page("http://test.local", $html);

        $id = $page->parse()->getId();
        $this->assertEquals('post_1088', $id);
        
        $title = $page->getTitle();
        $this->assertEquals('Zend\Di impressioni - ZF2', $title);
        
        $publishDate = $page->getPublishDate();
        $this->assertEquals('2012-01-08T15:25:42Z', $publishDate);
        
        $author = $page->getAuthor();
        $this->assertEquals('Walter Dal Mut', $author);
        
        $tags = $page->getTags();
        $this->assertInternalType('array', $tags);
        
        $content = $page->getContent();
        $this->assertInternalType('string', $content);
        $this->assertGreaterThan(0, strlen($content));
        
        $this->assertEquals(7, count($tags));
        //Dependency Injection,DiC,php,zend,Zend\Di,Zend_Di,zf2
        $this->assertEquals('Dependency Injection', $tags[0]);
        $this->assertEquals('DiC', $tags[1]);
        $this->assertEquals('php', $tags[2]);
        $this->assertEquals('zend', $tags[3]);
        $this->assertEquals('Zend\Di', $tags[4]);
        $this->assertEquals('Zend_Di', $tags[5]);
        $this->assertEquals('zf2', $tags[6]);
        
        $categories = $page->getCategories();
        $this->assertInternalType('array', $categories);
        $this->assertEquals(3, count($categories));

//         Applicazioni,PHP,Tests
        $this->assertEquals('Applicazioni', $categories[0]);
        $this->assertEquals('PHP', $categories[1]);
        $this->assertEquals('Tests', $categories[2]);
    }
    
    public function testContent()
    {
        $page = new Walk\Site\Page(
        	"http://test.local", 
        	"This is only and example...<!-- UPCLOO_POST_CONTENT -->This is my content<!-- UPCLOO_POST_CONTENT -->And this is the tail"
        );
        
        $content = $page->parse()->getContent();
        $this->assertEquals('This is my content', $content);
    }
    
    public function testXmlOutput()
    {
        $html = file_get_contents(__DIR__ . '/stuffs/single-content.html');
        
        $page = new Walk\Site\Page("http://test.local", $html);
        $page->setSitekey("ts90TEsts");
        $page->parse();

        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}