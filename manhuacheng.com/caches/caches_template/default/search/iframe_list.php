<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title><?php if(isset($SEO['title']) && !empty($SEO['title'])) { ?><?php echo $SEO['title'];?><?php } ?><?php echo $SEO['site_title'];?></title>
<meta name="keywords" content="<?php echo $SEO['keyword'];?>">
<meta name="description" content="<?php echo $SEO['description'];?>">
<link href="<?php echo CSS_PATH;?>css.css" rel="stylesheet" type="text/css" />

<style>
body{background:none;}
.middle_div1_le {border:none}
.middle_div1_le .xg_mh .xg_mh_txt .mh_copy {border:none}
.middle_div1_le .xg_dm .xg_dm_txt .dm_copy {border:none}
</style>
</head>
<body>

<div class ="middle_div1_le" >

<?php if($typeid == 18) { ?>
	<div class ="xg_mh">
		<div class ="xg_mh_txt">
			<div class="mh_copy">
				<ul>
					<?php $n=1; if(is_array($data)) foreach($data AS $i => $r) { ?>
					<li>
						<a title="<?php echo strip_tags($r[title]);?>" href="manhua<?php echo $r['id'];?>" target="_blank"><img src="<?php echo $r['thumb'];?>" width="100" height="120"></a>
						<a title="<?php echo strip_tags($r[title]);?>" href="manhua<?php echo $r['id'];?>" target="_blank"><?php echo $r['title'];?><?php if($r['posids']) { ?><img src="/statics/images/icon/small_elite.gif" title="推荐"><?php } ?></a>
					</li>
					<?php $n++;}unset($n); ?>
					<?php if(empty($data)) { ?>未找到结果<?php } ?>
				</ul>
			</div>
		</div>  
	</div>
<?php } elseif ($typeid == 21) { ?>
	<div class ="xg_dm">
		<div class ="xg_dm_txt">
			<div class="dm_copy">
				<ul>
					<?php $n=1; if(is_array($data)) foreach($data AS $i => $r) { ?>
					<li>
						<a title="<?php echo strip_tags($r[title]);?>" href="dongman<?php echo $r['id'];?>" target="_blank"><img src="<?php echo $r['thumb'];?>" width="120" height="90"></a>
						<a title="<?php echo strip_tags($r[title]);?>" href="dongman<?php echo $r['id'];?>" target="_blank"><?php echo $r['title'];?><?php if($r['posids']) { ?><img src="/statics/images/icon/small_elite.gif" title="推荐"><?php } ?></a>
					</li>
					<?php $n++;}unset($n); ?>	
					<?php if(empty($data)) { ?>未找到结果<?php } ?>
				</ul>
			</div>

		</div>  
	</div>
<?php } elseif ($typeid == 24) { ?>
	<div class ="xg_dm">
		<div class ="zx">
			<div class ="zx_copy">
				<div class="zx_copy_txt">
					<ul>
						<?php $n=1; if(is_array($data)) foreach($data AS $i => $r) { ?>
						<li><a title="<?php echo strip_tags($r[title]);?>" href="<?php echo $r['url'];?>" target="_blank"><?php echo $r['title'];?></a></li>
						<?php $n++;}unset($n); ?>
						<?php if(empty($data)) { ?>未找到结果<?php } ?>
					</ul>
				</div>
			</div>  
		</div>
	</div>
<?php } ?>

	<div class="ad02 page" id="search_fy"> 
		<?php echo $pages;?>
	</div>
</div>                       
</body>
</html>