<?php
/**
 * 调用API静态方法
 * 
 * @author wengebin
 * @date 2014-04-16
 */
class UtilApi
{
	/**
	 * 调用API
	 *
	 * @param string $_strParam 参数
	 * @return array
	 * 			<pre>
	 * 					return array( 'ISOK'=>1,'DATA'=>array(),'ERROR'=>'错误号' );
	 * 			</pre>
	 */
	public static function callMethod( $_strParam = '' )
	{
		$aryData = array();

		// cur version
		$aryData['param'] = $_strParam;

		return CApi::callApi( MAIN_DOMAIN."/dosomething" , $aryData , MAIN_DOMAIN_KEY , true );
	}

//end class
}
