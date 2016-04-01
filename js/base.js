var actions = {
    
	// send post to server
	sendPost : function( callback , tourl , senddata ){
		// set default data
		if ( typeof( senddata ) == 'undefined' ) senddata = {};

		$.ajax({
			type	: "POST",
			url		: tourl,
			data 	: senddata,
			success : function( r ){
				eval( callback );
			},
			fail	: function(){
				// bind fail
			}
		});
	}
};

/**
 * ajax请求封装
 * @author wengebin
 */
var ajax = {
	url : '',
	data : null,
	configAjax : {},
	configTip : {},
	init : function()
	{
		this.url = '';
		this.data = null;
		this.configAjax = {};
		this.configTip = {};
		return this;
	},
	setUrl : function( _url )
	{
		this.url = _url;
		return this;
	},
	setData : function( _data )
	{
		this.data = _data;
		return this;
	},
	setSn : function( _sn )
	{
		this.configAjax.sn = _sn;
		return this;
	},
	setAjaxType : function( _strType )
	{
		this.configAjax.ajaxType = _strType;
		return this;
	},
	setIsConfirm : function( _isConfirm )
	{
		this.configAjax.isConfirm = _isConfirm;
		return this;
	},
	setConfirmInfo : function( _strConfirmInfo )
	{
		this.configAjax.confirm_info = _strConfirmInfo;
		return this;
	},	
	setCb : function( _strFunName )
	{
		this.configAjax.cb = _strFunName;
		return this;
	},
	setCbSuccess : function( _strFunName )
	{
		this.configAjax.cb_success = _strFunName;
		return this;
	},
	setCbError : function( _strFunName )
	{
		this.configAjax.cb_error = _strFunName;
		return this;
	},
	setCbSysError : function( _strFunName )
	{
		this.configAjax.cb_syserror = _strFunName;
		return this;
	},
	setIsLoadingTip : function( _isTip )
	{
		this.configTip.isLoadingTip = _isTip;
		return this;
	},
	setIsTip : function( _isTip )
	{
		this.configTip.isTip = _isTip;
		return this;
	},
	setTipId : function( _tipId )
	{
		this.configTip.tipId = _tipId;
		return this;
	},
	setTipPosition : function( _tipPosition )
	{
		this.configTip.tipPosition = _tipPosition;
		return this;
	},
	setIsAutoCloseTip : function( _isAutoCloseTip )
	{
		this.configTip.isAutoCloseTip = _isAutoCloseTip;
		return this;
	},
	preRun : function()
	{
		return ajaxV2( this.url , this.data , this.configAjax , this.configTip );
	}
};

var ajaxCbData = {
	cbData : {},
	sn : function()
	{
		var dateToday = new Date();
		return dateToday.getHours()+'-'+dateToday.getMinutes()+'-'+dateToday.getSeconds()+'-'+dateToday.getMilliseconds()+'-'+Math.floor(Math.random()*100+1);
	},
	set : function( _sn , _jsonData )
	{
		this.cbData[_sn] = _jsonData;
	},
	get : function( _sn )
	{
		var returnVal = this.cbData[_sn];
		delete( this.cbData[_sn] );
		return returnVal;
	}
};

var ajaxV2 = function( _url , _data , _config , _configTip ){
	var cfg = {
				sn:'',
				cb:'',//通信成功并未出现异常，回调函数
				cb_success:'',//操作成功，回调函数
				cb_error:'',//操作失败，回调函数
				cb_syserror:'',//系统错误，回调函数(如404,505等)
				ajaxType : 'POST',//请求类型					
				isConfirm : false,//是否进行confirm确认
				confirm_info : ''//确认信息
			 };

	//加载配置
	if( _config ) $.extend( cfg , _config );	

	//定义ajax操作
	var ajaxOp = {};
		ajaxOp.sn = cfg.sn;
		ajaxOp.run = function()
		{
			//确认操作
			if( cfg.isConfirm && !confirm( cfg.confirm_info ) ) return false;
			
			//发送ajax请求
			$.ajax({
					type:cfg.ajaxType,
					url:_url,
					data:_data,
					beforeSend:function()
					{
						// before send
					},
					success:function( XMLHttpRequest , textStatus )
					{
						try
						{
							var data = eval( '('+XMLHttpRequest+')' );
							//将ajax响应结果存入返回值
							if( cfg.sn != '' )
								ajaxCbData.set( cfg.sn , data );
							
							if( data.ISOK )
							{
								//执行成功回调函数
								if( cfg.cb_success != '' )
									eval( cfg.cb_success );
							}
							else
							{
								//未登录，弹出登录框
								if( !data.ISLOGIN )
								{
									// to login...
									return;
								}
								else
								{
									//执行成功回调函数
									if( cfg.cb_error != '' )
										eval( cfg.cb_error );
								}
							}
							if( cfg.cb != '' )
								eval( ajax.cb );
						}
						catch( e )
						{
							//执行失败回调函数
							if( cfg.cb_syserror != '' )
								eval( cfg.cb_syserror );
						}
					},
					error:function( XMLHttpRequest , textStatus )
					{
						var msg = "系统错误";
						try
						{
							var data = eval( '('+XMLHttpRequest.response+')' );
							msg = data.MSG;
						}
						catch( e )
						{
							
						}
						//执行失败回调函数
						if( cfg.cb_error != '' )
							eval( cfg.cb_syserror );
					}
			});
		};
	return ajaxOp;
};

/**
 * 切换验证码
 */
$('#captchaImg').on('click', function() {
    var _this = $(this),
        url = _this.data('url');
    if (url.indexOf('?') > -1) {
        url += '&random=';
    } else {
        url += '?random=';
    }
    _this.attr('src', url + Math.random());
});
