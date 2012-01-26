<?php 
class SiteMapStrategyTest
    extends PHPUnit_Framework_TestCase
{
    public function testBaseSiteMap()
    {
        $stub = $this->getMock(
    		'\Walk\Strategy\Sitemap',
            array('_getSitemap', '_workOn')
        );
        
        $stub->expects($this->any())
            ->method('_getSitemap')
            ->will(
                $this->returnValue(
                    file_get_contents(__DIR__ . '/stuffs/simple-sitemap.xml')
                )
            );
        
        $stub->expects($this->any())
            ->method('_getSitemap')
            ->will(
                $this->returnValue(
                    file_get_contents(__DIR__ . '/stuffs/not-valid-page.html')
                )
            );
        
        $stub->setMainSeed("http://localhost");
        $stub->run();
    }
    
    /**
     * @expectedException \Walk\Strategy\SitemapException 
     */
    public function testNotValidSiteMap()
    {
        $stub = $this->getMock(
    		'\Walk\Strategy\Sitemap',
            array('_getSitemap', '_workOn')
        );
        
        $stub->expects($this->any())
            ->method('_getSitemap')
            ->will(
                $this->returnValue(
                    file_get_contents(__DIR__ . '/stuffs/not-valid-sitemap.xml')
                )
            );
        
        $stub->expects($this->any())
            ->method('_getSitemap')
            ->will(
                $this->returnValue(
                    file_get_contents(__DIR__ . '/stuffs/not-valid-page.html')
                )
            );

        $stub->setMainSeed("http://localhost");
        $stub->run();
    }
}