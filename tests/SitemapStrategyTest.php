<?php 
class SitemapStrategyTest
    extends \Zend\Test\PHPUnit\DatabaseTestCase
{
    private $_connectionMock;
    protected $_links;
    protected $_stub;
    
    protected function getConnection()
    {
        if($this->_connectionMock == null) {
            $connection = \Zend\Db\Db::factory("Pdo\Sqlite", array('dbname'=>':memory:'));
            $this->_connectionMock = $this->createZendDbConnection($connection, 'zfunittests');
            \Zend\Db\Table\AbstractTable::setDefaultAdapter($connection);
        }
        
        $this->_connectionMock->getConnection()->query(file_get_contents(__DIR__ . '/../src/walk.sql'));
    
        return $this->_connectionMock;
    }
    
    protected function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__ . '/stuffs/links-seed.xml');
    }
    
    public function setUp()
    {
        parent::setUp();
    
        $this->_links = new \Walk\Model\Link();
        
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
            ->method('_workOn')
            ->will(
                $this->returnValue(
                    file_get_contents(__DIR__ . '/stuffs/not-valid-page.html')
                )
            );
        
        $stub->setLinks($this->_links);
        
        $this->_stub = $stub;
        $this->_stub->setMainSeed("http://localhost");
    }
    
    public function testBaseSiteMap()
    {
        $this->_stub->run();
        
        $links = $this->_stub->getLinks();
        $links = $links->fetchAll(null, array('link_id'));
        
        $this->assertEquals(2, count($links));

        $this->assertEquals("http://your-domain.ltd/index.html", $links[0]->link);
        $this->assertEquals("http://your-domain.ltd/second-page.html", $links[1]->link);
        
        $this->_stub->run();
        
        $links = $this->_stub->getLinks();
        $links = $links->fetchAll(null, array('link_id'));
        
        $this->assertEquals(2, count($links));
        
        $this->assertEquals("http://your-domain.ltd/index.html", $links[0]->link);
        $this->assertEquals("http://your-domain.ltd/second-page.html", $links[1]->link);
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
            ->method('_workOn')
            ->will(
                $this->returnValue(
                    file_get_contents(__DIR__ . '/stuffs/not-valid-page.html')
                )
            );

        $stub->setLinks($this->_links);
        $stub->setMainSeed("http://localhost");
        $stub->run();
    }
}