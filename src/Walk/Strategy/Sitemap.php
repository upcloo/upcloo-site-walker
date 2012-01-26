<?php 

namespace Walk\Strategy;

class Sitemap extends StrategyAbstract
{
    const SCHEMA_SITEMAP = 'http://www.sitemaps.org/schemas/sitemap/sitemap.xsd';
    const SCHEMA_SITEMAP_INDEX = 'http://www.sitemaps.org/schemas/sitemap/siteindex.xsd';
    
    public function run()
    {
        $xml = $this->_getSitemap($this->_url);
        
        $doc = new \DOMDocument();
        $doc->loadXML($xml);
        
        if (@$doc->schemaValidate(self::SCHEMA_SITEMAP)) {
            //is a valid sitemap
            $locations = $doc->getElementsByTagName("loc");
            foreach ($locations as $location) {
                $url = $location->nodeValue;

                $this->_workOn($url);
            }
        } else {
            throw new \Walk\Strategy\SitemapException("Not a valid Sitemap at {$this->_url}");
        }
    }
    
    protected function _getSitemap($url)
    {
        $client = new \Zend\Http\Client($url);
        $response = $client->send();
        $xml = $response->getBody();
        
        return $xml;
    }
}