<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php include template('content', 'header'); ?>
<!--main-->
<div class="main special-channel">
	<div class="col-left">
        <div class="box-hots">
            <div class="icon"></div>
            <div class="content">
            	<div class="special-slide">
				<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"special\" data=\"op=special&tag_md5=200cc4bc5136111515e4fd8a0e24ca95&action=lists&elite=1&listorder=3&num=2\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">修改</a>";}$special_tag = pc_base::load_app_class("special_tag", "special");if (method_exists($special_tag, 'lists')) {$data = $special_tag->lists(array('elite'=>'1','listorder'=>'3','limit'=>'2',));}?>
                    <div class="changeDiv">
					<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                        <a href="<?php echo $r['url'];?>"><img src="<?php echo thumb($r['thumb'], 224, 112);?>" width="224" height="112" alt="<?php echo $r['title'];?>" /><p><strong><?php echo $r['title'];?></strong><br />
<?php echo $r['description'];?></p></a>
					<?php $n++;}unset($n); ?>
                    </div>
				<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                    <div class="change">
                        <i class="cur">1</i>
                        <i>2</i>
                    </div>
                </div>
<script type="text/javascript"> 
$(function(){
	new slide(".special-slide","cur",674,126);
})
</script>
<div class=" bk30 hr"></div>
<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"special\" data=\"op=special&tag_md5=013538320193e9db110d27269c228942&action=lists&elite=1&listorder=3\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">修改</a>";}$special_tag = pc_base::load_app_class("special_tag", "special");if (method_exists($special_tag, 'lists')) {$data = $special_tag->lists(array('elite'=>'1','listorder'=>'3','limit'=>'20',));}?>
<ul class="list row-2 f14 lh24">
	<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
    <li>·<a href="<?php echo $r['url'];?>" target="_blank"><?php echo str_cut($r['title'], 42);?></a></li>
   <?php $n++;}unset($n); ?>
</ul>
<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
</div>
        </div>
        <div class="bk10"></div>
        <div class="box">
        		<h5>专题</h5>
<div class="pad-lr-10">
<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"special\" data=\"op=special&tag_md5=fa5e925658420c04935751801fd80010&action=lists&listorder=3\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">修改</a>";}$special_tag = pc_base::load_app_class("special_tag", "special");if (method_exists($special_tag, 'lists')) {$data = $special_tag->lists(array('listorder'=>'3','limit'=>'20',));}?>
    <ul class="list row-2 f14 lh24">
	<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
		<li>·<a href="<?php echo $r['url'];?>" target="_blank"><?php echo str_cut($r['title'], 42);?></a></li>

	<?php $n++;}unset($n); ?>
    </ul>
<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
</div>
        </div>
    </div>
<div class="col-auto">

    <div class="box">
            <h5 class="title-2">一周点击排行</h5>
			<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"special\" data=\"op=special&tag_md5=bdad7c63981c9c2fe37b04f1087ad631&action=hits&num=10&order=views+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">修改</a>";}$special_tag = pc_base::load_app_class("special_tag", "special");if (method_exists($special_tag, 'hits')) {$data = $special_tag->hits(array('order'=>'views DESC','limit'=>'10',));}?>
            <ul class="content digg">
				<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                <li><a href="<?php echo $r['url'];?>" target="_blank"><?php echo $r['title'];?></a></li>
            <?php $n++;}unset($n); ?>
            </ul>
			<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
    </div>
    <div class="bk10"></div>
    <div class="box">
            <h5 class="title-2">一周评论排行</h5>
			<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"comment\" data=\"op=comment&tag_md5=f96eff7c12a07eee88c5cd43c712bf7e&action=bang&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">修改</a>";}$tag_cache_name = md5(implode('&',array()).'f96eff7c12a07eee88c5cd43c712bf7e');if(!$data = tpl_cache($tag_cache_name,3600)){$comment_tag = pc_base::load_app_class("comment_tag", "comment");if (method_exists($comment_tag, 'bang')) {$data = $comment_tag->bang(array('limit'=>'20',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
            <ul class="content digg">
				<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                <li><a href="<?php echo $r['url'];?>" target="_blank"><?php echo $r['title'];?></a></li>
            <?php $n++;}unset($n); ?>
            </ul>
			<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
    </div>
</div>

</div>
<?php include template('content', 'footer'); ?>