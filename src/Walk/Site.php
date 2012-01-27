<?php 
namespace Walk;

use Walk\Strategy\Crawler;

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
    
    private $_walkMethod;
    
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
        \phly\PubSub::publish(\Walk\Setting::CONSOLE_TOPIC, "Boot the walker...");
        
        //Init database connections
        self::$_instance->_createQueues();
        self::$_instance->_createBase();
        
        \phly\PubSub::publish(\Walk\Setting::CONSOLE_TOPIC, "Boot ends...");
        
        //Start
        $this->_walkMethod->setLinks($this->_links);
        $this->_walkMethod->setSitekey($this->_sitekey);
        $this->_walkMethod->setOutputDirectory($this->_outputDirectory);
        $this->_walkMethod->setMainSeed($this->_site);
        
        if ($this->_walkMethod instanceof \Walk\Strategy\Crawler) {
            $this->_queue->send($this->_site);
            $this->_walkMethod->setQueue($this->_queue);
        }
        
        $this->_walkMethod->run();
    }
    
    public function setWalkMethod(\Walk\Strategy\StrategyAbstract $method)
    {
        $this->_walkMethod = $method;
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
        
        \phly\PubSub::publish(\Walk\Setting::GREEN_CONSOLE_TOPIC, "In memory array queue started successfully");
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
        
        \phly\PubSub::publish(\Walk\Setting::GREEN_CONSOLE_TOPIC, "Links database started successfully");
    }
}