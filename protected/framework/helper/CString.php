<?php
/**
 * CString class file.
 * 
 * @author wengebin
 * @package framework
 * @date 2014-04-16
 */
class CString
{
	/**
	 * 密码加密
	 */
	public static function encodeUserPassword( $_strPwd = '' )
	{
		return md5( "www-user-".$_strPwd );
	}

	/**
	* Passport 加密函数
	*
	* @param	 string	 等待加密的原字串
	* @param	 string	 私有密匙(用于解密和加密)
	*
	* @return	string	 原字串经过私有密匙加密后的结果
	*/
	public static function encode( $_strVal = "" , $_strKey = "" )
	{
		// 使用随机数发生器产生 0~32000 的值并 MD5()
		srand((double)microtime() * 1000000);
		$encrypt_key = md5(rand(0, 32000));
		
		// 变量初始化
		$ctr = 0;
		$tmp = '';
		
		// for 循环，$i 为从 0 开始，到小于 $_strVal 字串长度的整数
		for($i = 0; $i < strlen($_strVal); $i++)
		{
			// 如果 $ctr = $encrypt_key 的长度，则 $ctr 清零
			$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
			// $tmp 字串在末尾增加两位，其第一位内容为 $encrypt_key 的第 $ctr 位，
			// 第二位内容为 $_strVal 的第 $i 位与 $encrypt_key 的 $ctr 位取异或。然后 $ctr = $ctr + 1
			$tmp .= $encrypt_key[$ctr].($_strVal[$i] ^ $encrypt_key[$ctr++]);
		}
		
		// 返回结果，结果为 passport_key() 函数返回值的 base65 编码结果
		return base64_encode(self::doOp($tmp, $_strKey));
	
	}
	
	/**
	* Passport 解密函数
	*
	* @param	 string	 加密后的字串
	* @param	 string	 私有密匙(用于解密和加密)
	*
	* @return	string	 字串经过私有密匙解密后的结果
	*/
	public static function decode( $_strVal = "" , $_strKey = "" )
	{
	
		// $_strVal 的结果为加密后的字串经过 base64 解码，然后与私有密匙一起，
		// 经过 passport_key() 函数处理后的返回值
		$_strVal = self::doOp(base64_decode($_strVal), $_strKey);
		
		// 变量初始化
		$tmp = '';
		
		// for 循环，$i 为从 0 开始，到小于 $_strVal 字串长度的整数
		for ($i = 0; $i < strlen($_strVal); $i++)
		{
			// $tmp 字串在末尾增加一位，其内容为 $_strVal 的第 $i 位，
			// 与 $_strVal 的第 $i + 1 位取异或。然后 $i = $i + 1
			$tmp .= $_strVal[$i] ^ $_strVal[++$i];
		}
		
		// 返回 $tmp 的值作为结果
		return $tmp;
	
	}
	
	/**
	* Passport 密匙处理函数
	*
	* @param	 string	 待加密或待解密的字串
	* @param	 string	 私有密匙(用于解密和加密)
	*
	* @return	string	 处理后的密匙
	*/
	public static function doOp( $_strVal = "" , $_strKey = "" )
	{	
		// 将 $encrypt_key 赋为 $encrypt_key 经 md5() 后的值
		$strKey = md5( $_strKey );
		
		// 变量初始化
		$ctr = 0;
		$tmp = '';
		
		// for 循环，$i 为从 0 开始，到小于 $_strVal 字串长度的整数
		for($i = 0; $i < strlen($_strVal); $i++) {
			// 如果 $ctr = $encrypt_key 的长度，则 $ctr 清零
			$ctr = $ctr == strlen($strKey) ? 0 : $ctr;
			// $tmp 字串在末尾增加一位，其内容为 $_strVal 的第 $i 位，
			// 与 $encrypt_key 的第 $ctr + 1 位取异或。然后 $ctr = $ctr + 1
			$tmp .= $_strVal[$i] ^ $strKey[$ctr++];
		}
	
		// 返回 $tmp 的值作为结果
		return $tmp;
	}

	/**
	 * 获得url域名
	 *
	 * @param string $_strUrl URL地址
	 * @return string
	 */
	public static function getDomain($_strUrl)
	{
		$pattern = "/[\w-]+\.(com|net|org|gov|biz|com.tw|com.hk|com.ru|net.tw|net.hk|net.ru|info|cn|com.cn|net.cn|org.cn|gov.cn|mobi|name|sh|ac|la|travel|tm|us|cc|tv|jobs|asia|hn|lc|hk|bz|com.hk|ws|tel|io|tw|ac.cn|bj.cn|sh.cn|tj.cn|cq.cn|he.cn|sx.cn|nm.cn|ln.cn|jl.cn|hl.cn|js.cn|zj.cn|ah.cn|fj.cn|jx.cn|sd.cn|ha.cn|hb.cn|hn.cn|gd.cn|gx.cn|hi.cn|sc.cn|gz.cn|yn.cn|xz.cn|sn.cn|gs.cn|qh.cn|nx.cn|xj.cn|tw.cn|hk.cn|mo.cn|org.hk|is|edu|mil|au|jp|int|kr|de|vc|ag|in|me|edu.cn|co.kr|gd|vg|co.uk|be|sg|it|ro|com.mo)(\.(cn|hk))$/";
		preg_match($pattern, $_strUrl, $matches);

		if(count($matches) > 0)
		{
			var_dump($matches);
			return $matches[0];
		}
		else
		{
			$rs = parse_url($_strUrl);
			$main_url = $rs["host"];
			if(!strcmp(long2ip(sprintf("%u",ip2long($main_url))),$main_url))
			{
				return $main_url;
			}
			else
			{
				$arr = explode(".",$main_url);
				$count=count($arr);
				$endArr = array("com","net","org");//com.cn net.cn 等情况

				if (in_array($arr[$count-2],$endArr))
				{
					$domain = $arr[$count-3].".".$arr[$count-2].".".$arr[$count-1];
				}
				else
				{
					$domain = $arr[$count-2].".".$arr[$count-1];
				}
				return $domain;
			}
		}
	}
	
//end class
}
