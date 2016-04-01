<div class="jumbotron">
    <form class="form-signin" role="form" method="POST" action="<?php echo $this->createUrl('login/index');?>"
    id="loginForm" name="loginForm" onsubmit="return validateLogin()">
        <input type="hidden" name="gourl" value="<?php echo $gourl; ?>" />
        <div id="loginDiv" class="validateDiv input-area">
            <?php echo CHtml::errorModelSummery( $model );?>
                <input class="form-control required" title="<?php echo CUtil::i18n('vlogin,username')?>" 
                       placeholder="<?php echo CUtil::i18n('vlogin,username')?>" name="username"
                id="username" value="<?php echo $aryData['username'];?>" type="text" />
                <input class="form-control required" title="<?php echo CUtil::i18n('vlogin,password')?>" 
                       placeholder="<?php echo CUtil::i18n('vlogin,password')?>" name="password"
                id="password" type="password" />
                <div id="lgcc" style="<?php echo empty($iscaptcha) ? 'display:none' : ''; ?>">
                    <input class="form-control" title="<?php echo CUtil::i18n('vlogin,securityCode')?>" 
                           placeholder="<?php echo CUtil::i18n('vlogin,securityCode')?>" name="captcha"
                    type="text" id="captcha" autocomplete="off" />
                    <div id="captcha-container">
                        &nbsp;&nbsp;&nbsp;
                        <?php $this->
                            widget('CWidgetCaptcha' , array( 'imageOptions'=>array( 'align'=>'absmiddle'
                            ) ));?>
                    </div>
                    <br>
                    <br>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">
                    <?php echo CUtil::i18n('vlogin,login')?>
                </button>
                <p class="link-to-minilogin">
                    <?php echo CUtil::i18n('vlogin,noAccountYet')?>
                    <a href="<?php echo $this->createUrl('regist/index'); ?>">
                        <?php echo CUtil::i18n('vlogin,registerNow')?>
                    </a>
                    &nbsp;&nbsp;&nbsp;&nbsp;<?php echo CUtil::i18n('vlogin,or')?>
                    <a href="<?php echo $this->createUrl('reset/forget'); ?>">
                        <?php echo CUtil::i18n('vlogin,forgetPassword')?>
                    </a>
                    ？
                </p>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#username').blur(function() {
            validateNeedCheckCode();
        });
    });

    function validateLogin() {
        var ret = true;

        var msg = '<div class="alert alert-danger">';
        $('input.required').each(function() {
            if (ret === true && $(this).val().trim() == '') {
                msg += '<div><?php echo CUtil::i18n('vlogin,pleaseEnter')?>' + $(this).attr('title') + '</div>';
                ret = false;
            }
        });

        msg += "</div>";

        if (ret == false) {
            $("#loginDiv").find('div.alert-danger').remove();
            $("#loginDiv").prepend(msg);
        }
        return ret;
    }

    var sn = ajaxCbData.sn();
    function validateNeedCheckCode() {
        var un = $('#username').val();
        if (un.trim() == '') return;
        var objAjax = ajax.init().setUrl("<?php echo $this->createUrl( 'login/check' , array( 't'=>rand() ) ); ?>")
                .setData({"un": un
        }).setSn(sn).setCbSuccess("cbSuccess()").setCbError("cbError()").setIsTip(false).preRun().run();
    }

    /**
 * 成功回调函数
 */
    function cbSuccess() {
        if (ajaxCbData.get(sn).DATA.isneed == 1) {
            $('#lgcc').css({
                'display': 'block'
            });
        } else {
            $('#lgcc').css({
                'display': 'none'
            });
        }
    }

    /**
 * 失败回调函数
 */
    function cbError() {
        //alert( ajaxCbData.get( sn ).MSG );
    }
</script>
