<?php
namespace Walk\Strategy;

class Crawler extends StrategyAbstract
{
    /**
     * @var \Zend\Queue\Queue
     */
    private $_queue;
    
    public function setQueue(\Zend\Queue\Queue $queue)
    {
        $this->_queue = $queue;
    }
    
    public function run()
    {
        while($this->_queue->count() > 0) {
            $messages = $this->_queue->receive();
            foreach ($messages as $message) {
                try {
                    $uri = $message->body;
                    
                    if (!$this->_links->exists($uri)) {
                        \phly\PubSub::publish(\Walk\Setting::CONSOLE_TOPIC, "Working on: {$uri}");
                        $this->_links->store($uri);
                        
                        $html = $this->_workOn($uri);
                        
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
                    \phly\PubSub::publish(\Walk\Setting::RED_CONSOLE_TOPIC, $e->getMessage());
                }
                
                $this->_queue->deleteMessage($message);
            }
        }
        
        \phly\PubSub::publish(\Walk\Setting::CONSOLE_TOPIC, "All pages captured... This is the end, my only friend, the end...");
    }
}