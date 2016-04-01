<?php
class CMongoQueryBuilder extends CMongoCursor
{
    /**
     * Convert find result to object
     * 
     * @param array $mongoDocument
     * @return className
     */
    protected function toObject($mongoDocument)
    {
        $className = $this->_collection->getDocumentClassName($mongoDocument);
        return new $className($this->_collection, $mongoDocument, array(
            'stored' => true
        ));
    }
}
