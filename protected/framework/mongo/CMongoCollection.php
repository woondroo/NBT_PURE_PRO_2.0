<?php
class CMongoCollection implements Countable
{
    protected $_queryBuliderClass = 'CMongoQueryBuilder';
    
    protected $_queryExpressionClass = 'CMongoExpression';

	protected $_mongoDocumentClass = 'CMongoDocument';
    
    /**
     *
     * @var list of indexes
     */
    protected $_index;
    
    /**
     *
     * @var CMongoDatabase
     */
    protected $_database;
    
    /**
     *
     * @var MongoCollection
     */
    protected $_mongoCollection;

    /**
     *
     * @var list of cached documents
     */
    private $_documentsPool = array();
    
    /**
     *
     * @var cache or not documents
     */
    private $_documentPoolEnabled = true;
    
    public function __construct(CMongoDatabase $database, $collection)
    {
        $this->_database = $database;
        
        if($collection instanceof MongoCollection) {
            $this->_mongoCollection = $collection;
        } else {
            $this->_mongoCollection = $database->getMongoDB()->selectCollection($collection);
        }
        
    }
    
    public function __get($name)
    {
        return $this->getDocument($name);
    }
    
    /**
     * Get name of collection
     * @return string name of collection
     */
    public function getName()
    {
        return $this->_mongoCollection->getName();
    }
    
    /**
     * 
     * @return MongoCollection
     */
    public function getMongoCollection()
    {
        return $this->_mongoCollection;
    }
    
    /**
     * 
     * @return CMongoDatabase
     */
    public function getDatabase()
    {
        return $this->_database;
    }
    
    public function delete() {
        $status = $this->_mongoCollection->drop();
        if($status['ok'] != 1) {
            // check if collection exists
            if('ns not found' !== $status['errmsg']) {
                // collection exist
                throw new Exception('Error deleting collection ' . $this->getName());
            }
        }
        
        return $this;
    }
    
    /**
     * Override to define classname of document by document data
     * 
     * @param array $documentData
     * @return string CMongoDocument class data
     */
    public function setDocumentClassName($documentName = '')
    {
		$this->_mongoDocumentClass = $documentName;
    }

	/**
     * Override to define classname of document by document data
     * 
     * @param array $documentData
     * @return string CMongoDocument class data
     */
    public function getDocumentClassName(array $documentData = null)
    {
        return $this->_mongoDocumentClass;
    }
    
    /**
     * 
     * @param array $data
     * @return CMongoDocument
     */
    public function createDocument(array $data = null)
    {
        $className = $this->getDocumentClassName($data);
        
        return new $className($this, $data, array(
            'stored' => false,
        ));
    }
    
    public function count()
    {
        return $this->find()->count();
    }
    
    /**
     * Create document query builder
     * 
     * @return CMongoQueryBuilder|CMongoExpression
     */
    public function find()
    {
        return new $this->_queryBuliderClass($this, array(
            'expressionClass'   => $this->_queryExpressionClass,
        ));
    }
    
    /**
     * Retrieve a list of distinct values for the given key across a collection.
     * 
     * @param string $selector field selector
     * @param CMongoExpression $expression expression to search documents
     * @return array distinct values
     */
    public function getDistinct($selector, CMongoExpression $expression = null)
    {
        if($expression) {
            $expression = $expression->toArray();
        }
        
        return $this->_mongoCollection->distinct($selector, $expression);
    }
    
    /**
     * 
     * @return CMongoExpression
     */
    public function expression()
    {        
        return new $this->_queryExpressionClass;
    }
    
    /**
     * 
     * @return CMongoOperator
     */
    public function operator()
    {
        return new CMongoOperator;
    }
    
    /**
     * Create document query builder
     * 
     * @return CMongoQueryBuilder
     */
    public function findAsArray()
    {
        return new $this->_queryBuliderClass($this, array(
            'expressionClass'   => $this->_queryExpressionClass,
            'arrayResult' => true
        ));
    }
    
    public function disableDocumentPool()
    {
        $this->_documentPoolEnabled = false;
        return $this;
    }
    
    public function enableDocumentPool()
    {
        $this->_documentPoolEnabled = true;
        return $this;
    }
    
    public function clearDocumentPool()
    {
        $this->_documentsPool = array();
        return $this;
    }
    
    /**
     * Get document by id
     * 
     * @param string|MongoId $id
     * @return CMongoDocument|null
     */
    public function getDocument($id)
    {
        if(!$this->_documentPoolEnabled) {
            return $this->getDocumentDirectly($id);
        }
        
        if(!isset($this->_documentsPool[(string) $id])) {
            $this->_documentsPool[(string) $id] = $this->getDocumentDirectly($id);
        }
        
        return $this->_documentsPool[(string) $id];
    }
    
