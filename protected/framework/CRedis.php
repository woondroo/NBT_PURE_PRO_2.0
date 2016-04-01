<?php
/**
 * Redis 存储类
 *
 * @author wengebin
 * @package framework
 * @date 2013-09-28
 *
 * 使用：
 * $redis = new CRedis();
 * $redis->writeByKey('key1', 'val1');
 * echo $redis->readByKey('key1');
 */
class CRedis extends CApplicationComponents 
{
	/**
	 * Redis key 前缀
	 * @var string
	 */
	private $_keyPrefix = "redis.string.";

	/**
	 * Redis key 后缀
	 * @var string
	 */
	private $_keySuffix = "";

	/**
	 * Redis 连接
	 * @var CRedis
	 */
	private static $_connection;

	/**
	 * Redis 值生存时间
	 * @var int
	 */
	private $_timeout = 0;
	
	/**
	 * Redis 存储是否需要设置域
	 * 默认 1 - 自动设置前缀， 0 - 无需前缀
	 *
	 * @var int
	 */
	private $_needDistrict = 1;

	/**
	 * 初始化
	 */
	public function init( $_prefix = '' , $_suffix = '' )
	{
		self::getConnection();
		parent::init();

		// 设置前缀
		$this->setPrefix( $_prefix );
		// 设置后缀
		$this->setSuffix( $_suffix );
	}

	/**
	 * 根据 key 读取一个 value
	 *
	 * @param string $_id	指定的Redis key
	 * @return bool
	 */
	public function readByKey( $_key = '' )
	{
		if ( self::getConnection() )
			$data = self::getConnection()->get( $this->calculateKey( $_key ) );

		return $data === false ? '' : $data;
	}

	/**
	 * 根据 key 存储一个给定的 value
	 *
	 * @param string $_key		给定的 redis key
	 * @param string $_value	给定的岁应 key 的 value 值
	 * @return bool
	 */
	public function writeByKey( $_key = '' , $_value = '' )
	{
		$key = $this->calculateKey( $_key );
		if ( self::getConnection() )
			$writeResult = self::getConnection()->set( $key , $_value );

		// 设置过期时间
		$this->setTimeoutByKey( $key );

		return $writeResult;
	}

	/**
	 * 根据 key 删除
	 *
	 * @param string $_key	给定的需要删除的 redis key
	 * @return bool
	 */
	public function deleteByKey( $_key = '' )
	{
		if ( self::getConnection() )
			$deleteCount = self::getConnection()->delete( $this->calculateKey( $_key ) );

		return $deleteCount > 0 ? true : false;
	}

	/**
	 * 为 KEY 重命名
	 *
	 * @param string $_strSourceKey		旧KEY
	 * @param string $_strNewKey		新KEY
	 * @return bool
	 */
	public function renameKey( $_strSourceKey = '' , $_strNewKey = '' )
	{
		$strSourceKey = $this->calculateKey( $_strSourceKey );
		$strNewKey = $this->calculateKey( $_strNewKey );
		if ( self::getConnection() )
			$result = self::getConnection()->rename( $strSourceKey , $strNewKey );

		return $result;
	}

	/**
	 * 根据匹配字符串获得对应的 key 集合
	 *
	 * @param string $_matchStr 匹配的字符串
	 * @return string
	 */
	public function getKeys( $_matchStr = '*' )
	{
		if ( self::getConnection() )
			$returnData = self::getConnection()->keys( $this->calculateKey( $_matchStr ) );

		return $returnData;
	}

	/**
	 * 手动设置 Redis 连接
	 *
	 * @param Redis $_connection	新连接
	 */
	public function setConnection( $_connection )
	{
		self::$_connection = $_connection;
		return true;
	}

	/**
	 * 获得 Redis 连接
	 *
	 * @return Redis
	 */
	public static function getConnection()
	{
		if ( self::$_connection === null && CACHE_STATUS === true )
		{
			$redis = new Redis();
			$aryPort = explode( ',' , REDIS_CONNECT_PORT );
			$strPort = $aryPort[ array_rand( $aryPort ) ];
			$redis->pconnect( REDIS_CONNECT_ADD , $strPort );
			self::$_connection = $redis;
		}
		return self::$_connection;
	}

	/**
	 * 根据给定的 key 计算出新 key
	 *
	 * @param string $_key	给定的 Redis key，通过与指定字符串合并计算得到一个新 key
	 * @return string
	 */
	public function calculateKey( $_key = '' )
	{
		if( empty( $_key ) )
			throw new CModelException(CUtil::i18n('exception,exec_data_noDataBeModified'));

		return ( $this->_needDistrict === 1 ? REDIS_DISTRICT_NAME.'.' : '' ).$this->_keyPrefix.$_key.$this->_keySuffix;
	}

	/**
	 * 清理 key，获得 key 的原始值
	 *
	 * @param string $_key	给定的 Redis key
	 * @return string
	 */
	public function clearCalculateKey( $_key = '' )
	{
		$key = $_key;
		if ( $this->_needDistrict === 1 ) $key = str_replace( REDIS_DISTRICT_NAME.'.' , '' , $key );
		if ( !empty( $this->_keyPrefix ) ) $key = str_replace( $this->_keyPrefix , '' , $key );
		if ( !empty( $this->_keySuffix ) ) $key = str_replace( $this->_keySuffix , '' , $key );

		return $key;
	}

	/**
	 * 为 key 设置过期时间
	 *
	 * @param string $_key KEY
	 * @return bool
	 */
	public function setTimeoutByKey( $_key = '' )
	{
		if ( $this->getTimeout() > 0 && self::getConnection() )
			self::getConnection()->expire( $_key, $this->getTimeout() );
	}

	/**
	 * 设置过期时间
	 *
	 * @param int $_timeout	过期时长
	 * @return bool
	 */
	public function setTimeout( $_timeout = 0 )
	{
		$this->_timeout = $_timeout;
		return true;
	}

	/**
	 * 获得过期时间
	 *
	 * @return int
	 */
	public function getTimeout()
	{
		return $this->_timeout;
	}

	/**
	 * 获得 key 前缀
	 *
	 * @return string
	 */
	public function getPrefix()
	{
		return $this->_keyPrefix;
	}

	/**
	 * 设置 key 前缀
	 *
	 * @param string $_strPrefix key的前缀字符串
	 * @return bool
	 */
	public function setPrefix( $_strPrefix = '' )
	{
		$this->_keyPrefix = $_strPrefix;
		return true;
	}

	/**
	 * 获得 key 后缀
	 *
	 * @return string
	 */
	public function getSuffix()
	{
		return $this->_keySuffix;
	}

	/**
	 * 设置 key 后缀
	 *
	 * @param string $_strSuffix key的后缀字符串
	 * @return bool
	 */
	public function setSuffix( $_strSuffix = '' )
	{
		$this->_keySuffix = $_strSuffix;
		return true;
	}

	/**
	 * 设置是否需要域前缀
	 *
	 * @param int $_intDistrict key的后缀字符串
	 * @return bool
	 */
	public function setDistrict( $_intDistrict = '' )
	{
		$this->_needDistrict = $_intDistrict;
		return true;
	}

	/**
	 * 保存数据
	 *
	 * @return bool
	 */
	public function saveData( $_intDistrict = '' )
	{
		$data = self::getConnection()->bgSave();
		return $data;
	}
        
//end class
}
