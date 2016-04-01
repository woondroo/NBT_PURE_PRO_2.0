<?php
/**
 * BaseMember Controller
 * 会员中心基类控制器
 * 
 * @author wengebin
 */
class BaseMemberController extends BaseController
{
	public $userId = 0;
	
	/**
	 * 初始化，验证是否登录
	 * 
	 */
	public function init()
	{
		parent::init();
		
		$intUserId = Nbt::app()->user->getUserId();
		if( $intUserId <= 0 )
		{
			//ajax请求，输出ajax格式信息
			if( Nbt::app()->request->isAjaxRequest )
			{
				echo $this->encodeAjaxData( false , array() , CUtil::i18n('message,msg_login_noLoginyet') );
				exit();
			}
			else
				$this->redirect(array('index/index'));
		}
	}
	

//end class 
}
