<?php
/**
 * 邮件发送
 *
 * @author wengebin
 * @date 2014-03-15
 */
class MailModel extends CModel 
{
	/**
	 * 邮件操作
	 * 
	 * @var CMailer
	 */
	private $_mailer = null;
	
	/**
	 * 初始化
	 */
	public function init()
	{
		parent::init();

		// 初始化 CMailer 类
		$this->_mailer = new CMailer();
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
	 * 获得邮件发送对象
	 *
	 * @return CMailer
	 */
	public function getMailer()
	{
		// 如果缓存对象为空
		if ( empty( $this->_mailer ) )
			$this->_mailer = new CMailer();

		return $this->_mailer;
	}

	/**
	 * 创建用户邮件确认URL
	 *
	 * @param string $_strType 邮件类型 BIND | RESET | REGIST
	 * @param int $_intUid 用户ID
	 * @param string $_strEmail 用户邮箱
	 */
	public function generateMailUrl( $_strType = '' , $_intUid = 0 , $_strEmail = '' )
	{
		if ( empty( $_strType ) || !in_array( $_strType , array( 'BIND' , 'RESET' , 'REGIST' ) ) )
			throw new CModelException(CUtil::i18n('exception,exec_email_checkMailType'));

		$key = '';
		$timestamp = time();
		$strUri = '';
		if ( $_strType === 'BIND' )
		{
			$key = MAIN_DOMAIN_BIND_MAIL_KEY;
			$strUri = 'bind/confirmMail';
		}
		else if ( $_strType === 'RESET' )
		{
			$key = MAIN_DOMAIN_RESET_MAIL_KEY;
			$strUri = 'reset/confirmReset';
		}
		else if ( $_strType === 'REGIST' )
		{
			$key = MAIN_DOMAIN_REGIST_MAIL_KEY;
			$strUri = 'login/confirmRegist';
		}

		$arySignData = array();
		$arySignData['time'] = $timestamp;
		$arySignData['uid'] = $_intUid;
		$arySignData['email'] = $_strEmail;
		
		// 获得签名
		$strSign = CApi::sign( $arySignData , $key );

		// 获得签名参数
		$arySignUrlParams = array();
		$arySignUrlParams[] = $_intUid;
		$arySignUrlParams[] = $timestamp;
		$arySignUrlParams[] = $strSign;

		// 获得签名参数字串
		$strSignData = urlencode( base64_encode( implode( '|' , $arySignUrlParams ) ) );

		// 获得签名URL
		$strSignUrl = MAIN_DOMAIN.Nbt::app()->createUrl( $strUri , array( 'd'=>$strSignData ) );
		return $strSignUrl;
	}

	/**
	 * 发送邮箱绑定邮件
	 *
	 * @param string $_strEmail 邮箱地址
	 * @param string $_strName 用户名称
	 * @param string $_strUrl 确认地址
	 * @return boolean
	 */
	public function sendBindMail( $_strEmail = '' , $_strName = '' , $_strUrl = '' )
	{
		// 绑定邮件模板位置
		$strContentFile = NBT_APPLICATION_PATH.'/framework/phpmailer/extras/bind.html';
		if ( empty( $_strEmail ) || !file_exists( $strContentFile ) )
			throw new CModelException(CUtil::i18n('exception,exec_email_notBeSent'));

		$strTitle = CUtil::i18n('models,mail_sendBindMail_strTitle');

		// 填写确认函
		$content = file_get_contents( $strContentFile );
		$content = str_replace( '---confirm_name---' , $_strName , $content );
		$content = str_replace( '---confirm_url---' , $_strUrl , $content );

		// 发送邮件
		$mailer = $this->getMailer();
		return $mailer->sendEmail( $_strEmail , $_strName , $strTitle , $content );
	}

	/**
	 * 发送密码充值邮件
	 *
	 * @param string $_strEmail 邮箱地址
	 * @param string $_strName 用户名称
	 * @param string $_strUrl 确认地址
	 * @return boolean
	 */
	public function sendResetMail( $_strEmail = '' , $_strName = '' , $_strUrl = '' )
	{
		// 绑定邮件模板位置
		$strContentFile = NBT_APPLICATION_PATH.'/framework/phpmailer/extras/reset.html';
		if ( empty( $_strEmail ) || !file_exists( $strContentFile ) )
			throw new CModelException( CUtil::i18n('exception,exec_email_notBeSent') );

		$strTitle = CUtil::i18n('models,mail_sendResetMail_strTitle');

		// 填写确认函
		$content = file_get_contents( $strContentFile );
		$content = str_replace( '---confirm_name---' , $_strName , $content );
		$content = str_replace( '---confirm_url---' , $_strUrl , $content );

		// 发送邮件
		$mailer = $this->getMailer();
		return $mailer->sendEmail( $_strEmail , $_strName , $strTitle , $content );
	}

//end class
}
