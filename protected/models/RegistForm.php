<?php
/**
 * Regist form.
 * 
 * @author wengebin
 * @date 2013-11-22
 */
class RegistForm extends CModel
{
	/**
	 * 验证规则
	 */
	public function rules()
	{
		return array(
			// 用户注册验证
			array( 'username,password','required','message'=>  CUtil::i18n('models,loginForm_notNull'),'on'=>'regist' ),
			array( 'captcha','captcha','message'=>CUtil::i18n('models,registForm_rules_captchaWrong'),on=>'regist' ),
			array( 'username','validateUserName','message'=>CUtil::i18n('models,registForm_rules_nameNotAllowed'),'on'=>'regist' ),
			array( 'password','validatePassword','message'=>CUtil::i18n('models,registForm_rules_pwdNotAllowed'),'on'=>'regist' ),
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
			'username'=>CUtil::i18n('models,loginForm_username'),
			'password'=>CUtil::i18n('models,loginForm_pwd'),
			'captcha'=>CUtil::i18n('models,loginForm_captcha'),
		);
	}

	/**
	 * 验证用户名
	 *
	 * @param string $_strField	字段名
	 * @param array $_aryRuleParams	其他参数
	 */
	public function validateUserName( $_strField = null , $_aryRuleParams = null )
	{
		$username =  $this->getData( $_strField );
		if ( preg_match( '/^[a-zA-Z0-9_@\.]{3,30}$/' , $username ) )
			return true;

		return false;
	}

	/**
	 * 验证密码
	 *
	 * @param string $_strField	字段名
	 * @param array $_aryRuleParams	参数
	 */
	public function validatePassword( $_strField = null , $_aryRuleParams = null )
	{
		$password =  $this->getData( $_strField );
		if ( preg_match( '/^\S{6,20}$/' , $password ) )
			return true;

		return false;
	}
	
	/**
	 * 用户注册
	 * 
	 * @params array $_aryData 用户注册数据
	 * @return bool.
	 */
	public function regist( $_aryData = array() )
	{
		// 绑定数据
		$aryData = array();
		$aryData['username'] = $_aryData['username'];
		$aryData['password'] = $_aryData['password'];
		$aryData['captcha'] = $_aryData['captcha'];

		$this->setScerian( 'regist' );
		$this->setData( $aryData );

		// 验证注册数据
		if( !$this->validate( $aryData ) )
			return false;

		// 获得用户数据模型
		$modelUser = UserModel::model();

		// 检查用户名是否被占用
		$aryUserInfo = $modelUser->getUserMessageByUsername( $aryData['username'] );

		// 用户名如果已被使用
		if( !empty( $aryUserInfo ) )
		{
			$this->addError( 'username' ,  CUtil::i18n('models,registForm_regist_registError') );
			return false;
		}

		$storeData = array();
		$storeData['u_username'] = $aryData['username'];
		$storeData['u_password'] = CString::encodeUserPassword( $aryData['password'] );
		// 从COOKIE中获取推广员ID
		$storeData['u_aid'] = intval( $_COOKIE['aid'] ) > 0 ? intval( $_COOKIE['aid'] ) : 0;

		return $modelUser->storeRegistUser( $storeData ) > 0 ? true : false;
	}

//end class
}
