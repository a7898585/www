<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><div class="rs_dm">
	<div class="rs_dm_tit">
	<h3>漫画点击排行榜</h3>
	<ul static="1600&amp;tn=indsa&amp;bl=index_top_search&amp;stp=tab" class="tang-title"  id="tab_rank">
		<li onmouseover="secBoard('tab_rank','ranklist_a',1,'tang-title-item tang-title-item-selected','tang-title-item first');" class="tang-title-item tang-title-item-selected"><a hidefocus="true" href="javascript:;">总</a><div class="jiantou"></div></li>
		<li onmouseover="secBoard('tab_rank','ranklist_a',2,'tang-title-item tang-title-item-selected','tang-title-item first');" class="tang-title-item first"><a hidefocus="true" href="javascript:;">月</a><div class="jiantou"></div></li>
		<li onmouseover="secBoard('tab_rank','ranklist_a',3,'tang-title-item tang-title-item-selected','tang-title-item first');" class="tang-title-item first"><a hidefocus="true" href="javascript:;">周</a><div class="jiantou"></div></li>
		<li onmouseover="secBoard('tab_rank','ranklist_a',4,'tang-title-item tang-title-item-selected','tang-title-item first');" class="tang-title-item first"><a hidefocus="true" href="javascript:;">日</a><div class="jiantou"></div></li>	
	</ul>
</div>
<div class="rs_dm_txt">
<?php $where = "type_id NOT IN(".dimplode(dexplode(NOT_ALLOW)).")";?>		
<?php $class= array(1=>'first',2=>'second',3=>'third')?>
	<ul id="ranklist_a_1" style="display:block" >
		<?php $i=1;?>
		<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=878fe84dc1118fb6ea3cf096c4a0fd2e&action=hits2&catid=13&num=10&where=%24where&order=views+DESC&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('catid'=>'13','where'=>$where,'order'=>'views DESC',)).'878fe84dc1118fb6ea3cf096c4a0fd2e');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'hits2')) {$data = $content_tag->hits2(array('catid'=>'13','where'=>$where,'order'=>'views DESC','limit'=>'10',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
		<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
				<li><i target="_blank" class="<?php echo $class[$i];?>"><?php echo $i;?></i><a href="/manhua<?php echo $r['id'];?>/"><?php echo str_cut($r[title],25,"");?></a></li>
			<?php $i++?>
		<?php $n++;}unset($n); ?>
		<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
	</ul>
	<ul id="ranklist_a_2" style="display:none">
		<?php $i=1;?>
		<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=26173fb6e16d7538d107652f9ab54c83&action=hits2&catid=13&num=10&where=%24where&order=monthviews+DESC&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('catid'=>'13','where'=>$where,'order'=>'monthviews DESC',)).'26173fb6e16d7538d107652f9ab54c83');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'hits2')) {$data = $content_tag->hits2(array('catid'=>'13','where'=>$where,'order'=>'monthviews DESC','limit'=>'10',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
		<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
				<li><i target="_blank" class="<?php echo $class[$i];?>"><?php echo $i;?></i><a href="/manhua<?php echo $r['id'];?>/"><?php echo str_cut($r[title],25,"");?></a></li>
			<?php $i++?>
		<?php $n++;}unset($n); ?>
		<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
	</ul>

	<ul id="ranklist_a_3" style="display:none">
		<?php $i=1;?>
		<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=04afc832643a9da070d3339687c7b75e&action=hits2&catid=13&num=10&where=%24where&order=weekviews+DESC&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('catid'=>'13','where'=>$where,'order'=>'weekviews DESC',)).'04afc832643a9da070d3339687c7b75e');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'hits2')) {$data = $content_tag->hits2(array('catid'=>'13','where'=>$where,'order'=>'weekviews DESC','limit'=>'10',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
		<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
				<li><i target="_blank" class="<?php echo $class[$i];?>"><?php echo $i;?></i><a href="/manhua<?php echo $r['id'];?>/"><?php echo str_cut($r[title],25,"");?></a></li>
			<?php $i++?>
		<?php $n++;}unset($n); ?>
		<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
	</ul>

	<ul id="ranklist_a_4" style="display:none">
		<?php $i=1;?>
		<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=b628dc8276ebba503338679b92f156c8&action=hits2&catid=13&num=10&where=%24where&order=dayviews+DESC&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('catid'=>'13','where'=>$where,'order'=>'dayviews DESC',)).'b628dc8276ebba503338679b92f156c8');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'hits2')) {$data = $content_tag->hits2(array('catid'=>'13','where'=>$where,'order'=>'dayviews DESC','limit'=>'10',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
		<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
				<li><i target="_blank" class="<?php echo $class[$i];?>"><?php echo $i;?></i><a href="/manhua<?php echo $r['id'];?>/"><?php echo str_cut($r[title],25,"");?></a></li>
			<?php $i++?>
		<?php $n++;}unset($n); ?>
		<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
	</ul>
</div>
</div>