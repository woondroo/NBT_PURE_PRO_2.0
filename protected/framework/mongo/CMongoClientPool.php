<?php
class CMongoClientPool
{
    private $_pool = array();
    
    private $_configuration;
    
    public function __construct(array $configuration)
    {
        $this->_configuration = $configuration;
    }
    
    public function addConnection($name, $dsn, $mapping = null, $defaultDatabase = null)
    {
        $this->_configuration[$name] = array(
            'dsn'               => $dsn,
            'defaultDatabase'   => $defaultDatabase,
            'mappign'           => $mapping,
        );
        
        return $this;
    }
    
    public function __get($name)
    {
        return $this->get($name);
    }
    
    /**
     * Get instance of connection
     * 
     * @param string $name
     * @return CMongoClientPool
     * @throws Exception
     */
    public function get($name)
    {
        // get from cache
        if(isset($this->_pool[$name])) {
            return $this->_pool[$name];
        }
        
        // initialise
        if(!isset($this->_configuration[$name])) {
            throw new Exception('Connection with name ' . $name . ' not found');
        }
        
        $client = new CMongoClient($this->_configuration[$name]['dsn']);
        
        if(isset($this->_configuration[$name]['mapping'])) {
            $client->map($this->_configuration[$name]['mapping']);
        }
        
        if(isset($this->_configuration[$name]['defaultDatabase'])) {
            $client->useDatabase($this->_configuration[$name]['defaultDatabase']);
        }
        
        $this->_pool[$name] = $client;
        
        return $client;
    }
}
