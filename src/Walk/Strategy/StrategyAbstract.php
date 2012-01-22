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