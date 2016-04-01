<?php
/**
 * 验证码
 * 
 */
class CaptchaController extends BaseController
{
	
	public function init()
	{
		
	}
	
	/**
	 * 显示验证码
	 *
	 */
	public function actionIndex()
	{
		$widgetCaptchaRenderImage = new CWidgetCaptchaRenderImage();
		$widgetCaptchaRenderImage->run();
	}
	
//end class
}
