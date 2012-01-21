<?php 
namespace Walk\Site;

use ZendTest\Di\TestAsset\CircularClasses\E;

class Page
{
    private $_html;
    
    private $_id;
    private $_title;
    private $_publishDate;
    
    private $_doc;
    
    const ID = 'UPCLOO_POST_ID';
    const TITLE = 'UPCLOO_POST_TITLE';
    const PUBLISH_DATE = 'UPCLOO_POST_PUBLISH_DATE';
    
    const COMMENT_START = '<!-- %s';
    const COMMENT_STOP = '%s -->';
    
    /**
     * 
     * Enter description here ...
     * @param string $html
     */
    public function __construct($html)
    {
        $this->_html = $html;
    }
    
    /**
     * @return \Walk\Site\Page This page
     */
    public function parse()
    {
        $this->_id = $this->_parse(self::ID);
        $this->_title = $this->_parse(self::TITLE);
        $this->_publishDate = $this->_parse(self::PUBLISH_DATE);
        
        return $this;
    }
    
    protected function _parse($commentText)
    {
        
        $pos = strpos($this->_html, $commentText);
        if ($pos !== false) {
            $endPos = strpos($this->_html, sprintf(self::COMMENT_STOP, $commentText));
            
            $piece = trim(substr($this->_html, $pos+strlen($commentText)));
            $posOfSpace = strpos($piece, sprintf(self::COMMENT_STOP, $commentText));
            
            return trim(substr($piece, 0, $posOfSpace));
        }
        
        return false;
    }
    
    public function getId()
    {
        return $this->_id;
    }
    
    public function getTitle()
    {
        return $this->_title;
    }
    
    public function getPublishDate()
    {
        return $this->_publishDate;
    }
    
    public function asXml()
    {
        return $this->_doc;
    }
}