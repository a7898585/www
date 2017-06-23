<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php $m = getboxname('12','type_id');?>
<?php $rg = getboxname('12','region');?>
<style>
.jq {
	height:25px;
}
.jq h3{
	height:20px;
}
</style>
<div class="x_nav1">
    	<div class="jq"><h3>地  区：</h3>
			<?php $n=1; if(is_array($rg)) foreach($rg AS $n => $l) { ?>
					<a href="/mh_diqu<?php echo $n;?>/" ><?php echo $l;?></a>&nbsp;|
			<?php $n++;}unset($n); ?>
		</div>
		<div class="jq"><h3>类  型：</h3>
			<a href="/lianzai" style="color:#FF0000">热门连载</a>&nbsp;|
			<a href="/wanjie"  style="color:#FF0000">完结漫画</a>&nbsp;|
			<?php $n=1; if(is_array($m)) foreach($m AS $n => $l) { ?>
				<?php if(!in_array($n,dexplode(NOT_ALLOW))) { ?>
					<a href="/type<?php echo $n;?>/" ><?php echo $l;?></a>&nbsp;|
				<?php } ?>
			<?php $n++;}unset($n); ?>
		</div>
    	<div class="jq"><h3>字母索引：</h3>
			<?php $Q=NULL;?>
			<?php for ($i=65; $i<=90; $i++) $Q[]=chr($i);?>
			<?php $n=1;if(is_array($Q)) foreach($Q AS $r) { ?>
				<a href="/index<?php echo $r;?>/"><?php echo $r;?></a>&nbsp;|
			<?php $n++;}unset($n); ?>
		</div>
</div>