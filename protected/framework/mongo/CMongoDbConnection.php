<?php
/**
 * Mongo 数据库连接
 *
 * @author wengebin
 * @date 2014-09-03
 */
class CMongoDbConnection
{
	/**
	 * Default Mongo 库连接
	 */
	private static $_dbConnectionDefault = null;

	/**
	 * 获取Web Mongo connection
	 * @return CPdo
	 */
	public static function getDefaultMongoConnection()
	{
		if( self::$_dbConnectionDefault === null )
		{
			// Connection to client
			$mongo = new CMongoClient(
					MONGO_CONNECT_PROTOCAL.MONGO_CONNECT_ADD.":".MONGO_CONNECT_PORT
				);
			// Connection to database
			$database = $mongo->getDatabase( MONGO_DEFAULT_DB_NAME );
			
			self::$_dbConnectionDefault = $database;
		}

		return self::$_dbConnectionDefault;
	}

//end class
}
