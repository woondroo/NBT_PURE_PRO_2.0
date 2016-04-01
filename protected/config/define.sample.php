<?php
/**
 * This file defined some constant.
 * 
 * @author wengebin<wengebin@hotmail.com> 
 */
//网站根路径
define('WEB_ROOT',dirname(dirname(dirname(__FILE__))));

//是否打开DEBUG
define( 'IS_DEBUG' , true );

//开始运行时间(秒)
define( 'NBT_BEGIN_TIME' , time() );
//开始运行时间(微秒)
define( 'NBT_BEGIN_MICROTIME' , microtime(true) );

//主域名设置
define( 'MAIN_DOMAIN' , 'http://www.shicai88.com.tw' );

//是否开启地址重写
define( 'REWRITE_MODE' , true );

//SESSION存储位置
define( 'SESSION_CONNECT_ADD' , '127.0.0.1' );
define( 'SESSION_CONNECT_PORT' , '6379' );

//Redis连接
define( 'REDIS_CONNECT_ADD' , '127.0.0.1' );
define( 'REDIS_CONNECT_PORT' , '6379' );
//Redis存储域，比如 www 站点存储为 www 域开头的 key 中
define( 'REDIS_DISTRICT_NAME' , 'www' );
//是否开启缓存
define( 'CACHE_STATUS' , false );

//Mongo连接
define( 'MONGO_CONNECT_PROTOCAL' , 'mongodb://' );
define( 'MONGO_CONNECT_ADD' , '127.0.0.1' );
define( 'MONGO_CONNECT_PORT' , '27017' );
define( 'MONGO_DEFAULT_DB_NAME' , 'shicai88' );
