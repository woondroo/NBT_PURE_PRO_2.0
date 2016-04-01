<?php
/**
 * 递增统计
 * 
 * @author wengebin
 * @date 2014-01-01
 */
class CounterModel extends CMongoModel
{
	/**
	 * 集合名
	 * 
	 * @var string
	 */
	const COLLECTION_NAME = 'counters';

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
	 * 获得下一个ID
	 * 
	 * @param array $_strName 递增字段名
	 * @return bool
	 */
	public function getNextSequence( $_strName )
	{
		// 获得Collection
		$collection = $this->getCollection();
		// 找到并修改，进行递增
		return $collection->getMongoCollection()->findAndModify(
				$collection->expression()->where('_id',$_strName)->toArray(),
				$collection->operator()->increment('seq',1)->getAll(),
				null,
				array(
					'new'    => true,
					'upsert' => true, 
				)
			);
	}

//end class
}
