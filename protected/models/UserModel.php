<?php
/**
 * User Model
 * 
 * @author wengebin
 * @date 2014-01-01
 */
class UserModel extends CMongoModel
{
	/**
	 * 集合名
	 * 
	 * @var string
	 */
	const COLLECTION_NAME = 'user';

	/**
	 * 分页，每页数量
	 * 
	 * @var int
	 */
	const PAGINATION_NUM = 20;
	
	/**
	 * 检验规则
	 *
	 */
	public function rules()
	{
		return array(
				// 重置密码验证
				array('u_newpwd,u_repwd','required','message'=>CUtil::i18n('models,loginForm_notNull'),'on'=>'resetpass'),
				array('u_newpwd','compare','compareValue'=>$this->getData('u_repwd') , 'message'=>CUtil::i18n('models,user_rules_compareDiffer') ,'on'=>'resetpass' ),

				// 邮箱绑定
				array('u_email','required','message'=>CUtil::i18n('models,user_rules_noNll'),'on'=>'safemail'),
				array('u_email','email','message'=>CUtil::i18n('models,user_rules_formatError'),'on'=>'safemail'),

				// 手机绑定
				array('u_phone','required','message'=>CUtil::i18n('models,user_rules_noNll'),'on'=>'safephone'),
				array('u_phone','phone','message'=>CUtil::i18n('models,user_rules_formatError'),'on'=>'safephone'),
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
			'u_password'=>CUtil::i18n('models,user_fieldCnName_password'),
			'u_newpwd'=>CUtil::i18n('models,user_fieldCnName_newPass'),
			'u_repwd'=>CUtil::i18n('models,user_fieldCnName_repwd'),
			'u_email'=>CUtil::i18n('models,user_fieldCnName_email'),
			'u_phone'=>CUtil::i18n('models,user_fieldCnName_phone')
		);
	}

	/**
	 * 初始化
	 */
	public function init()
	{
		// 设置表名
		$this->setCollectionName( self::COLLECTION_NAME );
		// 设置文档类名
		$this->setDocumentClassName( __CLASS__ );

		parent::init();
	}

	/**
	 * 返回惟一实例
	 *
	 * @return NewsModel
	 */
	public static function model( $className = __CLASS__ )
	{
		return parent::model( __CLASS__ );
	}

	/**
	 * 根据用户ID 查询用户信息
	 *
	 * @param int $_intUid 用ID
	 * @param boolean $_boolReturnDocument 是否返回Document对象
	 * @return array
	 */
	public function getUserMessageByUid( $_intUid = 0 , $_boolReturnDocument = false)
	{
		if ( empty( $_intUid ) )
			throw new CModelException( CUtil::i18n('models,params_error') );

		// 查询数据，不需要将ID转换为MongoID
		$data = $this->getCollection()->find()->where('_id',$_intUid)->findOne();

		// 根据条件返回数据
		if ( $_boolReturnDocument === true )
			return $data;
		else if ( empty($data) )
			return array();
		else
			return $data->toArray();
	}

	/**
	 * 根据用户名查询用户信息
	 *
	 * @param string $_strUsername 用户名
	 * @param boolean $_boolReturnDocument 是否返回Document对象
	 * @return array
	 */
	public function getUserMessageByUsername( $_strUsername = '' , $_boolReturnDocument = false)
	{
		if ( empty( $_strUsername ) )
			throw new CModelException( CUtil::i18n('models,params_error') );

		// 查询
		$data = $this->getCollection()->find()->where( 'u_username' , $_strUsername )->findOne();

		// 根据条件返回数据
		if ( $_boolReturnDocument === true )
			return $data;
		else if ( empty($data) )
			return array();
		else
			return $data->toArray();
	}

