<?php 

namespace Walk\Strategy;

class Sitemap extends StrategyAbstract
{
    const SCHEMA_SITEMAP = 'http://www.sitemaps.org/schemas/sitemap/sitemap.xsd';
    const SCHEMA_SITEMAP_INDEX = 'http://www.sitemaps.org/schemas/sitemap/siteindex.xsd';
    
    public function run($url = false)
    {
        if (!$url) {
            $url = $this->_url;
        }
        
        $xml = $this->_getSitemap($url);
        
        $doc = new \DOMDocument();
        $doc->loadXML($xml);
        
        if ($this->_isSitemap($doc)) {
            $this->_fetchSitemap($doc);
        } else if ($this->_isSitemapIndex($doc)) {
            $this->_fetchSitemapIndex($doc);
        } else {
            throw new \Walk\Strategy\SitemapException("Not a valid Sitemap at {$url}");
        }
    }
    
    protected function _fetchSitemapIndex(\DOMDocument $document)
    {
        $locations = $document->getElementsByTagName("loc");
        foreach ($locations as $location) {
            $url = (string)$location->nodeValue;
            
            $this->run($url);
        }
    }
    
    protected function _fetchSitemap(\DOMDocument $document)
    {
        $locations = $document->getElementsByTagName("loc");
        foreach ($locations as $location) {
            $url = $location->nodeValue;
        
            $this->_workOn($url);
        }
    }
    
    /**
     * Check if this document is a sitemap index
     * 
     * @param \DOMDocument $document
     */
    private function _isSitemapIndex(\DOMDocument $document)
    {
        return ((@$document->schemaValidate(self::SCHEMA_SITEMAP_INDEX)) ? true : false);
    }
    
    /**
     * Check if this document is  a url sitemap
     * 
     * @param \DOMDocument $document
     */
    private function _isSitemap(\DOMDocument $document)
    {
        return ((@$document->schemaValidate(self::SCHEMA_SITEMAP)) ? true : false);
    }
    
    protected function _getSitemap($url)
    {
        $client = new \Zend\Http\Client($url);
        $response = $client->send();
        $xml = $response->getBody();
        
        return $xml;
    }
}