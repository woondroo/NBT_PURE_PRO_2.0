<?php
/**
 * 登录
 * 
 * @author wengebin
 * @date 2013-11-20
 */
class LoginController extends BaseController
{
	/**
	 * 初始化
	 */
	public function init()
	{
		parent::init();
	}
	
	/**
	 * 登录页
	 */
	public function actionIndex()
	{
		$this->seoTitle = CUtil::i18n('controllers,login_index_seoTitle');
		if( !Nbt::app()->user->isGuest )
			$this->redirect( array( 'memberInfo/index' ) );
		
		// is mini window
		$isMini = isset( $_REQUEST['ismini'] ) ? intval( trim( $_REQUEST['ismini'] ) ) : 0;
		if ( $isMini === 1 )
		{
			$this->layout = "blank";
		}

		$gourl = Nbt::app()->request->getParam('gourl','');
		
		$aryData = array();
		$model = new LoginForm();
		if( Nbt::app()->request->isPostRequest )
		{
			// 捕获异常
			try
			{
				// 绑定数据
				$aryData['username'] = isset( $_POST['username'] ) ? htmlspecialchars( trim( $_POST['username'] ) ) : '';
				$aryData['password'] = isset( $_POST['password'] ) ? htmlspecialchars( trim( $_POST['password'] ) ) : '';
				$aryData['captcha'] = isset( $_POST['captcha'] ) ? htmlspecialchars( trim( $_POST['captcha'] ) ) : '';
				// 验证登录
				if( $model->login( $aryData ) )
				{
					$gourl = urldecode( $gourl );
					$gourl = empty($gourl)?array('memberInfo/index'):$gourl;
					if ( $isMini === 1 ) $gourl = array('login/reload');

					$this->redirect( $gourl );
				}

			} catch ( CModelException $e ) {
				UtilMsg::saveErrorTipToSession( $e->getMessage() );
			} catch ( Exception $e ) {
				UtilMsg::saveErrorTipToSession( NBT_DEBUG ? $e->getMessage() : CUtil::i18n('message,msg_sys_error'));
			}
		}
		
		// 输出 model 及 其他数据到页面
		$aryParams = array();
		$aryParams['model'] = $model;
		$aryParams['aryData'] = $aryData;

		// 获得是否需要验证码状态
		$aryParams['iscaptcha'] = $model->getIscaptcha();
		$aryParams['gourl'] = $gourl;

		if ( $isMini === 1 )
			$this->render( 'loginmini' , $aryParams );
		else
			$this->render( 'login' , $aryParams );
	}

	/** 
	 * 检查用户是否需要验证码
	 */
	public function actionCheck()
	{   
		// 获得用户名
		$strUsername = isset( $_REQUEST['un'] ) ? htmlspecialchars( trim( $_REQUEST['un'] ) ) : ''; 

		$isok = false;
		$data = array();
		$msg = ""; 

		try 
		{   
			if ( empty($strUsername) )
				throw new CModelException(CUtil::i18n('exception,exec_name_mustEnter') );

			$model = new LoginForm();
			$isneed = $model->isNeedCheckCode( $strUsername );
			if ( $isneed === true ) 
				$data['isneed'] = 1;
			else
				$data['isneed'] = 0;

			$isok = 1;
		} catch ( CModelException $e ) { 
			$msg = $e->getMessage();
		} catch ( Exception $e ) { 
			$msg = NBT_DEBUG ? $e->getMessage() : CUtil::i18n('message,msg_sys_error');
		}   

		echo $this->encodeAjaxData( $isok , $data , $msg , false );
		exit();
	}

	/**
	 * 登录成功
	 */
	public function actionReload()
	{
		$this->layout = "blank";
		$this->render( 'reload' , array() );
	}
	
	/**
	 * logout,and redirect login page.
	 */
	public function actionLogout()
	{
		Nbt::app()->user->logout();
		$this->redirect( array( 'index/index' ) );
	}

// end class
}
