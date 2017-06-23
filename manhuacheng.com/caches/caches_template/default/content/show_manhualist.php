<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<link href="<?php echo CSS_PATH;?>css.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo JS_PATH;?>change_tag.js"></script>

<style>
body{background:none;}
.new_img {
  position: absolute;
  left: 60px;
}

.mh_copy1{
	position: relative;
}
.middle_div3_ri .middle_ten .middle_ten_le .mos .mos_txt {
	width:530px;
}
#search_fy a:link, #search_fy a:visited {margin:0}
</style>
</head>
<body>  
<div id="ifreame_height" class ="middle_div3_ri" style="float:none">
<div style="margin-top:0px;" class="today">
		<div class="today_tit">
		<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=e82cb5ee2e82757fcf15cc9ecf21f9d4&action=lists&catid=14&manhuaid=%24id&num=9999&order=id+asc\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('catid'=>'14','manhuaid'=>$id,'order'=>'id asc','limit'=>'9999',));}?>
		<?php $mh_list = array_chunk($data,100);?>
			<ul id="tab_list"> 
				<?php $n=1; if(is_array($mh_list)) foreach($mh_list AS $key => $r) { ?>
				<?php $key ++?>
				<?php $start= current($r);?>
				<?php $end= end($r);?>
				<li class="<?php if(($key == 1)) { ?>on<?php } ?>" onmouseover="secBoard('tab_list','infolist_list',<?php echo $key;?>,'on','');GetSize();"><strong><?php echo $start['title'];?>-<?php echo $end['title'];?></strong></li>         
				<?php $n++;}unset($n); ?>
			</ul>
		</div>
		<div class="today_t">
		<?php $n=1; if(is_array($mh_list)) foreach($mh_list AS $key => $v) { ?>
			<?php $key++?>
			<ul id="infolist_list_<?php echo $key;?>" class="nr6 lan2" style="display:<?php if(($key == 1)) { ?>block<?php } else { ?>none<?php } ?>">		
				<?php $n=1;if(is_array($v)) foreach($v AS $val) { ?>
					<li>
						<a title="<?php echo $val['title'];?>" id="<?php echo $val['id'];?>" href="#" target="_blank"><?php echo $val['title'];?></a>
					</li>  
				<?php $n++;}unset($n); ?>
			</ul>
		<?php $n++;}unset($n); ?>
		</div>
		<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
	</div>
</div>
</body>
<script type="text/javascript">
	function GetSize() {  
		//自动调节窗口的大小  
		var vHeight = document.body.offsetHeight;  
		//alert(vHeight);
		window.parent.SetHeight(vHeight);
	}  
</script>
</html>