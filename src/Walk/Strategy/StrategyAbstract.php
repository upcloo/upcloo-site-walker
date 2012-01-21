<?php 
namespace Walk\Strategy;

abstract class StrategyAbstract
{
    protected $_userAgent = 'UpCloo-Spider-1.0';
    protected $_sitekey;
    protected $_outputDirectory;
    
    abstract public function run();
    
    public function setSitekey($sitekey)
    {
        $this->_sitekey = $sitekey;
    }
    
    public function setOutputDirectory($outputDirectory)
    {
        $this->_outputDirectory = $outputDirectory;
    }
}