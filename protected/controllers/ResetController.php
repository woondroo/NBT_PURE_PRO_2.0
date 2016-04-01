<?php
/**
 * Reset Controller
 * 设备绑定
 * 
 * @author wengebin
 * @date 2014-01-01
 */
class ResetController extends BaseController 
{
	/**
	 * 初始化
	 */
	public function init()
	{
		parent::init();
	}

	/**
	 * 默认
	 * @author wengebin
	 */
	public function actionIndex(){
		exit();
	}

	/**
	 * 忘记密码
	 * @author wengebin
	 */
	public function actionForget()
	{
		$this->seoTitle = CUtil::i18n('controllers,reset_forget_pass');

		$aryParams = array();
		$this->render( 'forget' , $aryParams );
	}

	/**
	 * 重置密码
	 * @author wengebin
	 */
	public function actionResetPwd()
	{
		$this->seoTitle = CUtil::i18n('controllers,reset_reset_Pwd');

		$sign = htmlspecialchars( trim( $_GET['key'] ) );
		$aryResetData = json_decode( Nbt::app()->session->get( 'reset_pass_result' ) , 1 );
		if ( empty( $sign ) 
				|| empty( $aryResetData ) 
				|| $sign != $aryResetData['key'] 
				|| $aryResetData['used'] === 1
				|| empty( $aryResetData['uid'] ) )
		{
			UtilMsg::saveErrorTipToSession(CUtil::i18n('controllers,reset_signatureInvalid'));
			$this->redirect( $this->createUrl( 'login/index' ) );
		}

		$userModel = new UserModel();
		$aryUserData = $userModel->getUserMessageByUid( $aryResetData['uid'] );
		if ( empty( $aryUserData ) )
		{
			UtilMsg::saveErrorTipToSession(CUtil::i18n('controllers,reset_pwd_userNotExist'));
			$this->redirect( $this->createUrl( 'login/index' ) );
		}

		if ( Nbt::app()->request->isPostRequest )
		{
			$aryData = array();
			$aryData['u_newpwd'] = htmlspecialchars( trim( $_POST['u_newpassword']) );
			$aryData['u_repwd'] = htmlspecialchars( trim( $_POST['u_repassword']) );
			if( $userModel->resetPass( $aryData , $aryResetData['uid'] , false ) )
			{
				$aryResetData['used'] = 1;
				Nbt::app()->session->set( 'reset_pass_result' , json_encode( $aryResetData ) );

				UtilMsg::saveTipToSession(CUtil::i18n('controllers,reset_pwd_success'));
				$this->redirect( $this->createUrl( 'login/index' ) );
			}
		}

		$aryParams = array();
		$aryParams['model'] = $userModel;
		$this->render( 'reset' , $aryParams );
	}

	/**
	 * 发送重置邮件
	 * @author wengebin
	 */
	public function actionSendReset()
	{
		$isok = 0;
		$data = array();
		$msg = "";
		
		if( Nbt::app()->request->isAjaxRequest ) {
			$strMail = htmlspecialchars( trim( $_POST['e'] ) );

			try
			{
				if ( empty( $strMail ) )
					throw new CModelException(CUtil::i18n('exception,exec_email_AddressMustEnter'));
				
				// 检查邮箱并获得用户数据
				$userModel = new UserModel();
				$aryUserData = $userModel->getUserMessageByMail( $strMail );
				if ( empty( $aryUserData ) || empty( $aryUserData['u_id'] ) )
					throw new CModelException(CUtil::i18n('exception,exec_email_notBoundYet'));

				// 发送重置邮件
				$mailModel = new MailModel();
				$strResetUrl = $mailModel->generateMailUrl( 'RESET' , $aryUserData['u_id'] , $strMail );
				$boolSendResult = $mailModel->sendResetMail( $strMail , $aryUserData['u_username'] , $strResetUrl );

				// 发送成功
				if ( $boolSendResult === true )
					$isok = 1;
				else
					throw new CModelException(CUtil::i18n('exception,exec_email_sendFailed'));

				$isok = 1;
			} catch ( CModelException $e ) {
				$msg = $e->getMessage();
			} catch ( Exception $e ) {
				$msg = NBT_DEBUG ? $e->getMessage() : CUtil::i18n('message,msg_sys_error');
			}
		}

		echo $this->encodeAjaxData( $isok , $data , $msg , false );
		exit();
	}

	/**
	 * 确认重置邮件
	 * @author wengebin
	 */
	public function actionConfirmReset(){
		try
		{
			$strData = trim( $_GET['d'] );
			// [0] - uid [1] - timestamp [2] - sign
			$aryParams = explode( '|' , base64_decode( urldecode( $strData ) ) );
			if ( empty( $strData ) || empty( $aryParams ) )
				throw new CModelException(CUtil::i18n('exception,exec_data_Incomplete'));

			// 用户ID
			$intUid = intval( $aryParams[0] );
			// 时间戳
			$intTimestamp = intval( $aryParams[1] );
			// 签名
			$sign = $aryParams[2];

			if ( empty( $intUid ) )
				throw new CModelException(CUtil::i18n('exception,exec_pass_cannotReset'));

			$userModel = new UserModel();
			$aryUserData = $userModel->getUserMessageByUid( $intUid );
			if ( empty( $aryUserData ) )
				throw new CModelException(CUtil::i18n('exception,exec_passReset_userNotExist'));

			$aryResetData = json_decode( Nbt::app()->session->get( 'reset_pass_result' ) , 1 );
			if ( !empty( $aryResetData ) && $sign == $aryResetData['key'] && $aryResetData['used'] === 1 )
				throw new CModelException(CUtil::i18n('exception,exec_signature_invalid'));

			// 签名数据
			$aryCheckData = array();
			$aryCheckData['time'] = $intTimestamp;
			$aryCheckData['uid'] = $intUid;
			$aryCheckData['email'] = $aryUserData['u_email'];

			// 验证签名
			if( !CApi::verifySign( $aryCheckData , $sign , MAIN_DOMAIN_RESET_MAIL_KEY ) )
				throw new CModelException(CUtil::i18n('exception,exec_signature_Failed'));

			// 判断签名是否过期
			if ( time() - $intTimestamp > 3600 )
				throw new CModelException(CUtil::i18n('exception,exec_signature_outdated'));

			if ( empty( $aryUserData['u_email'] ) )
				throw new CModelException(CUtil::i18n('exception,exec_email_notBound'));

			// 开始重置密码
			$aryResetData = array( 'uid'=>$aryUserData['u_id'] , 'used'=>0 , 'key'=>$sign , 'time'=>time() );
			Nbt::app()->session->set( 'reset_pass_result' , json_encode( $aryResetData ) );
			$this->redirect( $this->createUrl( 'reset/resetPwd' , array('key'=>$sign) ) );

		} catch ( CModelException $e ) {
			UtilMsg::saveErrorTipToSession($e->getMessage());
		} catch ( Exception $e ) {
			UtilMsg::saveErrorTipToSession(NBT_DEBUG ? $e->getMessage() :CUtil::i18n('message,msg_sys_error'));
		}

		$this->redirect( $this->createUrl( 'login/index' ) );
	}

//end class
}
