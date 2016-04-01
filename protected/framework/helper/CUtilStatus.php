<?php
/**
 * 状态通用类
 */
class CUtilStatus
{
	/**游戏类型-重庆时时彩*/
	const GAME_TYPE_CQ = 1;
	/**游戏类型-江西时时彩*/
	const GMAE_TYPE_JX = 2;
	/**游戏类型-新疆时时彩*/
	const GMAE_TYPE_XJ = 3;

	/**派奖状态-未开奖*/
	const BONUS_CONFIRM_NO = 0;
	/**派奖状态-已完成*/
	const BONUS_CONFIRM_COMPLETE = 1;
	/**派奖状态-已派奖*/
	const BONUS_CONFIRM_YES = 2;

	/**中奖状态-未开奖*/
	const LOTTERY_STATUS_NO = 0;
	/**中奖状态-否*/
	const LOTTERY_STATUS_NO = 1;
	/**中奖状态-是*/
	const LOTTERY_STATUS_YES = 2;

	/**是否是虚拟数据-否*/
	const VIRTUAL_NO = 0;
	/**是否是虚拟数据-是*/
	const VIRTUAL_YES = 1;

	/**资金变动类型-充值*/
	const MONEY_CHANGE_TYPE_RECHARGE = 1;
	/**资金变动类型-提现*/
	const MONEY_CHANGE_TYPE_WITHDRAW = 2;
	/**资金变动类型-消费*/
	const MONEY_CHANGE_TYPE_CONSUMPTION = 3;
	/**资金变动类型-中奖*/
	const MONEY_CHANGE_TYPE_BONUS = 4;
	/**资金变动类型-活动*/
	const MONEY_CHANGE_TYPE_ACTIVITY = 5;

	/**管理员帐号状态-冻结*/
	const ADMIN_STATUS_DISABLED = 0;
	/**管理员帐号状态-正常*/
	const ADMIN_STATUS_ENABLE = 1;

	/**管理员帐号类型-超级管理员*/
	const ADMIN_TYPE_ADMIN = 0;
	/**管理员帐号类型-站点*/
	const ADMIN_TYPE_SITE = 1;
	/**管理员帐号类型-组长*/
	const ADMIN_TYPE_GROUP = 2;
	/**管理员帐号类型-推广员*/
	const ADMIN_TYPE_AGENT = 3;

	/**银行-工商*/
	const BANK_GS = 1;
	/**银行-建设*/
	const BANK_JS = 2;
	/**银行-农业*/
	const BANK_NY = 3;

// end class
}
