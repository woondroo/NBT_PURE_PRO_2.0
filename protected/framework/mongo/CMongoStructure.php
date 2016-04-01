<?php
class CMongoStructure
{
    protected $_data = array();
    
    protected $_originalData = array();
    
    protected $_modifiedFields = array();
    
    public function reset()
    {
        $this->_data = $this->_originalData;
        $this->_modifiedFields = array();
        
        return $this;
    }
    
    public function __get($name)
    {
        return isset($this->_data[$name]) ? $this->_data[$name] : null;
    }
    
    public function get($selector)
    {
        if(false === strpos($selector, '.')) {
            return isset($this->_data[$selector]) ? $this->_data[$selector] : null;
        }

        $value = $this->_data;
        foreach(explode('.', $selector) as $field)
        {
            if(!isset($value[$field])) {
                return null;
            }

            $value = $value[$field];
        }

        return $value;
    }
    
    /**
     * Get structure object from adocument's value
     * 
     * @param string $selector
     * @param string|closure $className string classname or closure, which accept data and return string class name
     * @return object representation of document with class, passed as argument
     * @throws Exception
     */
    public function getObject($selector, $className)
    {
        $data = $this->get($selector);
        if(!$data) {
            return null;
        }
        
        // get classname from callable
        if(is_callable($className)) {
            $className = $className($data);
        }
        
        // prepare structure
        $structure =  new $className();
        if(!($structure instanceof CMongoStructure)) {
            throw new Exception('Wring structure class specified');
        }
        
        return clone $structure->merge($data);
    }
    
    /**
     * Get list of structure objects from list of values in mongo document
     * 
     * @param string $selector
     * @param string|closure $className string classname or closure, which accept data and return string class name
     * @return object representation of document with class, passed as argument
     * @throws Exception
     */
    public function getObjectList($selector, $className)
    {
        $data = $this->get($selector);
        if(!$data) {
            return array();
        }
        
        // classname is string
        if(is_string($className)) {
            
            $structure = new $className();
            if(!($structure instanceof CMongoStructure)) {
                throw new Exception('Wring structure class specified');
            }

            return array_map(function($dataItem) use($structure) {
                return clone $structure->merge($dataItem);
            }, $data);
        }
        
        // classname id callable
        if(is_callable($className)) {
            
            $structurePool = array();

            return array_map(function($dataItem) use($structurePool, $className) {
                
                $classNameString = $className($dataItem);
                if(empty($structurePool[$classNameString])) {
                    $structurePool[$classNameString] = new $classNameString;
                    if(!($structurePool[$classNameString] instanceof CMongoStructure)) {
                        throw new Exception('Wring structure class specified');
                    }
                }
                
                return clone $structurePool[$classNameString]->merge($dataItem);
            }, $data);
        }
        
        throw new Exception('Wrong class name specified. Use string or closure');
    }
    
    /**
     * Handle setting params through public property
     * 
     * @param type $name
     * @param type $value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }
    
    /**
     * Store value to specified selector in local cache
     * 
     * @param type $selector
     * @param type $value
     * @return CMongoDocument
     * @throws Exception
     */
    public function set($selector, $value)
    {
        $value = $this->_prepareValue($value);

        // modify
        $arraySelector = explode('.', $selector);
        $chunksNum = count($arraySelector);
        
        // optimize one-level selector search
        if(1 == $chunksNum) {
            
            // update only if new value different from current
            if(!isset($this->_data[$selector]) || $this->_data[$selector] !== $value) {
                // modify
                $this->_data[$selector] = $value;
                // mark field as modified
                $this->_modifiedFields[] = $selector;
            }
        
            return $this;
        }
        
        // selector is nested
        $section = &$this->_data;

        for($i = 0; $i < $chunksNum - 1; $i++) {

            $field = $arraySelector[$i];

            if(!isset($section[$field])) {
                $section[$field] = array();
            }
            elseif(!is_array($section[$field])) {
                throw new Exception('Assigning subdocument to scalar value');
            }

            $section = &$section[$field];
        }
        
        // update only if new value different from current
        if(!isset($section[$arraySelector[$chunksNum - 1]]) || $section[$arraySelector[$chunksNum - 1]] !== $value) {
            // modify
            $section[$arraySelector[$chunksNum - 1]] = $value;
            // mark field as modified
            $this->_modifiedFields[] = $selector;
        }
        
        return $this;
    }
    
