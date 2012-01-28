<?php 
class SiteMapStrategyTest
    extends \Zend\Test\PHPUnit\DatabaseTestCase
{
    private $_connectionMock;
    private $_links;
    
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
    }
    
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