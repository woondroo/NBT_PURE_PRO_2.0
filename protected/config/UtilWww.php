<?php
class UtilWww
{
	/**
	 * 将传入的UID 进行md5加密 以及取出MD5后的数字
	 *
	 * @param string $_str
	 * @return string
	 *
	 * @author wengebin
	 */
	public static function md5FindNum( $_str = null )
	{
		// 如果为空
		if ( empty( $_str ) )
			return '';

		// MD5加密
		$_str=md5(trim($_str));

		if(empty($_str))
			return '';

		// 过滤的字符
		$aryTemp = array('1','2','3','4','5','6','7','8','9','0');

		$result='';
		$aryString = array();
		$aryString = str_split( $_str );

		// 获得所有数字
		foreach ($aryString as $key=>$val)
		{
			if( in_array( $_str[$key] , $aryTemp ) )
				$result .= $_str[$key];
		}

		return $result;
	}
	
//end class
}
?>
