	<div class="page-header">
		<h1>重置密码</h1>
	</div>
	<div class="jumbotron">
		<form class="form-signin" role="form" method="POST" action="<?php echo $this->createUrl('reset/resetPwd',array('key'=>$_GET['key']));?>" id="updateForm" name="updateForm" onsubmit="return validateForm()">
			<div id="registDiv" class="validateDiv input-area">
				<?php echo CHtml::errorModelSummery( $model ); ?>
				<div class="input-group">
					<span class="input-group-addon glyphicon glyphicon-lock"></span>
					<input title="新密码" placeholder="新密码" class="form-control required" autocomplete="off" type="password" name="u_newpassword" value="">
				</div>
				<div class="input-group">
					<span class="input-group-addon glyphicon glyphicon-lock"></span>
					<input title="确认密码" placeholder="确认密码" class="form-control required" autocomplete="off" type="password" name="u_repassword" value="">
				</div>
				<button class="btn btn-lg btn-primary btn-block" type="submit">重置密码</button>
			</div>
		</form>
	</div>
	<script type="text/javascript">
	function validateForm()
	{
		var msg = '<div class="alert alert-danger">';
		var ret = true;
		if ( ret === true )
		{
			$('input.required').each(function(){
				if ( ret === true && $(this).val().trim() == '' )
				{
					msg += '<div>请输入'+$(this).attr('title')+'</div>';
					ret = false;
				}
			});
		}
		
		msg += "</div>";

		if( ret == false )
		{
			$("#registDiv").find('div.alert-danger').remove();
			$("#registDiv").prepend( msg );    
		}

		return ret;
	}
	</script>
