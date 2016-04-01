<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
		<meta name="Keywords" content="<?php echo $this->getSeoKeyword();?>" />
		<meta name="Description" content="<?php echo $this->getSeoDesc();?>" />
		<title><?php echo $this->getSeoTitle();?></title>

		<link rel="stylesheet" type="text/css" href="<?php echo $this->baseUrl;?>/css/bootstrap.min.css"/>
		<link rel="stylesheet" type="text/css" href="<?php echo $this->baseUrl;?>/css/index.css"/>
		<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl;?>/js/jquery.min.js"></script>
		<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl;?>/js/bootstrap.min.js"></script>
		<script language="javascript" type="text/javascript">
			var WEB_PATH = "<?php echo $this->baseUrl;?>";
			var WEB_DOMAIN = "<?php echo $_SERVER['HTTP_HOST'] ?>";
		</script>
	</head>

	<body>
		<?php include NBT_VIEW_PATH.'/layouts/_header.php';?>
		<?php $this->widget('EWidgetSessionTipMsg'); ?>
		<?php echo $content;?>	    	
		<?php include NBT_VIEW_PATH.'/layouts/_footer.php';?>
		<?php include NBT_VIEW_PATH.'/systems/debug.php';?>
		<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl;?>/js/base.js"></script>
	</body>
</html>
