<?php

/**
 * Redis SortedSet 存储类
 *
 * @package framework
 * @date 2014-10-21
 *
 * SortedSet 为有序队列
 */
class CRedisSortedSet extends CRedis 
{
	/** Redis SortedSet 中数据的位置，链表头或链表尾 */
	const REDIS_SORTEDSET_FRONT = 'front';
	const REDIS_SORTEDSET_END = 'end';

	/** Redis SortedSet 排序方式 */
	const REDIS_SORTEDSET_SORT_INCREASING = 1;
	const REDIS_SORTEDSET_SORT_DIMINISHING = -1;

	/** 返回数据是否包含 Score 值 */
	const REDIS_SORTEDSET_WITHSCORES = 'withscores';

	/**
	 * 初始化
	 */
	public function init( $_prefix = 'redis.sortedset.' , $_suffix = '' )
	{
		parent::init( $_prefix , $_suffix );
	}

	/**
	 * 将一个member元素及其score值加入到有序集key当中,或者对数据重置该member元素值
	 *
	 * @param string $_key		给定的 redis key
	 * @param string $_member	给定的 member 元素
	 * @param string $_key		给定的 score 值
	 * @return boolean
	 *
	 * @date 2014-10-21
	 */
	public function zadd( $_key = '' , $_member = '' ,$_score = '' )
	{
		// 进行数据添加或者重置该元素值
		if ( self::getConnection() )
			$addResult = self::getConnection()->zadd( $this->calculateKey( $_key ) , $_score , $_member );

		return $addResult > 0 ? true : false;
	}

	/**
	 * 移除有序集key中的一个成员，不存在的成员将被忽略。
	 *
	 * @param string $_key		给定的 redis key
	 * @param string $_member	给定的 member 元素
	 * @return boolean
	 *
	 * @date 2014-10-21
	 */
	public function zrem( $_key = '' , $_member = '' )
	{
		// 移除该元素的值
		if ( self::getConnection() )
			$removeResult = self::getConnection()->zrem( $this->calculateKey( $_key ) , $_member );

		return $removeResult > 0 ? true : false;
	}

	/**
	 * 返回有序集key的基数
	 *
	 * @param string $_key		给定的 redis key
	 * @return int 基数
	 *
	 * @date 2014-10-21
	 */
	public function zcard( $_key = '' )
	{
		// 获取有序集基数
		if ( self::getConnection() )
			$returnData = self::getConnection()->zcard( $this->calculateKey( $_key ) );

		return $returnData;
	}

	/**
	 * 返回有序集key中，
	 * score值在min和max之间(默认包括score值等于min或max)的成员。
	 *
	 * @param string $_key		给定的 redis key
	 * @param int min			最小值(-inf)
	 * @param int max			最大值(+inf)
	 * @return int 总数
	 *
	 * @date 2014-10-21
	 */
	public function zcount( $_key = '' , $_min = 0 , $_max = 0 )
	{
		// 获取有序集中一定范围内数据
		if ( self::getConnection() )
			$returnData = self::getConnection()->zcount( $this->calculateKey( $_key ) , $_min , $_max );

		return $returnData;
	}

	/**
	 * 返回有序集key中，成员member的score值
	 *
	 * @param string $_key		给定的 redis key
	 * @param string $_member	member 元素
	 * @return string
	 *
	 * @date 2014-10-21
	 */
	public function zscore( $_key = '' , $_member = '' )
	{
		// 根据某个成员获取数据值
		if ( self::getConnection() )
			$returnData = self::getConnection()->zscore( $this->calculateKey( $_key ) , $_member );

		return $returnData;
	}

	/**
	 * 为有序集key的成员member的score值加上增量increment。
	 *
	 * @param string $_key		给定的 redis key
	 * @param string $_member	member 元素
	 * @param string $_increment	需要增加的值
	 * @return int 增加后的值
	 *
	 * @date 2014-10-21
	 */
	public function zincrby( $_key = '' , $_member = '' , $_increment = 0 )
	{
		// 进行数据增加
		if ( self::getConnection() )
			$returnData = self::getConnection()->zincrby( $this->calculateKey( $_key ) , $_increment , $_member );

		return $returnData;
	}

