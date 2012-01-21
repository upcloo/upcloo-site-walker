<?php 
namespace Walk\Site;

use ZendTest\Di\TestAsset\CircularClasses\E;

class Page
{
    private $_html;
    
    private $_id;
    private $_title;
    private $_publishDate;
    private $_author;
    private $_tags;
    private $_categories;
    private $_type;
    
    private $_summary;
    private $_content;
    
    private $_page;
    private $_language;
    
    private $_sitekey;
    private $_url;
    
    private $_image;
    
    const ID = 'UPCLOO_POST_ID';
    const TITLE = 'UPCLOO_POST_TITLE';
    const PUBLISH_DATE = 'UPCLOO_POST_PUBLISH_DATE';
    const AUTHOR = 'UPCLOO_POST_AUTHOR';
    const TAGS = 'UPCLOO_POST_TAGS';
    const CATEGORIES = 'UPCLOO_POST_CATEGORIES';
    const TYPE = 'UPCLOO_POST_TYPE';
    const IMAGE = 'UPCLOO_POST_IMAGE';
    const CONTENT = 'UPCLOO_POST_CONTENT';
    
    const COMMENT_START = '<!-- %s';
    const COMMENT_STOP = '%s -->';
    const COMMENT_FULL = '<!-- %s -->';
    
    /**
     * 
     * Enter description here ...
     * @param string $url
     * @param string $html
     */
    public function __construct($url, $html)
    {
        $this->_html = $html;
        $this->_url = $url;
    }
    
    public function setSiteKey($sitekey)
    {
        $this->_sitekey = $sitekey;
    }
    
    /**
     * @return \Walk\Site\Page This page
     */
    public function parse()
    {
        $this->_id = $this->_parse(self::ID);
        $this->_title = $this->_parse(self::TITLE);
        $this->_publishDate = $this->_parse(self::PUBLISH_DATE);
        $this->_author = $this->_parse(self::AUTHOR);
        $this->_type = $this->_parse(self::TYPE);
        $tags = $this->_parse(self::TAGS);
        $categories = $this->_parse(self::CATEGORIES);
        
        $this->_content = $this->_parseBetween(self::CONTENT);
        
        $this->_tags = array_map('trim', explode(",", $tags));
        $this->_categories = array_map('trim', explode(",", $categories));
        
        return $this;
    }
    
    protected function _parseBetween($commentText)
    {
        $tag = sprintf(self::COMMENT_FULL, $commentText);
        
        $pos = strpos($this->_html, $tag);
        
        if ($pos !== false) {
            $init = $pos + strlen($tag);
            $tmp = substr($this->_html, $init);
            
            $end = strpos($tmp, $tag);
            
            if ($end !== false) {
                return substr($tmp, 0, $end);
            } else {
                return false;
            }
        }
        
        return false;
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
    
    public function getAuthor()
    {
        return $this->_author;
    }
    
    public function getTags()
    {
        return $this->_tags;
    }
    
    public function getCategories()
    {
        return $this->_categories;
    }
    
    public function getImage()
    {
        return $this->_image;
    }
    
    public function getSiteKey()
    {
        if (!$this->_sitekey) {
            throw new \Exception("You must provide the SiteKey!");
        }
        return $this->_sitekey;
    }
    
    public function getLanguage()
    {
        if (!$this->_language) {
            $this->_language = 'it';
        }
        
        return $this->_language;
    }
    
    public function getPage()
    {
        if (!$this->_page) {
            $this->_page = 1;
        }
        return $this->_page;
    }
    
    public function getType()
    {
        return $this->_type;
    }
    
    public function getContent()
    {
        return $this->_content;
    }
    
    public function getSummary()
    {
        $content = $this->getContent();
        //TODO: create summary on content
        
        $this->_summary = '';
        
        return $this->_summary;
    }
    
    public function asXml()
    {
        $doc = new \DOMDocument("1.0", "utf-8");
        
        $root = $doc->createElement("model");
        
        $idNode = $doc->createElement("id", $this->getId());
        $sitekeyNode = $doc->createElement("sitekey", $this->getSiteKey());
        $titleNode = $doc->createElement("title", $this->getTitle());
        
        $publishDateNode = $doc->createElement("publish_date", $this->getPublishDate());
        $typeNode = $doc->createElement("type", $this->getType());
        $imageNode = $doc->createElement("image", $this->getImage());
        
        $root->appendChild($idNode);
        $root->appendChild($sitekeyNode);
        $root->appendChild($titleNode);
        
        $root->appendChild($publishDateNode);
        $root->appendChild($typeNode);
        
        $root->appendChild($imageNode);
        
        $doc->appendChild($root);
        $doc->formatOutput = true;
        
        return $doc->saveXML($doc);
    }
}