    /**
     * Get document by id directly omiting cache
     * 
     * @param type $id
     * @return CMongoDocument|null
     */
    public function getDocumentDirectly($id)
    {
        return $this->find()->byId($id)->findOne();
    }
    
    /**
     * Get document by id
     * 
     * @param string|MongoId $id
     * @return CMongoDocument|null
     */
    public function getDocuments(array $idList)
    {
        $documents = $this->find()->byIdList($idList)->findAll();
        if(!$documents) {
            return array();
        }
        
        if($this->_documentPoolEnabled) {
            $this->_documentsPool = array_merge(
                $this->_documentsPool,
                $documents
            );
        }
        
        return $documents;
    }
    
    /**
     * 
     * @param CMongoDocument $document
     * @return CMongoCollection
     * @throws Exception
     * @throws Validate
     */
    public function saveDocument(CMongoDocument $document, $validate = true)
    {
        $document->save($validate);
        return $this;
    }
    
    public function deleteDocument(CMongoDocument $document)
    {        
        $status = $this->_mongoCollection->remove(array(
            '_id'   => $document->getId()
        ));
        
        if($status['ok'] != 1) {
            throw new Exception('Delete error: ' . $status['err']);
        }
        
        // drop from document's pool
        unset($this->_documentsPool[(string) $document->getId()]);
        
        return $this;
    }
    
    public function deleteDocuments(CMongoExpression $expression)
    {
        $result = $this->_mongoCollection->remove($expression->toArray());
        if(!$result) {
            throw new Exception('Error removing documents from collection');
        }
        
        return $this;
    }
    
    public function insertMultiple($rows)
    {
        $document = $this->createDocument();
        
        foreach($rows as $row) {
            $document->fromArray($row);
            
            if(!$document->isValid()) {
                throw new Exception('CMongoDocument invalid');
            }
            
            $document->reset();
        }
        
        $result = $this->_mongoCollection->batchInsert($rows);
        if(!$result || $result['ok'] != 1) {
            throw new Exception('Batch insert error: ' . $result['err']);
        }
        
        return $this;
    }
    
    /**
     * Direct insert of array to MongoDB without creating documnt object and validation
     * 
     * @param array $document
     * @return CMongoCollection
     * @throws Exception
     */
    public function insert(array $document)
    {
        $result = $this->_mongoCollection->insert($document);
        if(!$result || $result['ok'] != 1) {
            throw new Exception('Insert error: ' . $result['err']);
        }
        
        return $this;
    }
    
    /**
     * Update multiple documents
     * @param CMongoExpression $expression expression to define 
     *  which documents will change. 
     * @param CMongoOperator|array $updateData new data or commands
     *  to update
     * @return CMongoCollection
     * @throws Exception
     */
    public function updateMultiple(CMongoExpression $expression, $updateData)
    {
        if($updateData instanceof CMongoOperator) {
            $updateData = $updateData->getAll();
        }
        
        $status = $this->_mongoCollection->update(
            $expression->toArray(), 
            $updateData,
            array(
                'multiple'  => true,
            )
        );
        
        if(1 != $status['ok']) {
            throw new Exception('Multiple update error: ' . $status['err']);
        }
        
        return $this;
    }
    
    public function updateAll($updateData)
    {
        if($updateData instanceof CMongoOperator) {
            $updateData = $updateData->getAll();
        }
        
        $status = $this->_mongoCollection->update(
            array(), 
            $updateData,
            array(
                'multiple'  => true,
            )
        );
        
        if(1 != $status['ok']) {
            throw new Exception('Multiple update error: ' . $status['err']);
        }
        
        return $this;
    }
    
    /**
     * Create Aggregator pipelines instance
     * 
     * @return CMongoAggregatePipelines
     */
    public function createPipeline() {
        return new CMongoAggregatePipelines($this);
    }
    
    /**
     * Aggregate using pipelines
     * 
     * @param type $pipelines
     * @return array result of aggregation
     * @throws Exception
     */
    public function aggregate($pipelines) {
        
        if($pipelines instanceof CMongoAggregatePipelines) {
            $pipelines = $pipelines->toArray();
        }
        elseif(!is_array($pipelines)) {
            throw new Exception('Wrong pipelines specified');
        }
        
        // log
        $client = $this->_database->getClient();
        if($client->hasLogger()) {
            $client->getLogger()->debug(
                get_called_class() . ':<br><b>Pipelines</b>:<br>' .
                json_encode($pipelines));
        }
        
        // aggregate
        $status = $this->_database->executeCommand(array(
            'aggregate' => $this->getName(),
            'pipeline'  => $pipelines
        ));
        
        if($status['ok'] != 1) {
            throw new Exception($status['errmsg']);
        }
        
        return $status['result'];
    }
    
