<?php 

namespace Walk\Strategy;

class Sitemap extends StrategyAbstract
{
    const SCHEMA = 'http://www.sitemaps.org/schemas/sitemap/sitemap.xsd';
    
    public function run()
    {
        $client = new \Zend\Http\Client($this->_url);
        
        $response = $client->send();
        
        $doc = new \DOMDocument();
        $doc->loadXML($response->getBody());
        
        var_dump($doc->schemaValidate(self::SCHEMA));
    }
}