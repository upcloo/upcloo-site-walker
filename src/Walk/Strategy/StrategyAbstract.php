<?php 
namespace Walk\Strategy;

abstract class StrategyAbstract
{
    protected $_userAgent = 'UpCloo-Spider-1.0';
    protected $_sitekey;
    protected $_outputDirectory;
    /**
     * @var \Walk\Model\Link
     */
    protected $_links;
    
    
    protected $_host;
    protected $_url;
    
    abstract public function run();
    
    /**
    * Get the URI and genrate the response...
    *
    * @param string $uri
    * @return string The HTML page
    */
    protected function _workOn($uri)
    {
        $client = new \Zend\Http\Client($uri);
        $client->setConfig(array('useragent'=> $this->_userAgent));
        $page = $client->send();
    
        $html = $page->getBody();
    
        $page = new \Walk\Site\Page($uri, $html);
        $page->setSitekey($this->_sitekey);
    
        if ($page->parse()->getId()) {
            $filename = $this->_outputDirectory . "/" . $page->getId() . ".xml";
    
            if (!file_exists($filename)) {
                $xml = $page->asXml();
                file_put_contents($filename, $xml);
                \phly\PubSub::publish(\Walk\Setting::GREEN_CONSOLE_TOPIC, "[{$page->getId()}] {$page->getTitle()}");
            } else {
                \phly\PubSub::publish(\Walk\Setting::RED_CONSOLE_TOPIC, "[{$page->getId()}] {$page->getTitle()} already written on file... I skip it...");
            }
        }
    
        return $html;
    }
    
    public function setMainSeed($mainSeed)
    {
        $uri = new \Zend\Uri\Uri($mainSeed);
        $this->_url = $mainSeed;
        $this->_host = $uri->getHost();
    }
    
    public function setSitekey($sitekey)
    {
        $this->_sitekey = $sitekey;
    }
    
    public function setOutputDirectory($outputDirectory)
    {
        $this->_outputDirectory = $outputDirectory;
    }
    
    public function setLinks(\Walk\Model\Link $links)
    {
        $this->_links = $links;
    }
}