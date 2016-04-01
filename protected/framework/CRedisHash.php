<?php
/**
 * Redis hash 存储类
 *
 * @author wengebin
 * @package framework
 * @date 2013-11-1
 *
 * 
 */
class CRedisHash extends CRedis 
{
	/**
	 * 初始化
	 */
	public function init( $_prefix = 'redis.hash.' , $_suffix = '' )
	{
		parent::init( $_prefix , $_suffix );
	}

	/**
	 * 根据 key 读取一个 value
	 * List 中没有根据 key 获得值的方法
	 *
	 * @param string $_key	给定的 redis key
	 * @return bool
	 */
	public function readByKey( $_key = '' )
	{
		return false;
	}

	/**
	 * 根据 key 存储一个给定的 value
	 * List 中没有根据 key 获得值的方法
	 *
	 * @param string $_key		给定的 redis key
	 * @param string $_value	给定的岁应 key 的 value 值
	 * @return bool
	 */
	public function writeByKey( $_key = '' , $_value = '' )
	{
		return false;
	}

	/**
	 * 设置值
	 *
	 * @param string $_key		给定的 redis key
	 * @param string $_field	给定的 hash 域，获取对应域值
	 * @param string $_value	给定的对应 field 的 value 值
	 * @return bool
	 */
	public function set( $_key = '' , $_field = '' , $_value = '' )
	{
		$key = $this->calculateKey( $_key );

		// 存储数据
		if ( self::getConnection() )
		{
			$setResult = self::getConnection()->hSet( $key , $_field , $_value );
			
			// 设置过期时间
			$this->setTimeoutByKey( $key );
		}

		return $setResult >= 0 ? true : false;
	}

	/**
	 * 获得值
	 *
	 * @param string $_key		给定的 redis key
	 * @param string $_field	给定的 hash 域，获取对应域值
	 * @return string
	 */
	public function get( $_key = '' , $_field = '' )
	{
		$key = $this->calculateKey( $_key );

		// 获得数据
		if ( self::getConnection() )
			$returnData = self::getConnection()->hGet( $key , $_field );

		return $returnData;
	}

	/**
	 * 批量设置值
	 *
	 * @param string $_key		给定的 redis key
	 * @param array $_arySet	需要存储的数据集，key=>value 结构
	 * @return bool
	 */
	public function setMap( $_key = '' , $_arySet = array() )
	{
		$key = $this->calculateKey( $_key );

		// 存储数据
		if ( self::getConnection() )
		{
			$setResult = self::getConnection()->hMSet( $key , $_arySet );

			// 设置过期时间
			$this->setTimeoutByKey( $key );
		}

		return !empty($setResult) ? true : false;
	}

	/**
	 * 获得值
	 *
	 * @param string $_key		给定的 redis key
	 * @param string $_aryFields	需要取值的域
	 * @return array
	 */
	public function getMap( $_key = '' , $_aryFields = array() )
	{
		$key = $this->calculateKey( $_key );

		// 获得数据
		if ( self::getConnection() )
			$returnData = self::getConnection()->hMGet( $key , $_aryFields );

		return $returnData;
	}

	/**
	 * 从 Hash 表中删除一个域
	 *
	 * @param string $_key		给定的 redis key
	 * @param string $_field	给定的 hash 域，获取对应域值
	 * @return bool
	 */
	public function remove( $_key = '' , $_field = '' )
	{
		$key = $this->calculateKey( $_key );

		// 从 Hash 表中删除一个域
		if ( self::getConnection() )
			$removeResult = self::getConnection()->hDel( $key , $_field );

		return $removeResult > 0 ? true : false;
	}

	/**
	 * 判断key是否存在
	 *
	 * @param string $_key		给定的 redis key
	 * @return bool
	 */
	public function keyExists( $_key = '' )
	{
		$key = $this->calculateKey( $_key );

		$isExists = false;
		// 判断key是否存在
		if ( self::getConnection() )
			$isExists = self::getConnection()->exists( $key );

		return $isExists;
	}

	/**
	 * 判断hash field是否存在
	 *
	 * @param string $_key		给定的 redis key
	 * @param string $_field	给定的 hash field
	 * @return bool
	 */
	public function fieldExists( $_key = '' , $_field = '' )
	{
		$key = $this->calculateKey( $_key );

		$isExists = false;
		// 判断field是否存在
		if ( self::getConnection() )
			$isExists = self::getConnection()->hExists( $key , $_field );

		return $isExists;
	}

	/**
	 * 根据匹配字符串获得对应的 key 集合
	 *
	 * @param string $_key		给定的 redis key
	 * @return string
	 */
	public function getKeys( $_key = '' )
	{
		if ( self::getConnection() )
			$returnData = self::getConnection()->hKeys( $this->calculateKey( $_key ) );

		return $returnData;
	}

	/**
	 * 获得HASH列表长度
	 *
	 * @param string $_key		给定的 redis key
	 * @return string
	 */
	public function getLength( $_key = '' )
	{
		if ( self::getConnection() )
			$returnData = self::getConnection()->hLen( $this->calculateKey( $_key ) );

		return $returnData;
	}

	/**
	 * 根据匹配字符串获得对应的 key 集合
	 *
	 * @param string $_matchStr 匹配的字符串
	 * @return string
	 */
	public function getSourceKeys( $_key = '*' )
	{
		return parent::getKeys( $_key );
	}

	/**
	 * 获得所有键值
	 *
	 * @param string $_key		给定的 redis key
	 * @return array
	 */
	public function getAll( $_key = '' )
	{
		if ( self::getConnection() )
			$returnData = self::getConnection()->hGetAll( $this->calculateKey( $_key ) );

		return $returnData;
	}

	/**
	 * 获得所有值
	 *
	 * @param string $_key		给定的 redis key
	 * @return array
	 */
	public function getAllValues( $_key = '' )
	{
		if ( self::getConnection() )
			$returnData = self::getConnection()->hVals( $this->calculateKey( $_key ) );

		return $returnData;
	}

//end class	
}
