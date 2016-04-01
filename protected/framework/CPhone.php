<?php
/**
 * 手机短信类封装
 *
 * @author wengebin
 * @package framework
 * @date 2015-02-28
 */
class CPhone extends CApplicationComponents 
{
	/**
	 * 初始化
	 */
	public function init()
	{
		parent::init();
	}

	/**
	 * 发送短信
	 *
	 * @param string $_strContent 短信
	 * @param string $_strPhone 手机号
	 * return bool;
	 */
	public function sendCode( $_strContent = '' , $_strPhone = '' )
	{
		
	}

//end class
}
