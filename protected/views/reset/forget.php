	<div class="page-header">
		<h1>修改密码</h1>
	</div>
	<div class="jumbotron">
		<div id="registDiv" class="validateDiv input-area form-signin">
			<div id="safeContainer"></div>
			<div class="input-group">
				<span class="input-group-addon glyphicon glyphicon-envelope"></span>
				<input title="邮箱地址" placeholder="邮箱地址" class="form-control required" autocomplete="off" type="text" id="u_email" name="u_email" />
				<span class="input-group-btn">
					<button class="btn btn-default customer-input-btn" id="send-mail-btn" type="button">修改密码</button>
				</span>
			</div>
		</div>
	</div>
<script type="text/javascript">
$('#send-mail-btn').click(function(){
	sendMail();
});

function validateForm()
{
	$("#safeContainer").find('div.alert-danger').remove();
	$("#safeContainer").find('div.alert-success').remove();

	var ret = true;

	var msg = '<div class="alert alert-danger">';
	$('input.required').each(function(){
		if ( ret === true && $(this).val().trim() == '' )
		{
			msg += '<div>请输入'+$(this).attr('title')+'</div>';
			ret = false;
		}
	});

	var mailAddr = $('#u_email').val();
	var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	var regResult = regex.test( mailAddr );
	if ( ret == true && regResult == false )
	{
		msg += '<div>邮箱地址不符合要求！</div>';
		ret = false;
	}

	msg += "</div>";

	if( ret == false )
		$("#safeContainer").prepend( msg );

	return ret;
}

var sn = ajaxCbData.sn();
var issend = false;
function sendMail()
{
	if ( issend == true )
		return;

	var validateResult = validateForm();
	if ( validateResult == false )
		return;

	issend = true;
	$('#u_email').attr('disabled','true');
	$('#send-mail-btn').html('发送邮件');

	var mail = $('#u_email').val();
	var objAjax = ajax.init()
		.setUrl( "<?php echo $this->createUrl( 'reset/sendReset' , array( 't'=>rand() ) ); ?>" )
		.setData( {"e":mail} )
		.setSn(sn) 
		.setCbSuccess("cbSuccess()")
		.setCbError("cbError()")
		.setIsTip(false)
		.preRun()
		.run();
}

/**
 * 成功回调函数
 */
function cbSuccess()
{
	var data = ajaxCbData.get( sn );
	if ( data.ISOK == 1 )
	{
		$('#send-mail-btn').attr('disabled','true').html('发送成功');
		$("#safeContainer").prepend( '<div class="alert alert-success">邮件发送成功！</div>' );
	}
	else
	{
		issend = false;
		$('#send-mail-btn').html('失败,重新发送');
		$('#u_email').removeAttr('disabled');
		$("#safeContainer").prepend( '<div class="alert alert-danger">'+data.MSG+'</div>' );
	}
}

/**
 * 失败回调函数
 */
function cbError()
{
	issend = false;
	$('#send-mail-btn').html('失败,重新发送');
	$('#u_email').removeAttr('disabled');

	var msg = ajaxCbData.get( sn ).MSG;
	$("#safeContainer").prepend( '<div class="alert alert-danger">'+( msg == '' ? '邮件发送失败！' : msg )+'</div>' );
}
</script>
