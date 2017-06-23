<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><div class="x_nav1">
    	<div class="jq"><h3>剧  情：</h3>
			<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=c1950da82c3ca4691469dee4edcf4fb9&action=category&catid=32&order=listorder+ASC&return=catlist\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'category')) {$catlist = $content_tag->category(array('catid'=>'32','order'=>'listorder ASC','limit'=>'20',));}?>
			  <?php $n=1;if(is_array($catlist)) foreach($catlist AS $r) { ?>
				<a href="<?php echo $r['url'];?>" ><?php echo $r['catname'];?></a>&nbsp;|
			  <?php $n++;}unset($n); ?>
			<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
		</div>
		<div class="nd"><h3>地  区：</h3>
			 <?php $sql = "select linkageid,name from tt_linkage where keyid=3360 order by listorder desc";?>
				<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"get\" data=\"op=get&tag_md5=32bd45650461350abd1f2dcfcd9ad79d&sql=%24sql&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('sql'=>$sql,)).'32bd45650461350abd1f2dcfcd9ad79d');if(!$data = tpl_cache($tag_cache_name,3600)){pc_base::load_sys_class("get_model", "model", 0);$get_db = new get_model();$r = $get_db->sql_query("$sql LIMIT 20");while(($s = $get_db->fetch_next()) != false) {$a[] = $s;}$data = $a;unset($a);if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
				<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
					<a href="/dm_diqu<?php echo $r['linkageid'];?>"><?php echo $r['name'];?></a>&nbsp;|
				<?php $n++;}unset($n); ?>
			 <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
		</div>
        <div class="jq"><h3>状  态：</h3>
		<a href="/dm_lianzai">连载</a>&nbsp;|&nbsp;
		<a href="/dm_wanjie">完结</a>
	</div>
 </div>