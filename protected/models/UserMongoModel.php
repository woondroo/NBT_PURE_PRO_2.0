<?php
/**
 * 用户信息Mongo Document
 * 
 * @author wengebin
 * @date 2015-02-28
 */
class UserMongoModel extends CMongoDocument
{
	/**
	 * 字段数据
	 *
	 * @var array
	 */
	public static $_fields = array(
			// Object id
			'_id'			=> 0,
			// 推广员ID
			'u_aid'			=> 0,
			// 用户名
			'u_username'	=> null,
			// 用户密码
			'u_password'	=> null,
			// 资金密码
			'u_safepwd'		=> null,
			// 昵称
			'u_nickname'	=> null,
			// 手机号
			'u_phone'		=> null,
			// 余额
			'u_balance'		=> 0.00,
			// 虚拟余额
			'u_balance2'	=> 0.00,
			// 总充值
			'u_recharge'	=> 0.00,
			// 虚拟总充值
			'u_recharge2'	=> 0.00,
			// 总消费
			'u_consume'		=> 0.00,
			// 虚拟总消费
			'u_consume2'	=> 0.00,
			// 总奖金
			'u_bonus'		=> 0.00,
			// 虚拟总消费
			'u_bonux2'		=> 0.00,
			// 活动金
			'u_activity'	=> 0.00,
			// 重试次数
			'u_retry'		=> 0,
			// 状态(0=未激活,1=激活)
			'u_status'		=> 0,
			// 注册时间
			'u_regtime'		=> 0,
			// 最后活动时间
			'u_lastlog'		=> 0,
		);

	/**
	 * 索引
	 *
	 * 复合索引key长度最多128个字符，按照"u_uid_1_u_aid_1_u_time_-1"这种方式组合
	 * @var array
	 */
	public static $_indexes = array(
				'joinIndex'=>array(
						array(
							'u_regtime'=>-1,
							'u_aid'=>1,
							'u_username'=>1,
							'u_phone'=>1,
							'u_status'=>1,
						)
					),
				'index'=>array(
						'u_aid'=>1,
						'u_username'=>1,
						'u_phone'=>1,
					)
			);

	/**
	 * 索引范围
	 *
	 * @var array
	 */
	public static $_indexesRange = array(
				'regtime_aid_username_phone_status'=>array(
						'u_regtime'=>array('$gte'=>0),
						'u_aid'=>array('$gte'=>0),
						'u_username'=>array('$in'=>array('/./',null)),
						'u_phone'=>array('$in'=>array('/./',null)),
						'u_status'=>array('$gte'=>0),
					)
			);

	/**
	 * 关系模型
	 */
	public function relations()
	{
		return array(
					// 用户-游戏记录-关系
					'userGameRelation' => array( self::RELATION_HAS_MANY , UserGameModel::COLLECTION_NAME , 'uga_uid' ),
					// 用户-中奖记录-关系
					'userBonusRelation' => array( self::RELATION_HAS_MANY , UserBonusModel::COLLECTION_NAME , 'ubo_uid' ),
					// 用户-充值记录-关系
					'userRechargeRelation' => array( self::RELATION_HAS_MANY , UserRechargeModel::COLLECTION_NAME , 'ur_uid' ),
					// 用户-提现记录-关系
					'userWithdrawalRelation' => array( self::RELATION_HAS_MANY , UserWithdrawalModel::COLLECTION_NAME , 'uw_uid' ),
					// 用户-资金记录-关系
					'userMoneyRelation' => array( self::RELATION_HAS_MANY , UserMoneyModel::COLLECTION_NAME , 'um_uid' ),
					// 用户-银行卡-关系
					'userBankRelation' => array( self::RELATION_HAS_ONE , UserBankModel::COLLECTION_NAME , 'ub_uid' ),
					// 用户-登录记录-关系
					'userLogRelation' => array( self::RELATION_HAS_MANY , UserLogModel::COLLECTION_NAME , 'ul_uid' ),
					// 用户-活动记录-关系
					'userActivityRelation' => array( self::RELATION_HAS_MANY , ActivityResultModel::COLLECTION_NAME , 'ar_uid' ),
				);
	}

//end class
}