	/**
	 * 根据邮箱查询用户信息
	 * 
	 * @param array $_strMail 用户邮箱
	 * @param boolean $_boolReturnDocument 是否返回Document对象
	 * @return bool
	 */
	public function getUserMessageByMail( $_strMail = '' , $_boolReturnDocument = false)
	{
		// 验证数据
		$aryData = array('u_email'=>$_strMail);
		$this->setData( $aryData );
		$this->setScerian( 'safemail' );
		if ( !$this->validate( $aryData ) )
			throw new CModelException(CUtil::i18n('exception,exec_email_emailFormatWrong'));

		// 查询
		$data = $this->getCollection()->find()->where( 'u_email' , $_strMail )->findOne();

		// 根据条件返回数据
		if ( $_boolReturnDocument === true )
			return $data;
		else if ( empty($data) )
			return array();
		else
			return $data->toArray();
	}

	/**
	 * 根据手机号查询用户信息
	 * 
	 * @param array $_strPhone 用户邮箱
	 * @param boolean $_boolReturnDocument 是否返回Document对象
	 * @return bool
	 */
	public function getUserMessageByPhone( $_strPhone = '' , $_boolReturnDocument = false)
	{
		// 验证数据
		$aryData = array('u_phone'=>$_strPhone);
		$this->setData( $aryData );
		$this->setScerian( 'safephone' );
		if ( !$this->validate( $aryData ) )
			throw new CModelException(CUtil::i18n('exception,exec_email_emailFormatWrong'));

		// 查询
		$data = $this->getCollection()->find()->where( 'u_phone' , $_strPhone )->findOne();

		// 根据条件返回数据
		if ( $_boolReturnDocument === true )
			return $data;
		else if ( empty($data) )
			return array();
		else
			return $data->toArray();
	}

	/**
	 * 修改数据
	 *
	 * @param int $_intUid 用户ID
	 * @param array $_aryData 状态
	 * @return boolean|string
	 */
	public function updateById( $_intUid = '' , $_aryData = 0 )
	{
		if ( empty( $_intUid ) )
			throw new CModelException( CUtil::i18n('models,params_error') );

		// 获得库连接
		$collection = $this->getCollection();

		// 建立查询表达式
		$expression = $collection
				->expression()
				->where('_id',$_intUid);

		// 建立修改表达式
		$operator = $collection
				->operator();
		// 循环所有需要修改的数据
		foreach ( $_aryData as $key=>$val )
			$operator = $operator->set( $key , $val );

		// 开始修改指定条件的数据
		$collection->updateMultiple( $expression , $operator );

		// 如果执行修改失败，会抛出异常，如果正常执行，则返回true即可
		return true;
	}

	/**
	 * 分页查询
	 *
	 * @param array $_aryData 查询条件
	 * @return array
	 */
	public function getPageList( $_aryData = array() )
	{
		// 实现分页
		$cpages = new CMongoPages(0);
		$cpages->setPageSize(self::PAGINATION_NUM);
		// 当前页数
		$intCurPage = isset( $_GET[$cpages->getPageParamName()] ) ? intval( $_GET[$cpages->getPageParamName()] ) : 1;

		// 查询
		$cursor = $this->getCollection()->find();

		// 整理条件
		$_aryData = $this->mergeParams( $_aryData , 'regtime_aid_username_phone_status' );
		foreach ( $_aryData as $key=>$val )
			$cursor = $cursor->where( $key , $val );
		$cursor->sort( array( 'u_regtime'=>-1 ) );
		
		// 分页
		$pagination = $cursor->paginate( $intCurPage , self::PAGINATION_NUM );
		$intTotal = $pagination->getTotalRowsCount();

		// 设置总数量
		$cpages->setItemCount( $intTotal );

		return array(
					'datas'=>$pagination,
					'pages'=>$cpages
				);
	}
	
