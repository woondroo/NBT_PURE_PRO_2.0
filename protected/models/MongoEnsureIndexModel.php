<?php
/**
 * Mongo索引初始化
 * 
 * @author wengebin
 * @date 2014-09-04
 */
class MongoEnsureIndexModel extends CModel
{
	/**
	 * 初始化
	 */
	public function init()
	{
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
	 * 初始化索引
	 *
	 * $model = MongoEnsureIndexModel::model();
	 * $model->initEnsureIndex();
	 */
	public function initEnsureIndex()
	{
		// user
		$model = UserModel::model();
		$model->initIndex();
	}
	
//end class
}
