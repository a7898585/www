<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php include template("content","header"); ?>

<div class="mainborder mar">
     <div class="mainbox">
      <div class="title">

        <div class="title_left"></div>
        <div class="title_mid title3"><span id="result_box"><span title="" closure_uid_16t2ze="97" lc="新闻动态" mc="新闻动态">新闻动态</span></span></div>
        <div class="title_right"></div>
      </div>
      <div class="content_box box5">
	   <ul>	    
		<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=11e982198282a77f17ebf8d7a1dede5e&action=lists&catid=%24catid&num=10&order=id+DESC&page=%24page\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$pagesize = 10;$page = intval($page) ? intval($page) : 1;if($page<=0){$page=1;}$offset = ($page - 1) * $pagesize;$content_total = $content_tag->count(array('catid'=>$catid,'order'=>'id DESC','limit'=>$offset.",".$pagesize,'action'=>'lists',));$pages = pages($content_total, $page, $pagesize, '');$data = $content_tag->lists(array('catid'=>$catid,'order'=>'id DESC','limit'=>$offset.",".$pagesize,'action'=>'lists',));}?>
		<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
		<li><a href="<?php echo $r['url'];?>" target="_blank" class="style1"><?php echo $r['title'];?></a><span></span></li>
		<?php $n++;}unset($n); ?>
		<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
        </ul>
      <div class="page"><?php echo $pages;?></div>
      </div>
      <div class="box_foot">

        <div class="foot_left"></div>
        <div class="foot_mid title3"></div>
        <div class="foot_right"></div>
      </div>
    </div>
</div>
<?php include template("content","footer"); ?>