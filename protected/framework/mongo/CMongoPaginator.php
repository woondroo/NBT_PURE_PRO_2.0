<?php
class CMongoPaginator implements Iterator
{
    private $_currentPage = 1;
    
    private $_itemsOnPage = 30;
    
    private $_totalRowsCount;
    
    /**
     *
     * @var CMongoQueryBuilder
     */
    private $_cursor;
    
    public function __construct(CMongoCursor $cursor = null)
    {
        if($cursor) {
            $this->setCursor($cursor);
        }
    }
    
    public function __destruct()
    {
        $this->_cursor = null;
    }
    
    /**
     * 
     * @param int $itemsOnPage
     * @return CMongoPaginator
     */
    public function setItemsOnPage($itemsOnPage)
    {
        $this->_itemsOnPage = (int) $itemsOnPage;
        
        $this->_cursor->limit($this->_itemsOnPage);
        
        // define offset
        $this->_applyLimits();
        
        return $this;
    }
    
    /**
     * 
     * @param int $currentPage
     * @return CMongoPaginator
     */
    public function setCurrentPage($currentPage)
    {        
        $this->_currentPage = (int) $currentPage;
        
        // define offset
        $this->_applyLimits();
        
        return $this;
    }
    
    public function getCurrentPage()
    {
        // check if current page number greater than max allowed
        $totalPageCount = $this->getTotalPagesCount();
        
        // no document found - page is 1
        if(!$totalPageCount) {
            return 1;
        }
        
        if($this->_currentPage <= $totalPageCount) {
            $currentPage = $this->_currentPage;
        } else {
            $currentPage = $totalPageCount;
        }
        
        return $currentPage;
    }
    
    /**
     * Define cursor for paginator
     * 
     * @param CMongoCursor $cursor
     * @return CMongoPaginator
     */
    public function setCursor(CMongoCursor $cursor)
    {
        $this->_cursor = clone $cursor;
        
        $this->_applyLimits();
        
        return $this;
    }
    
    /**
     * Define cursor for paginator
     * 
     * @deprecated since 1.2.0 use self::setCursor()
     * @param CMongoCursor $cursor
     * @return type
     */
    public function setQueryBuilder(CMongoCursor $cursor)
    {
        return $this->setCursor($cursor);
    }
    
    public function getTotalRowsCount()
    {
        if($this->_totalRowsCount) {
            return $this->_totalRowsCount;
        }
        
        $this->_totalRowsCount = $this->_cursor->count();
        
        return $this->_totalRowsCount;
    }
    
    public function getTotalPagesCount()
    {
        return (int) ceil($this->getTotalRowsCount() / $this->_itemsOnPage);
    }
    
    private function _applyLimits()
    {
        if(!$this->_cursor) {
            return;
        }
        
        $currentPage = $this->getCurrentPage();
        
        // get page of rows
        $this->_cursor
            ->limit($this->_itemsOnPage)
            ->skip(($currentPage - 1) * $this->_itemsOnPage);
    }
    
    /**
     * @return CMongoDocument
     */
    public function current()
    {
        return $this->_cursor->current();
    }
    
    public function key()
    {
        return $this->_cursor->key();
    }
    
    public function next()
    {
        $this->_cursor->next();
        return $this;
    }
    
    public function rewind()
    {
        $this->_cursor->rewind();
        return $this;
    }
    
    public function valid()
    {
        return $this->_cursor->valid();
    }
}
