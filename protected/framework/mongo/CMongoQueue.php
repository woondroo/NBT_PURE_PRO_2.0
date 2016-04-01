<?php
class CMongoQueue implements Countable
{
    /**
     *
     * @var CMongoCollection
     */
    private $_collection;
    
    public function __construct(CMongoDatabase $database, $channel)
    {
        $this->_collection = $database
            ->getCollection($channel)
            ->disableDocumentPool();
    }
    
    /**
     * Add item to queue
     * 
     * @param mixed $payload data to send
     * @param int $priority more priority num give quicker geting from queue
     */
    public function enqueue($payload, $priority = 0)
    {
        $this->_collection
            ->createDocument(array(
                'payload'   => $payload,
                'priority'  => (int) $priority,
                'datetime'  => new MongoDate,
            ))
            ->save();
        
        return $this;
    }
    
    /**
     * Get item from queue as is
     * 
     * @return mixed
     */
    public function dequeuePlain()
    {
        $document = $this->_collection
            ->find()
            ->sort(array(
                'priority' => -1,
                'datetime' => 1,
            ))
            ->findAndRemove();
        
        if(!$document) {
            return null;
        }
        
        return $document->get('payload');
    }
    
    /**
     * Get item from queue as CMongoStructure if array put into queue
     * 
     * @return mixed|CMongoStructure
     */
    public function dequeue()
    {
        $value = $this->dequeuePlain();
        return is_array($value) ? new CMongoStructure($value) : $value;
    }
    
    /**
     * Get number of elements in queue
     * 
     * @return int
     */
    public function count()
    {
        return count($this->_collection);
    }
    
    /**
     * Clear queue
     * 
     * @return CMongoQueue
     */
    public function clear()
    {
        $this->_collection->delete();
        return $this;
    }
}
