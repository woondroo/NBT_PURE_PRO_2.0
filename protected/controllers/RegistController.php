<?php
/**
 * Regist Controller
 * 
 * @author wengebin
 * @date 2013-10-11
 */
class RegistController extends BaseController
{
	/**
	 * 初始化
	 */
	public function init()
	{
		parent::init();
	}
	
	/**
	 * 注册
	 */
	public function actionIndex()
	{
		$this->seoTitle = CUtil::i18n('controllers,regist_index_seoTitle');

		// is mini window
		$isMini = isset( $_REQUEST['ismini'] ) ? intval( trim( $_REQUEST['ismini'] ) ) : 0;
		if ( $isMini === 1 )
		{
			$this->layout = "blank";
		}

		$gourl = Nbt::app()->request->getParam('gourl','');
		
		$aryData = array();

		if ( Nbt::app()->user->getUserId() > 0 )
			$this->redirect( array('memberInfo/index') );

		// 捕获异常
		try
		{
			$model = new RegistForm();
			if( Nbt::app()->request->isPostRequest )
			{
				// 绑定数据
				$aryData['username'] = isset( $_POST['username'] ) ? htmlspecialchars( trim( $_POST['username'] ) ) : '';
				$aryData['password'] = isset( $_POST['password'] ) ? htmlspecialchars( trim( $_POST['password'] ) ) : '';
				$aryData['captcha'] = isset( $_POST['captcha'] ) ? htmlspecialchars( trim( $_POST['captcha'] ) ) : '';

				// 开始注册
				if( $model->regist( $aryData ) )
				{
					$gourl = urldecode( $gourl );

					$loginModel = new LoginForm();
					$loginModel->login( $aryData );

					if ( empty($gourl) ) $gourl = array( 'memberInfo/index' );
					if ( $isMini === 1 ) $gourl = array('regist/reload');
					$this->redirect( $gourl );
				}
			}
		} catch ( CModelException $e ) {
			$model->addError( 'sys' , $e->getMessage() );
			//UtilMsg::saveErrorTipToSession( $e->getMessage() );
		} catch ( Exception $e ) {
			$model->addError( 'sys' , NBT_DEBUG ? $e->getMessage() :CUtil::i18n('message,msg_sys_error'));
			//UtilMsg::saveErrorTipToSession( NBT_DEBUG ? $e->getMessage() : '系统错误' );
		}

		// 输出 model 及 其他数据到页面
		$aryParams = array();
		$aryParams['model'] = $model;
		$aryParams['aryData'] = $aryData;
		if ( $isMini === 1 )
			$this->render( 'registmini' , $aryParams );
		else
			$this->render( 'regist' , $aryParams );
	}

	/**
	 * 注册成功
	 */
	public function actionReload()
	{
		$this->layout = "blank";
		$this->render( 'reload' , array() );
	}

// end class
}
