<?php
/**
 * 邮件类封装
 *
 * @author wengebin
 * @package framework
 * @date 2013-12-18
 */
class CMailer extends CApplicationComponents 
{
	/**
	 * 初始化
	 */
	public function init()
	{
		include( NBT_APPLICATION_PATH.'/framework/phpmailer/PHPMailerAutoload.php' );
		parent::init();
	}

	/**
	 * 发送单封邮件
	 *
	 * @param string $_strAddress 发送到邮件地址
	 * @param string $_strName 对方的称呼
	 * return bool;
	 */
	public function sendEmail( $_strAddress = '' , $_strName = '' , $_strTitle = '' , $_strContent = '' )
	{
		$mail = new PHPMailer;

		$mail->isSMTP();
		$mail->Host = SMTP_HOST;
		$mail->Port = SMTP_PORT;
		$mail->SMTPSecure = SMTP_SECURE;
		$mail->SMTPAuth = true;
		$mail->Username = SMTP_ACCOUNT;
		$mail->Password = SMTP_PASSWORD;
		//$mail->SMTPSecure = 'tls';

		$mail->From = SMTP_ACCOUNT;
		$mail->FromName = SMTP_ACCOUNT_NAME;

		if ( !empty( $_strName ) )
			$mail->addAddress($_strAddress, $_strName);
		else
			$mail->addAddress($_strAddress);

		// 回复到哪个邮件地址
		//$mail->addReplyTo('wengebin@hotmail.com', 'hotmail');
		// 添加抄送
		//$mail->addCC('wengebin@hotmail.com');
		// 添加密送
		//$mail->addBCC('woondroo@hotmail.com');

		$mail->WordWrap = 50;
		// 添加附件
		//$mail->addAttachment('/var/tmp/file.tar.gz');
		// 添加附件
		//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');
		$mail->isHTML(true);

		$mail->Subject = $_strTitle;
		$mail->Body    = $_strContent;
		$mail->AltBody = strip_tags( $_strContent );

		$boolResult = false;
		if( $mail->send() )
			$boolResult = true;

		return $boolResult;
	}

//end class
}
