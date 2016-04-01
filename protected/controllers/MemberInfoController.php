<?php
/**
 * Member Controller
 * 会员中心控制器
 * 
 * @author wengebin
 * @date 2014-01-01
 */
class MemberInfoController extends BaseMemberController 
{
	/**
	 * 初始化
	 *
	 */
	public function init()
	{
		parent::init();
	}
	
	/**
	 * 用户基本资料
	 * @author wengebin
	 */
	public function actionIndex()
	{
                
		$this->seoTitle = CUtil::i18n('controllers,memberinfo_index_seoTitle');
                
		try
		{
			$intUid = Nbt::app()->user->getUserId();
			if ( empty( $intUid ) )
				throw new CModelException( CUtil::i18n('exception,exec_login_loginInvalid') );
			
		} catch ( CModelException $e ) {
			UtilMsg::saveErrorTipToSession( $e->getMessage() );
		} catch ( Exception $e ) {
			UtilMsg::saveErrorTipToSession( NBT_DEBUG ? $e->getMessage() : CUtil::i18n('message,msg_sys_error') );
		}

		$aryData = array();
		$this->render('index',$aryData);
	}
	
	/**
	 * 修改用户密码
	 * @author wengebin
	 */
	public function actionUpdatePass(){
            
		$this->seoTitle = CUtil::i18n('controllers,memberinfo_updatePass_seoTitle');

		$userModel = new UserModel();
		if(Nbt::app()->request->isPostRequest){
			$aryPost = array();
			$aryData['u_password'] = htmlspecialchars( trim( $_POST['u_password'] ) );
			$aryData['u_newpwd'] = htmlspecialchars( trim( $_POST['u_newpassword']) );
			$aryData['u_repwd'] = htmlspecialchars( trim( $_POST['u_repassword']) );

			$intUid = Nbt::app()->user->getUserId();
			if($userModel->resetPass( $aryData , $intUid ))
			{
				UtilMsg::saveTipToSession(CUtil::i18n('controllers,memberinfo_index_pass_saveSuccess'));
				$this->redirect( $this->createUrl('memberInfo/updatePass') );
			}
		}

		$aryData = array();
		$aryData['model'] = $userModel;
		$this->render('update',$aryData);
	}

//end class
}
