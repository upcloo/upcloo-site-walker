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
        
        if ($doc->schemaValidate(self::SCHEMA)) {
            //is a valid sitemap
            $locations = $doc->getElementsByTagName("loc");
            foreach ($locations as $location) {
                $url = $location->nodeValue;

                $this->_workOn($url);
            }
        }
    }
}