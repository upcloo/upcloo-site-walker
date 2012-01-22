<?php
namespace Walk\Strategy;

class Crawler extends StrategyAbstract
{
    /**
     * @var \Zend\Queue\Queue
     */
    private $_queue;
    /**
     * @var \Walk\Model\Link
     */
    private $_links;
    
    private $_host;
    
    public function setQueue(\Zend\Queue\Queue $queue)
    {
        $this->_queue = $queue;
    }
    
    public function setLinks(\Walk\Model\Link $links)
    {
        $this->_links = $links;
    }
    
    public function setMainSeed($mainSeed)
    {
        $uri = new \Zend\Uri\Uri($mainSeed);
        $this->_host = $uri->getHost();
    }
    
    public function run()
    {
        while($this->_queue->count() > 0) {
            $messages = $this->_queue->receive();
            foreach ($messages as $message) {
                try {
                    $uri = $message->body;
                    
                    if (!$this->_links->exists($uri)) {
                        \phly\PubSub::publish(CONSOLE_TOPIC, "Working on: {$uri}");
                        $this->_links->insert($uri);
                        $client = new \Zend\Http\Client($uri);
                        $client->setConfig(array('useragent'=> $this->_userAgent));
                        $page = $client->send();
                        
                        $html = $page->getBody();
                        
                        $page = new \Walk\Site\Page($uri, $html);
                        $page->setSitekey($this->_sitekey);
                        
                        if ($page->parse()->getId()) {
                            \phly\PubSub::publish(GREEN_CONSOLE_TOPIC, "[{$page->getId()}] {$page->getTitle()}");
                            $xml = $page->parse()->asXml();
                            $filename = $this->_outputDirectory . "/" . $page->getId() . ".xml";
                            file_put_contents($filename, $xml);
                        }
                        
                        $dom = new \Zend\Dom\Query($html);
                        
                        //Get all links of this page...
                        $results = $dom->execute("a");
                        foreach ($results as $result) {
                            $linkUrl = $result->getAttribute("href");
                            
                            $uri = new \Zend\Uri\Uri($linkUrl);
    
                            if ($uri->getHost() == $this->_host) {
                                if (!$this->_links->exists($linkUrl)) {
                                    $this->_queue->send($linkUrl);
                                }
                            } 
                        }
                    }
                } catch (\Exception $e) {
                    \phly\PubSub::publish(RED_CONSOLE_TOPIC, $e->getMessage());
                }
                
                $this->_queue->deleteMessage($message);
            }
        }
        
        \phly\PubSub::publish(CONSOLE_TOPIC, "All pages captured... This is the end, my only friend, the end...");
    }
}