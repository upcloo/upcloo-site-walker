<?php 
namespace Walk;

use \Zend\Db\Table\AbstractTable;

class Site
{
    private $_site;
    private $_sitekey;
    private $_outputDirectory;
    
    private static $_instance;
    
    private $_console;
    
    private $_queue;
    private $_links;
    
    /**
     * @return \Walk\Site
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
            
            self::$_instance->_console = \Wally\Console\Console::getInstance();
        }
        
        return self::$_instance;
    }
    
    public function setSite($site)
    {
        $this->_site = $site;
    }
    
    public function setSiteKey($sitekey)
    {
        $this->_sitekey = $sitekey;
    }
    
    public function setOutputDirectory($dir)
    {
        $this->_outputDirectory = $dir;
    }
    
    public function getOutputDirectory()
    {
        return $this->_outputDirectory;
    }
    
    public function walk()
    {
        \phly\PubSub::publish(CONSOLE_TOPIC, "Boot the walker...");
        
        //Init database connections
        self::$_instance->_createQueues();
        self::$_instance->_createBase();
        
        \phly\PubSub::publish(CONSOLE_TOPIC, "Boot ends...");
        
        //Init walking
        //Push the first link (seed) into the queue
        $this->_queue->send($this->_site);
        Crawler::start(
            $this->_queue, 
            $this->_links, 
            $this->_site, 
            $this->_sitekey, 
            $this->_outputDirectory
        );
    }
    
    private function _createQueues()
    {        
        $queue = new \Zend\Queue\Queue(
        	"ArrayAdapter", 
            array(
                'name' => 'links'
            )
        );

        $this->_queue = $queue;
        
        \phly\PubSub::publish(GREEN_CONSOLE_TOPIC, "In memory array queue started successfully");
    }
    
    private function _createBase()
    {
        $dbFileName = $this->getOutputDirectory() . "/links.sqlite";
        
        $file = new \SplFileInfo($dbFileName);
        $file->openFile("w");
        
        $specFileName = __DIR__ . '/../walk.sql';
        $spec = file_get_contents($specFileName);
        
        $db = \Zend\Db\Db::factory(
        	"Pdo\Sqlite", 
            array('dbname' => $dbFileName)
        );
        
        $db->query($spec);
        
        AbstractTable::setDefaultAdapter($db);
        $this->_links = new \Walk\Model\Link();
        
        \phly\PubSub::publish(GREEN_CONSOLE_TOPIC, "Links database started successfully");
    }
}