<?php
/**
 * i18n操作类
 * 
 * @package framework
 * @date    2014-3-27
 */
class CLanguage {

    /** 获取redis对象 */
    public $_redis;

    /** 获取地区语言 格式 zh en  */
    private $_language = 'zh';

    /** 此数组为二维数组 用于缓存每次请求所需要的语言配置文件 */
    public $_aryData;
    
	/** 支持的语言类型 */
    public $_aryLanguage = array('zh','en');
    
    private $_strRedisLanguageKey = 'language:config:{area}:{filePath}';
    
    /**
     * 初始化
     */
    public function __construct()
	{
        $aryUrl = explode('.', $_SERVER['HTTP_HOST']);
        if((in_array($aryUrl[0], $this -> _aryLanguage)) === true)
            $this->_language = $aryUrl[0];
    }

    /**
     * i18n根据key获取值
     * 
     * @date 2014-03-28
     * @param String $_strKey  key值 格式('文件前缀,数组key')
     * @return String 该key值
     */
    public function i18n( $_strKey = '' ) {
       
        $arykeys = explode(',', $_strKey);
        
        //判断总的数组中是否存在该key数据
        if (empty($this->_aryData[$arykeys[0]])) {
            //根据key获取数据
            $this->_aryData[$arykeys[0]] = $this->getAryByKey($arykeys[0]);
            $aryData = $this->_aryData[$arykeys[0]];
        } else {
            $aryData = $this->_aryData[$arykeys[0]];
        }
        return $aryData[$arykeys[1]];
    }

    /**
     * 获得语言缓存/加载语言文件
     * 
     * @date 2014-3-28
     * @param String $_strKey  配置文件前缀
     * @return type
     */
    public function getAryByKey( $_strKey = '' ) {
        $language = $this->_language;
        $strFilePath = WEB_ROOT
                . "/protected/config/language/{$language}/{$_strKey}.config.php";

        //是否允许缓存
        if (CACHE_STATUS === true) {
            //检查缓存中是否存在值
            $redis = $this->getRedis();
            
            $strRedisLanguageKey = str_replace('{area}', $language , str_replace('{filePath}', $_strKey, $this -> _strRedisLanguageKey));
            $configval = $redis->readByKey($strRedisLanguageKey);
            
            //如果不存在值则去读取配置文件并存入缓存
            if (empty($configval)) {
                $aryData = $this -> getAryByFile($strFilePath);
                $redis->writeByKey($strRedisLanguageKey, json_encode($aryData));
                return $aryData;
            } else {
                return json_decode($configval, TRUE);
            }
        } else {
            return $this -> getAryByFile($strFilePath);
        }
    }

    /**
     * 根据文件路径获取指定config文件中的array
     * 
     * @param type $_file 文件路径
	 *
     * @date 2014-3-28
     * @return array
     */
    public function getAryByFile($_file = '') {
        if (file_exists($_file))
            return require($_file);
        else{
            switch ($this->_language)
            {
                case 'zh':
                    throw new CModelException("文件地址不存在");
                    break;
                case 'en':
                    throw new CModelException("file not exists");
                    break;
                default :
                    throw new CModelException("file not exists");
            }
        }
    }

    /**
     * get redis connection
     */
    public function getRedis() {
        if (empty($this->_redis))
        {
            $this->_redis = new CRedis();
            $this -> _redis -> setPrefix('redis.string.');
        }
        return $this->_redis;
    }
    
    /**
     * language的get方法
	 *
     * @return String 当前语言设置
     * @date 2014-4-4
     */
    public function getLanguage()
    {
        return $this -> _language;
    }
}
