<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php include template("content","header"); ?>
<link href="<?php echo CSS_PATH;?>link.css" rel="stylesheet" type="text/css" />
<!--main-->
<div class="main">
	<!--left_bar-->
	<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"link\" data=\"op=link&tag_md5=42e45ff679875fd1ef9d33748ef70002&action=get_type&typeid=%24type_id\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$link_tag = pc_base::load_app_class("link_tag", "link");if (method_exists($link_tag, 'get_type')) {$data = $link_tag->get_type(array('typeid'=>$type_id,'limit'=>'20',));}?>
     <?php $type_arr = $data;?>
  	 <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
	<div class="col-left"> 
	<div class="left c666" style="padding-top:5px;">您现在的位置：&nbsp;<a class="LinkPath" href="<?php echo siteurl();?>">首页</a>&nbsp;&gt;&nbsp;<a class="LinkPath" href="/link">友情链接</a>

    <div class="box boxsbg cboxs flink">
		<div class="mhc_tit"><h3>友情链接</h3></div>
			<div class="tag_a">
        	<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"link\" data=\"op=link&tag_md5=f84b2581a06205cba02520ada49ee39d&action=lists&typeid=%24type_id&linktype=1&order=desc&num=6&return=dat\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$link_tag = pc_base::load_app_class("link_tag", "link");if (method_exists($link_tag, 'lists')) {$dat = $link_tag->lists(array('typeid'=>$type_id,'linktype'=>'1','order'=>'desc','limit'=>'6',));}?>
				<?php $n=1;if(is_array($dat)) foreach($dat AS $v) { ?>
	       		 <a href="<?php echo $v['url'];?>" title="<?php echo $v['name'];?>"  target="_blank"><img src="<?php echo $v['logo'];?>" width="92" height="31" /></a>
		        <?php $n++;}unset($n); ?>
			<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
         </div>
        <div class="tag_a">
        	<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"link\" data=\"op=link&tag_md5=09ac96400f8c307149a75ce25735fcd0&action=lists&typeid=%24type_id&linktype=0&order=desc&num=10&return=dat\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$link_tag = pc_base::load_app_class("link_tag", "link");if (method_exists($link_tag, 'lists')) {$dat = $link_tag->lists(array('typeid'=>$type_id,'linktype'=>'0','order'=>'desc','limit'=>'10',));}?>
				<?php $n=1;if(is_array($dat)) foreach($dat AS $v) { ?>
	       		 <a href="<?php echo $v['url'];?>" title="<?php echo $v['name'];?>" target="_blank"><?php echo $v['name'];?> </a>
		        <?php $n++;}unset($n); ?>
			<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
         </div>
    	 </div>
         	<!--pages-->
       </div>
    <!--right_bar-->
</div>
<?php include template("content","footer"); ?>
