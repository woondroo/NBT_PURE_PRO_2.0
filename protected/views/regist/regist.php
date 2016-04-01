  <div class="jumbotron">
    <form class="form-signin" role="form" method="POST" action="<?php echo $this->createUrl('regist/index');?>" id="registForm" name="registForm" onsubmit="return validateRegist()">
      <div id="registDiv" class="validateDiv input-area">
        <?php echo CHtml::errorModelSummery( $model );?>
        <input class="form-control required" title="<?php echo CUtil::i18n('vregist,username')?>" 
               placeholder="<?php echo CUtil::i18n('vregist,username')?>" name="username" value="<?php echo $aryData['username'];?>" type="text" autocomplete="off" />
        <input class="form-control required" title="<?php echo CUtil::i18n('vregist,password')?>" 
               placeholder="<?php echo CUtil::i18n('vregist,password')?>" name="password" type="password" autocomplete="off" />
        <input class="form-control required" title="<?php echo CUtil::i18n('vregist,securityCode')?>" 
               placeholder="<?php echo CUtil::i18n('vregist,securityCode')?>" name="captcha" type="text" id="captcha" autocomplete="off"/><div id="captcha-container">&nbsp;&nbsp;&nbsp;<?php $this->widget('CWidgetCaptcha' , array( 'imageOptions'=>array( 'align'=>'absmiddle' ) ));?></div>
        <br><br>
        <button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo CUtil::i18n('vregist,regist')?></button>
        <p class="link-to-minilogin">
        <?php echo CUtil::i18n('vregist,haveAccount')?> <a href="<?php echo $this->createUrl('login/index'); ?>">
            <?php echo CUtil::i18n('vregist,logNow')?></a>
        </p>
      </div>
    </form>
  </div>
  <script type="text/javascript">
  function validateRegist()
  {
  	var msg = '<div class="alert alert-danger">';
    var ret = true;
  	if ( ret === true )
  	{
  		$('input.required').each(function(){
  			if ( ret === true && $(this).val().trim() == '' )
  			{
  				msg += '<div><?php echo CUtil::i18n('vregist,pleaseEnter')?> '+$(this).attr('title')+'</div>';
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