	/**
	 * 返回有序集key中，指定区间内的成员,
	 * 其中成员的位置 根据$_sort参数 按score值递增(从小到大) 或 递减(从大到小) 来排序
	 *
	 * @param string $_key		给定的 redis key
	 * @param string $_where	数据的位置，默认为链表尾端
	 * @param int $_count		获得制定数量数据
	 * @param string $_score	是否需要sorce值
	 * @param int $_sort		排序方式，1 - 递增（默认），-1 - 递减
	 * @return array 
	 *
	 * @date 2014-10-21
	 */
	public function zrange(
			$_key = '' ,
			$_where = self::REDIS_SORTEDSET_FRONT ,
			$_count = 20 ,
			$_score = '' ,
			$_sort = self::REDIS_SORTEDSET_SORT_INCREASING
		)
	{
		$key = $this->calculateKey( $_key );

		// 获得索引位置
		$intIndex = $_where == self::REDIS_SORTEDSET_FRONT ? 1 : -1;

		// 获得起始位置
		$intStart = $intIndex > 0 ? 0 : -1;
		$intEnd = $intIndex > 0 && $_count != -1 ? $_count-1 : $intIndex*$_count;
		$returnData = array();
		if ( self::getConnection() )
		{
			// 如果需要score值
			if( $_score === self::REDIS_SORTEDSET_WITHSCORES )
			{
				// 如果按递增排序
				if ( $_sort === self::REDIS_SORTEDSET_SORT_INCREASING )
					$returnData = self::getConnection()->zrange( $key , $intStart , $intEnd , true );
				else
					$returnData = self::getConnection()->zrevrange( $key , $intStart , $intEnd , true );
			}
			else
			{
				if ( $_sort === self::REDIS_SORTEDSET_SORT_INCREASING )
					$returnData = self::getConnection()->zrange( $key , $intStart , $intEnd );
				else
					$returnData = self::getConnection()->zrevrange( $key , $intStart , $intEnd );
			}
		}

		return $returnData;
	}

	/**
	 * 返回有序集key中成员member的排名。
	 * 根据$_sort参数 按score值递增(从小到大) 或 递减(从大到小) 来排序
	 *
	 * @param string $_key		给定的 redis key
	 * @param string $_member	给定的 member 元素
	 * @param int $_sort		排序方式，1 - 递增（默认），-1 - 递减
	 * @return int
	 *
	 * @date 2014-10-21
	 */
	public function zrank( $_key = '' , $_member = '' , $_sort = self::REDIS_SORTEDSET_SORT_INCREASING )
	{
		// 获取某个成员再集合中的排名
		if ( self::getConnection() )
		{
			if ( $_sort === self::REDIS_SORTEDSET_SORT_INCREASING )
				$returnData = self::getConnection()->zrank( $this->calculateKey( $_key ) , $_member );
			else
				$returnData = self::getConnection()->zrevrank( $this->calculateKey( $_key ) , $_member );
		}

		return $returnData;
	}

	/**
	 * 根据 score 值范围获得数据
	 *
	 * @param string $_key		给定的 redis key
	 * @param int min			最小值(-inf)
	 * @param int max			最大值(+inf)
	 * @param string $_score	是否需要score值
	 * @param int $_sort		排序方式，1 - 递增（默认），-1 - 递减
	 * @return array
	 *
	 * @author wengebin
	 * @date 2014-10-22
	 */
	public function getRangeByScore( 
			$_key = '' , 
			$_min = 0 , 
			$_max = 0 , 
			$_score = '' , 
			$_sort = self::REDIS_SORTEDSET_SORT_INCREASING
		)
	{
		// 根据某个成员获取数据值
		if ( self::getConnection() )
		{
			// 如果需要score值
			if( $_score === self::REDIS_SORTEDSET_WITHSCORES )
			{
				// 如果按递增排序
				if ( $_sort === self::REDIS_SORTEDSET_SORT_INCREASING )
					$returnData = self::getConnection()->zRangeByScore( $this->calculateKey( $_key ) , $_min , $_max , array( self::REDIS_SORTEDSET_WITHSCORES=>true ) );
				else
					$returnData = self::getConnection()->zRevRangeByScore( $this->calculateKey( $_key ) , $_min , $_max , array( self::REDIS_SORTEDSET_WITHSCORES=>true ) );
			}
			else
			{
				if ( $_sort === self::REDIS_SORTEDSET_SORT_INCREASING )
					$returnData = self::getConnection()->zRangeByScore( $this->calculateKey( $_key ) , $_min , $_max );
				else
					$returnData = self::getConnection()->zRevRangeByScore( $this->calculateKey( $_key ) , $_min , $_max );
			}
		}

		return $returnData;
	}

	/**
	 * 根据 score 值范围删除数据
	 *
	 * @param string $_key		给定的 redis key
	 * @param int min			最小值(-inf)
	 * @param int max			最大值(+inf)
	 * @return array
	 *
	 * @author wengebin
	 * @date 2014-10-22
	 */
	public function removeRangeByScore( $_key = '' , $_min = 0 , $_max = 0 )
	{
		// 根据某个成员获取数据值
		if ( self::getConnection() )
			$returnData = self::getConnection()->zRemRangeByScore( $this->calculateKey( $_key ) , $_min , $_max );

		return $returnData;
	}

//end class	
}