    public function explainAggregate($pipelines)
    {
        if(version_compare($this->getDatabase()->getClient()->getDbVersion(), '2.6.0', '<')) {
            throw new Exception('Explain of aggregation implemented only from 2.6.0');
        }
        
        if($pipelines instanceof CMongoAggregatePipelines) {
            $pipelines = $pipelines->toArray();
        }
        elseif(!is_array($pipelines)) {
            throw new Exception('Wrong pipelines specified');
        }
        
        // aggregate
        return $this->_database->executeCommand(array(
            'aggregate' => $this->getName(),
            'pipeline'  => $pipelines,
            'explain'   => true
        ));
    }
    
    public function validate($full = false)
    {
        $response = $this->_mongoCollection->validate($full);
        if(!$response || $response['ok'] != 1) {
            throw new Exception($response['errmsg']);
        }
        
        return $response;
    }
    
    /**
     * Create index
     * 
     * @param array $key
     * @param array $options
     * @return CMongoCollection
     */
    public function ensureIndex($key, array $options = array())
    {
        $this->_mongoCollection->ensureIndex($key, $options);
        return $this;
    }
    
    /**
     * Create unique index
     * 
     * @param array $key
     * @param boolean $dropDups
     * @return CMongoCollection
     */
    public function ensureUniqueIndex($key, $dropDups = false)
    {
        $this->_mongoCollection->ensureIndex($key, array(
            'unique'    => true,
            'dropDups'  => (bool) $dropDups,
        ));
        
        return $this;
    }
    
    /**
     * Create sparse index
     * 
     * @param type $key
     * @return CMongoCollection
     */
    public function ensureSparseIndex($key)
    {
        $this->_mongoCollection->ensureIndex($key, array(
            'sparse'    => true,
        ));
        
        return $this;
    }
    
    /**
     * Create TTL index
     * 
     * @param array $key
     * @param int $seconds
     * @return CMongoCollection
     */
    public function ensureTTLIndex($key, $seconds)
    {
        $this->_mongoCollection->ensureIndex($key, array(
            'expireAfterSeconds' => $seconds,
        ));
        
        return $this;
    }
    
    /**
     * Create indexes based on self::$_index metadata
     * 
     * @return CMongoCollection
     * @throws Exception
     */
    public function initIndexes()
    {
        foreach($this->_index as $options) {
            
            if(empty($options['keys'])) {
                throw new Exception('Keys not specified');
            }
            
            $keys = $options['keys'];
            unset($options['keys']);
            
            $this->_mongoCollection->ensureIndex($keys, $options);
        }
        
        return $this;
    }
    
    public function readPrimaryOnly()
    {
        $this->_mongoCollection->setReadPreference(MongoClient::RP_PRIMARY);
        return $this;
    }
    
    public function readPrimaryPreferred(array $tags = null)
    {
        $this->_mongoCollection->setReadPreference(MongoClient::RP_PRIMARY_PREFERRED, $tags);
        return $this;
    }
    
    public function readSecondaryOnly(array $tags = null)
    {
        $this->_mongoCollection->setReadPreference(MongoClient::RP_SECONDARY, $tags);
        return $this;
    }
    
    public function readSecondaryPreferred(array $tags = null)
    {
        $this->_mongoCollection->setReadPreference(MongoClient::RP_SECONDARY_PREFERRED, $tags);
        return $this;
    }
    
    public function readNearest(array $tags = null)
    {
        $this->_mongoCollection->setReadPreference(MongoClient::RP_NEAREST, $tags);
        return $this;
    }
    
    /**
     * @param string|integer $w write concern
     * @param int $timeout timeout in miliseconds
     */
    public function setWriteConcern($w, $timeout = 10000)
    {
        if(!$this->_mongoCollection->setWriteConcern($w, (int) $timeout)) {
            throw new Exception('Error setting write concern');
        }
        
        return $this;
    }
    
    /**
     * @param int $timeout timeout in miliseconds
     */
    public function setUnacknowledgedWriteConcern($timeout = 10000)
    {
        $this->setWriteConcern(0, (int) $timeout);
        return $this;
    }
    
    /**
     * @param int $timeout timeout in miliseconds
     */
    public function setMajorityWriteConcern($timeout = 10000)
    {
        $this->setWriteConcern('majority', (int) $timeout);
        return $this;
    }
    
    public function getWriteConcern()
    {
        return $this->_mongoCollection->getWriteConcern();
    }
    
    public function stats()
    {
        return $this->getDatabase()->executeCommand(array(
            'collstats' => $this->getName(),
        ));
    }
}
