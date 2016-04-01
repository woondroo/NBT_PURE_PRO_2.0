<?php
/**
 * CArray class file.
 * 
 * @author wengebin
 * @package framework
 * @date 2014-02-19
 */
class CArray
{
	/**
	 * 数组获得前10位数量最多的数据
	 *
	 * @param array $_aryData 排名数组
	 * @param int $_intRank 排名数量
	 * @param boolean $_isAll 是否显示全部
	 * @return array
	 */
	public static function sortArrayRank( $_aryData = array() , $_intRank = 10 , $_isAll = false )
	{
		if ( empty( $_aryData ) )
			return array();

		// 先进行排序
		arsort( $_aryData );

		// 进行整合
		$aryRank = array();
		$intOthers = 0;
		$intCur = 0;
		$intCount = 0;
		foreach ( $_aryData as $key=>$val )
		{
			preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', trim( $key , '"' ) , $match);
			if ( $_isAll === false && ( $intCur >= $_intRank-1 || !empty( $match[0] ) ) )
			{
				$intOthers += $val;
			}
			else
			{
				$aryRank[$key] = $val;
				$intCur++;
			}

			$intCount += $val;
		}

		if ( $_isAll === false )
			$aryRank['"其他"'] = $intOthers;
		else
			$aryRank['"总共"'] = $intCount;
		return $aryRank;
	}
// end class
}
