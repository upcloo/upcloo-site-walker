<?php
namespace Walk\Model;

class Link extends \Zend\Db\Table\AbstractTable
{
    protected $_name = 'links';
    
    public function exists($link)
    {
        $select = $this->select()->where('hash = ?', sha1($link));
        return $this->fetchRow($select);
    }
    
    public function store($link) 
    {
        $this->insert(array('link' => $link, 'hash' => sha1($link)));
    }
}