<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php if($state) { ?>
<?php $tmp="完结";?>
<?php } else { ?>
<?php $tmp="连载";?>
<?php } ?>
<?php $SEO['title']="漫画_".$tmp."漫画_最新".$tmp."漫画_".$tmp."在线漫画-漫画城";?>
<?php $SEO['keyword']="漫画,".$tmp."漫画,最新".$tmp."漫画,".$tmp."在线漫画，好看的".$tmp."漫画";?>
<?php $SEO['description']="漫画城在线漫画网为您提供最全、最新的".$tmp."漫画、最新".$tmp."漫画、".$tmp."在线漫画、好看的".$tmp."漫画，漫画城在线漫画网是国内更新速度最快、漫画资源最全的专业在线漫画网站！";?>
<?php $footer_txt = "漫画城".$tmp."频道为您提供最全、最新的".$tmp."漫画、最新".$tmp."漫画、".$tmp."在线漫画、好看的".$tmp."漫画，漫画城在线漫画网是国内更新速度最快、漫画资源最全的专业漫画网站！"?>

<?php include template("content","header"); ?>
<?php include template("content","mh_nav"); ?>

	<div class="left c666">您现在的位置：&nbsp;<a class="LinkPath" href="/">首页</a>&nbsp;&gt;&nbsp;<a class="LinkPath" href="/manhua">在线漫画</a>&nbsp;&gt;&nbsp;<?php echo $tmp;?>漫画列表</div>
    <div class="middle_div1">
    	<div class="middle_div1_le" style="border:1px solid #AEDD5E">
        	<div class="jingdian_tit">
            	<i class="icon"></i>
            	<h3><?php echo $tmp;?>漫画列表</h3>
            </div>
            <div class="jingdian_txt">
            	<ul>
				<?php $w=$where." AND state=$state"?>
			 <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=d763560b3486eae4323eb850d9ea8631&action=lists&catid=13&where=%24w&num=16&order=updatetime+DESC&page=%24page&moreinfo=1\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$pagesize = 16;$page = intval($page) ? intval($page) : 1;if($page<=0){$page=1;}$offset = ($page - 1) * $pagesize;$content_total = $content_tag->count(array('catid'=>'13','where'=>$w,'order'=>'updatetime DESC','moreinfo'=>'1','limit'=>$offset.",".$pagesize,'action'=>'lists',));$pages = pages($content_total, $page, $pagesize, '');$data = $content_tag->lists(array('catid'=>'13','where'=>$w,'order'=>'updatetime DESC','moreinfo'=>'1','limit'=>$offset.",".$pagesize,'action'=>'lists',));}?>			
			 <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
				<li>
                       <a target="_blank" title="<?php echo $r['title'];?>" href="/manhua<?php echo $r['id'];?>"><img width='140' height='170' src="<?php echo $r['thumb'];?>"></a>
                       <a target="_blank" title="<?php echo $r['title'];?>" href="/manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],35,'');?></a>
                </li>
			 <?php $n++;}unset($n); ?>
                </ul>
            </div>
            <div id="search_fy" class="sy_tbb" style="margin:0;"><?php echo $pages;?></div>
        </div>
		<?php include template("content","block_right"); ?>
</div>

<?php include template("content","footer"); ?>