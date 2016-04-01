<?php if( NBT_DEBUG ):?>
<div class="debug_time">
	Time:<?php echo number_format( microtime(true) - NBT_BEGIN_MICROTIME , 3 );?>(seconds)
	Memory:<?php echo number_format( memory_get_usage()/(1024*1024) , 2 );?>(M)
</div>
<div class="debug_sql">	
	<?php foreach( (array)Nbt::$aryCollectSql as $v ):?>
	<p><?php echo CHtml::encode($v);?></p>
	<?php endforeach;?>
</div>
<?php endif;?>
