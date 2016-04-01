<?php
class UtilUrl
{
	// 配置
	private static $_aryConfigRouteReWrite = array(
						''=>'index/index',
						'logout'=>'login/logout',
						'check'=>'login/check',
						'captcha'=>'captcha/index',
						// login & regist
						'login'=>'login/index',
						'regist'=>'regist/index',
						// member
						'ucenter'=>'memberInfo/index',
						'u<action:\w+>'=>'memberInfo/<action>',
					);

	/**
	 * 获得配置
	 */
	public static function getConfig()
	{
		return self::$_aryConfigRouteReWrite;
	}

//end class
}
