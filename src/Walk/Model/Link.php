<?php
namespace Walk\Model;

class Link extends \Zend\Db\Table\AbstractTable
{
    protected $_name = 'links';
    
    public function exists($link)
    {
        return $this->fetchRow(array('hash = ?', sha1($link)));
    }
    
    public function insert($link) 
    {
        $this->insert(array('link' => $link, 'hash' => sha1($link)));
    }
}