	/**
	 * 注册用户
	 *
	 * @params array $_aryData 用户数据
	 * @return boolean | int, if regist success
	 */
	public function storeRegistUser( $_aryData = array() )
	{
		if ( empty( $_aryData ) )
			return false;

		// 获得下一个ID
		$counter = new CounterModel();
		$arySequence = $counter->getNextSequence( 'userid' );

		$aryData = array();
		$aryData['_id'] = $arySequence['seq'];
		$aryData['u_username'] = $_aryData['u_username'];
		$aryData['u_password'] = $_aryData['u_password'];
		$aryData['u_aid'] = $_aryData['u_aid'];
		$aryData['u_status'] = CUtil::USER_ACCOUNT_STATUS_DISABLED;
		$aryData['u_regtime'] = time();

		// 合并数据，让所有字段生成
		$aryData = array_merge( UserMongoModel::$_fields , $aryData );

		// 创建新数据
		$aryDoc = $this->getCollection()->createDocument( $aryData )->save();
		return empty( $aryDoc ) ? false : $aryDoc->get('_id');
	}

	/**
	 * 重置密码
	 * 
	 * @param array $_aryData 用户数据
	 * @param int $_intUid 用户ID
	 * @param boolean $_boolIsCheckOldPass 是否需要验证密码
	 * @return bool
	 */
	public function resetPass( $_aryData = array() , $_intUid = 0 , $_boolIsCheckOldPass = true )
	{
		// 验证数据
		$this->setData( $_aryData );
		$this->setScerian( 'resetpass' );
		if( !$this->validate( $_aryData ) )
			return false;

		// 用户原始数据
		$userData = $this->getUserMessageByUid($_intUid);

		// 判断密码是否正确
		if( $_boolIsCheckOldPass === true 
				&& $userData['u_password'] != CString::encodeUserPassword( $_aryData['u_password'] ) )
		{

			$this->addError( 'u_password' , CUtil::i18n('models,loginForm_wrong'));
			return false;
		}

		// 修改数据
		$aryUpdate = array();
		$aryUpdate['u_password'] = CString::encodeUserPassword($_aryData['u_newpwd']);
		return $this->updateById($_intUid , $aryUpdate);
	}

	/**
	 * 验证邮箱
	 * 
	 * @param array $_strMail 用户邮箱
	 * @return bool
	 */
	public function checkMail( $_strMail = '' ){
		// 验证数据
		$aryData = array('u_email'=>$_strMail);
		$this->setData( $aryData );
		$this->setScerian( 'safemail' );
		if( !$this->validate( $aryData ) )
			return false;

		// 查询
		$cursor = $this->getCollection()->find();

		// 整理条件
		$aryData['u_status'] = CUtil::USER_ACCOUNT_STATUS_ENABLED;
		$aryData = $this->mergeParams( $aryData , 'regtime_aid_username_phone_status' );
		foreach ( $aryData as $key=>$val )
			$cursor = $cursor->where( $key , $val );

		// 查询数据
		$data = $cursor->findOne();

		// 如果邮箱已被占用
		if ( !empty( $data ) && $data['_id'] > 0 )
			throw new CModelException( CUtil::i18n('exception,exec_email_adsBeenOccupied') );
		else
			return true;
	}

	/**
	 * 绑定邮箱
	 * 
	 * @param array $_strMail 用户邮箱
	 * @param int $_intUid 用户ID
	 * @param int $_intStatus 用户邮箱激活状态
	 * @return bool
	 */
	public function checkAndBindMail( $_strMail = '' , $_intUid = 0 , $_intStatus = 0 ){
		if ( empty( $_intUid ) )
			return false;
		
		// 验证数据
		$boolCheckResult = $this->checkMail( $_strMail );

		// 如果邮箱未被占用
		if ( $boolCheckResult === true )
		{
			$aryUpdateData = array();
			$aryUpdateData['u_email'] = $_strMail;
			$aryUpdateData['u_status'] = $_intStatus;

			return $this->updateById( $_intUid , $aryUpdateData );
		}
		else
			throw new CModelException(CUtil::i18n('exception,exec_email_adsInvalid'));
	}

//end class
}
