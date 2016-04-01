<?php
/**
 * 登录相关
 * 
 * @author wengebin
 * @date 2013-11-20
 */
class LoginForm extends CModel
{
	/**
	 * user name.
	 * 
	 * @var string
	 */
	public $username = "";
	
	/**
	 * password
	 * 
	 * @var string
	 */
	public $password = "";

	/**
	 * is need captcha
	 * 0 - need not captcha , 1 - need captcha
	 * 
	 * @var int
	 */
	public $iscaptcha = 0;

	/**
	 * set uername
	 * 
	 * @param string $_username
	 */
	public function setUsername( $_username )
	{
		$this->username = $_username;	
	}

	/**
	 * set password
	 * 
	 * @param string $_password
	 */
	public function setPassword( $_password )
	{
		$this->password = $_password;
	}

	/**
	 * set iscaptcha
	 * 
	 * @param int $_iscaptcha
	 */
	public function setIscaptcha( $_iscaptcha )
	{
		$this->iscaptcha = $_iscaptcha; 
	}

	/**
	 * get iscaptcha
	 * 
	 * @return int
	 */
	public function getIscaptcha()
	{
		return $this->iscaptcha;
	}

	/**
	 * 验证规则
	 */
	public function rules()
	{
		return array(
			// 用户登录验证
			array( 'username,password','required','message'=>CUtil::i18n('models,loginForm_notNull'),'on'=>'login' ),
		);
	}

	/**
	 * 字段名配置
	 *
	 * @return array
	 */
	public function fieldCnName()
	{
		return array(
			'username'=> CUtil::i18n('models,loginForm_username'),
			'password'=>CUtil::i18n('models,loginForm_pwd'),
			'captcha'=>CUtil::i18n('models,loginForm_captcha'),
		);
	}

	/**
	 * Login Authenticate
	 * 
	 * @params array $_aryData 用户登录数据
	 * @return bool.
	 */
	public function login( $_aryData = array() )
	{
		//check account
		$aryData = array();
		$aryData['username'] = $_aryData['username'];
		$aryData['password'] = $_aryData['password'];
		$aryData['captcha'] = $_aryData['captcha'];

		$this->setScerian( 'login' );
		$this->setData( $aryData );

		if( !$this->validate( $aryData ) )
			return false;

		// get UserModel
		$modelUser = UserModel::model();
		$aryUserInfo = $modelUser->getUserMessageByUsername( $aryData['username'] );

		//check username
		if( empty( $aryUserInfo ) )
		{
			$this->addError( 'username' , CUtil::i18n('models,loginForm_notExist'));
			return false;
		}

		//check login try
		if( $aryUserInfo['u_retry'] >= 3 )
		{
			if ( $this->_tmpCheckCaptcher( $aryData['captcha'] ) === false ) 
			{
				// need captcha
				$this->setIscaptcha( 1 );
				$this->addError( 'captcha' , CUtil::i18n('models,loginForm_wrong'));
				return false;
			}
		}
		
		//check password
		if( $aryUserInfo['u_password'] != CString::encodeUserPassword( $aryData['password'] ) )
		{
			// need captcha
			if ( $aryUserInfo['u_retry'] >= 2 ) $this->setIscaptcha( 1 );

			// update login try times
			$aryUpdate = array('u_retry'=>(int)$aryUserInfo['u_retry']+1);
			$modelUser->updateById( $aryUserInfo['_id'] , $aryUpdate );

			$this->addError( 'password' , CUtil::i18n('models,loginForm_wrong') );
			return false;
		}

		//check disabled
		/*
		if( $aryUserInfo['u_status'] == CUtil::USER_ACCOUNT_STATUS_DISABLED )
		{
			$this->addError( 'username' , CUtil::i18n('models,loginForm_freezed') );
			return false;
		}
		*/

		//update login try times
		$aryUpdate = array('u_retry'=>0);
		$res = $modelUser->updateById( $aryUserInfo['_id'] , $aryUpdate );

		//set current user's session
		$cUser = Nbt::app()->user;
		$cUser->login( $aryUserInfo );
		$cUser->setUserId( $aryUserInfo['_id'] );
		$cUser->setState( 'name' , $aryUserInfo['u_username'] );
		$cUser->setState( 'status' , $aryUserInfo['u_status'] );

		return true;
	}

	/**
	 * 检查用户名是否需要验证码
	 *
	 * @params string $_strUsername 需要检查的用户名
	 * @return bool
	 */
	public function isNeedCheckCode( $_strUsername = '' )
	{
		// get UserModel
		$modelUser = UserModel::model();
		$aryUserInfo = $modelUser->getUserMessageByUsername( $aryData['username'] );

		// 如果用户不存在，则不需要验证码
		if( empty( $aryUserInfo ) )
			return false;

		// 如果用户密码输入错误超过3次，则需要验证码
		if( $aryUserInfo['u_retry'] >= 3 )
			return true;

		return false;
	}

	/**
	 * 临时检查验证码是否正确
	 *
	 * @params string $_val 输入的验证码
	 * @return bool
	 */
	private function _tmpCheckCaptcher( $_val )
	{
		$objWidgetCaptchaRenderImage = new CWidgetCaptchaRenderImage();
		if( !$objWidgetCaptchaRenderImage->validate( $_val ) )
			return false;

		return true;
	}

// end class
}
