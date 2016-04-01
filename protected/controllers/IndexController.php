<?php
/**
 * Index Controller
 * 
 * @author wengebin
 * @date 2013-12-31
 */
class IndexController extends BaseController
{
	private $_redis;

	/**
	 * init
	 */
	public function init()
	{
		parent::init();		
	}
	
	/**
	 * 首页
	 */
	public function actionIndex()
	{
		$this->seoTitle = CUtil::i18n('controllers,index_index_title');
		$this->seoKeyword = CUtil::i18n('controllers,index_index_seoKeyword');
		$this->seoDesc = CUtil::i18n('controllers,index_index_seoDesc');

		$this->render( 'index' , array() );
	}

	// 初始化Mongo索引
	public function actionInitIndexForMongo()
	{
		$model = MongoEnsureIndexModel::model();
		$model->initEnsureIndex();

		echo 'Init mongo index success!';
	}

//end class
}
