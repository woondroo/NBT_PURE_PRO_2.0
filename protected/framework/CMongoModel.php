<?php
/**
 * Mongo base model.
 * 
 * @author wengebin
 * @package framework
 * @date 2014-09-03
 */
class CMongoModel extends CModel
{
	/**
	 * Mongo操作对象
	 * 
	 * @var CMongoDatabase
	 */
	private $_mongoDb;

	/**
	 * 集合名
	 * 
	 * @var string
	 */
	private $_collectionName;

	/**
	 * 文档名
	 * 
	 * @var string
	 */
	private $_documentClassName;

	/**
	 * 返回数据库连接
	 *
	 * @return Model
	 */
	public function getMongoDatabase( $_strDB = 'default' , $_boolChange = false )
	{
		// 数据库是否已连接
		if ( empty( $this->_mongoDb ) || $_boolChange === true )
		{
			// 初始化数据库连接
			switch ( $_strDB )
			{
				case 'default':
					$this->_mongoDb = CMongoDbConnection::getDefaultMongoConnection();
					break;

				case 'others':
					break;

				default:
					break;
			}
		}

		return $this->_mongoDb;
	}

	/**
	 * 强制切换数据库
	 *
	 * @return void
	 */
	public function changeDatabase( $_strDB = '' )
	{
		if ( !empty( $_strDB ) )
		{
			$this->getMongoDatabase( $_strDB , true );
		}
	}

	/**
	 * 设置Document类名
	 *
	 * @return void
	 */
	public function setDocumentClassName( $_strDocumentClassName = '' )
	{
		$this->_documentClassName = str_replace( 'Model' , 'MongoModel' , $_strDocumentClassName );
	}

	/**
	 * 设置表名
	 *
	 * @return void
	 */
	public function setCollectionName( $_strCollectionName = '' )
	{
		$this->_collectionName = $_strCollectionName;
	}

	/**
	 * 返回表名
	 *
	 * @return string
	 */
	public function getCollectionName()
	{
		return $this->_collectionName;
	}

	/**
	 * 返回集合实例
	 *
	 * @return string
	 */
	public function getCollection( $_strDB = 'default' )
	{
		// 获得库连接
		$collection = $this->getMongoDatabase( $_strDB )->getCollection( $this->getCollectionName() );
		$collection->setDocumentClassName( $this->_documentClassName );
		return $collection;
	}

	/**
	 * 初始化索引
	 *
	 * @return void
	 */
	public function initIndex()
	{
		$coll = $this->getCollection();

		// 获得所有索引
		$aryIndexes = eval('return '.$this->_documentClassName.'::$_indexes;');
		// 开始初始化索引
		foreach ( $aryIndexes as $name=>$indexes )
		{
			switch ( $name )
			{
				// 如果为复合索引
				case 'joinIndex':
					foreach ( $indexes as $index=>$indexVal )
					{
						$coll->ensureIndex( $indexVal );
					}
					break;

				// 如果为单索引
				case 'index':
					foreach ( $indexes as $index=>$indexVal )
					{
						$coll->ensureIndex( array( $index=>$indexVal ) );
					}
					break;

				default:
			}
		}
	}

	/**
	 * 获得默认索引范围
	 *
	 * @param string $_strIndexName 索引名称
	 * @return array
	 */
	public function getDefaultIndexArea( $_strIndexName )
	{
		$aryRange = eval('return '.$this->_documentClassName.'::$_indexesRange[$_strIndexName];');
		foreach ( $aryRange as &$d )
		{
			foreach ( $d as &$dd )
			{
				if ( $dd === '/./' )
					$dd = new MongoRegex($dd);

				if ( is_array($dd) )
				{
					foreach ( $dd as &$ddd )
					{
						if ( $ddd === '/./' )
							$ddd = new MongoRegex($ddd);
					}
				}
			}
		}

		return $aryRange;
	}

	/**
	 * 整理条件
	 * 
	 * @param array $_aryData 已有的数据
	 * @param string $_strIndexName 索引名称
	 * @return array
	 */
	public function mergeParams( $_aryData = array() , $_strIndexName )
	{
		// 获得默认数据
		$aryDefaultData = $this->getDefaultIndexArea( $_strIndexName );
		// 合并数据
		$aryMergeData = array_merge( $aryDefaultData , $_aryData );

		return $aryMergeData;
	}

//end class	
}
