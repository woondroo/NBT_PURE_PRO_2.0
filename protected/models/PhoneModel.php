<?php
/**
 * 短信发送
 *
 * @author wengebin
 * @date 2015-02-28
 */
class PhoneModel extends CModel 
{
	/**
	 * 短信操作
	 * 
	 * @var CMailer
	 */
	private $_phone = null;
	
	/**
	 * 初始化
	 */
	public function init()
	{
		parent::init();

		// 初始化 CPhone 类
		$this->_phone = new CPhone();
	}
	
	/**
	 * 返回惟一实例
	 *
	 * @return NewsModel
	 */
	public static function model()
	{
		return parent::model( __CLASS__ );
	}
	
	/**
	 * 获得发送器
	 *
	 * @return CPhone
	 */
	public function getSender()
	{
		// 如果缓存对象为空
		if ( empty( $this->_phone ) )
			$this->_phone = new CPhone();

		return $this->_phone;
	}

	/**
	 * 发送验证码
	 *
	 * @param string $_strPhone 邮箱地址
	 * @param string $_strCode 验证码
	 * @return boolean
	 */
	public function sendBindMessage( $_strPhone = '' , $_strCode = '' )
	{
		// 手机绑定验证码发送模板
		$strContentFile = NBT_APPLICATION_PATH.'/framework/phone/templates/bind_code.txt';
		if ( empty( $_strPhone ) || !file_exists( $strContentFile ) )
			throw new CModelException(CUtil::i18n('exception,exec_message_notBeSent'));

		// 填写确认函
		$content = file_get_contents( $strContentFile );
		$content = str_replace( '---bind_code---' , $_strCode , $content );

		// 发送邮件
		$mailer = $this->getSender();
		return $mailer->sendCode( $content , $_strPhone );
	}

//end class
}