    public function has($selector)
    {
        $pointer = &$this->_data;
        
        foreach(explode('.', $selector) as $field) {
            if(!array_key_exists($field, $pointer)) {
                return false;
            }
            
            $pointer = &$pointer[$field];
        }
        
        return true;
    }
    
    private function _prepareValue($value)
    {
        if(is_array($value)) {
            foreach($value as $k => $v) {
                $value[$k] = $this->_prepareValue($v);
            }
        }
        
        // convert objects to arrays except internal mongo types
        elseif(is_object($value)) {
            if(!in_array(get_class($value), array('MongoId', 'MongoCode', 'MongoDate', 'MongoRegex', 'MongoBinData', 'MongoInt32', 'MongoInt64', 'MongoDBRef', 'MongoMinKey', 'MongoMaxKey', 'MongoTimestamp'))) {
                $value = (array) $value;
            }
        }
        
        return $value;
    }
    
    public function unsetField($selector)
    {
        // modify
        $arraySelector = explode('.', $selector);
        $chunksNum = count($arraySelector);
        
        // optimize one-level selector search
        if(1 == $chunksNum) {
            // check if field exists
            if(isset($this->_data[$selector])) {
                // unset field
                unset($this->_data[$selector]);
                // mark field as modified
                $this->_modifiedFields[] = $selector;
            }
            
            return $this;
        }
        
        // find section
        $section = &$this->_data;

        for($i = 0; $i < $chunksNum - 1; $i++) {

            $field = $arraySelector[$i];

            if(!isset($section[$field])) {
                return $this;
            }

            $section = &$section[$field];
        }
        
        // check if field exists
        if(isset($section[$arraySelector[$chunksNum - 1]])) {
            // unset field
            unset($section[$arraySelector[$chunksNum - 1]]);
            // mark field as modified
            $this->_modifiedFields[] = $selector;
        }
        
        return $this;
    }
    
    public function append($selector, $value)
    {
        $oldValue = $this->get($selector);
        if($oldValue) {
            if(!is_array($oldValue)) {
                $oldValue = (array) $oldValue;
            }
            $oldValue[] = $value;
            $value = $oldValue;
        }
        
        $this->set($selector, $value);
        return $this;
    }
    
    public function isModified($selector = null)
    {
        if(!$this->_modifiedFields) {
            return false;
        }
        
        if(!$selector) {
            return (bool) $this->_modifiedFields;
        }
        
        foreach($this->_modifiedFields as $modifiedField) {
            if(preg_match('/^' . $selector . '($|.)/', $modifiedField)) {
                return true;
            }
        }
        
        return false;
    }
    
    public function getModifiedFields()
    {
        return $this->_modifiedFields;
    }
        
    public function toArray()
    {
        return $this->_data;
    }
    
    
    /**
     * Recursive function to merge data without setting modification mark
     * 
     * @param type $target
     * @param type $source
     */
    private function _mergeUnmodified(&$target, $source) 
    {
        foreach($source as $key => $value) {
            if(is_array($value) && isset($target[$key])) {
                $this->_merge($target[$key], $value);
            }
            else {
                $target[$key] = $value;
            }
        }
    }
    
    /**
     * Merge array to current structure
     * 
     * @param array $data
     * @return CMongoStructure
     */
    public function mergeUnmodified(array $data)
    {
        $this->_mergeUnmodified($this->_data, $data);
        $this->_mergeUnmodified($this->_originalData, $data);
        
        return $this;
    }
    
    /**
     * Recursive function to merge data with setting modification mark
     * 
     * @param type $target
     * @param type $source
     */
    private function _merge(&$target, $source, $prefix = null) 
    {
        foreach($source as $key => $value) {
            if(is_array($value) && isset($target[$key])) {
                $this->_merge($target[$key], $value, $prefix . $key . '.');
            }
            else {
                $target[$key] = $value;
                $this->_modifiedFields[] = $prefix . $key;
            }
        }
    }
    
    /**
     * Merge array to current structure
     * 
     * @param array $data
     * @return CMongoStructure
     */
    public function merge(array $data)
    {
        $this->_merge($this->_data, $data);
        return $this;
    }
    
    public function load(array $data, $modified = true)
    {
        if($modified) {
            $this->merge($data);
        } else {
            $this->mergeUnmodified($data);
        }
        
        return $this;
    }
}
