<?php
namespace Walk;

class Crawler
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
    
    private $_userAgent = 'UpCloo-Spider-1.0';
    
    public static function setValidHost($host)
    {
        
    }
    
    public static function start(\Zend\Queue\Queue $queue, \Walk\Model\Link $links, $mainSeed)
    {
        $instance = new self();
        $instance->_queue = $queue;
        $instance->_links = $links;
        
        $uri = new \Zend\Uri\Uri($mainSeed);
        $instance->_host = $uri->getHost();
        
        $instance->run();
    }
    
    public function run()
    {
        while($this->_queue->count() > 0) {
            $messages = $this->_queue->receive();
            foreach ($messages as $message) {
                try {
                    \phly\PubSub::publish(CONSOLE_TOPIC, "Get page '{$message->body}' from queue");
                    $uri = $message->body;
                    
                    if (!$this->_links->exists($uri)) {
                        $this->_links->insert($uri);
                        \phly\PubSub::publish(GREEN_CONSOLE_TOPIC, "Link analyzed: {$uri}");
                        $client = new \Zend\Http\Client($uri);
                        $client->setConfig(array('useragent'=> $this->_userAgent));
                        $page = $client->send();
                        
                        $html = $page->getBody();
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
                            } else {
                                \phly\PubSub::publish(CONSOLE_TOPIC, "Host not valid: {$linkUrl}");
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