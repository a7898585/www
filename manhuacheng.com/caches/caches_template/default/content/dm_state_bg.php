<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php if($state) { ?>
<?php $state="完结";?>
<?php } else { ?>
<?php $state="连载";?>
<?php } ?>
<?php $SEO['title']=$state."动漫_在线动漫_在线动漫观看 - 漫画城";?>
<?php $SEO['keyword']=$state."动漫,".$state."动漫观看，最新".$state."动漫,".$state."动漫";?>
<?php $SEO['description']="漫画城为您提供".$state."动漫，".$state."动漫观看，最新".$state."动漫,".$state."动漫，漫画城是最齐全的动漫大全、国内最大的动漫城。";?>
<?php $footer_txt = "漫画城".$state."动漫频道为您提供".$state."动漫，".$state."动漫观看，最新".$state."动漫,热门".$state."动漫，漫画城是最齐全的动漫大全、国内最大的动漫城。"?>
<?php include template("content","header"); ?>
<?php include template("content","dm_nav"); ?>
	<div class="left c666">您现在的位置：&nbsp;<a class="LinkPath" href="/">首页</a>&nbsp;&gt;&nbsp;<a class="LinkPath" href="/dongman">动漫城</a>&nbsp;&gt;&nbsp;<?php echo $state;?>动漫</div>
    <div class="middle_div1">
    	<div class="middle_div1_le" style="border:1px solid #AEDD5E">
        	<div class="jingdian_tit">
            	<i class="icon"></i>
            	<h3><?php echo $state;?>动漫列表</h3>
            </div>
            <div class="jingdian_txt">
            	<ul>
			 <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=f7bf46e3992ebe2046f92a24ed89b3f7&action=lists&catid=32&where=%24where&order=updatetime+DESC&num=16&page=%24page\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$pagesize = 16;$page = intval($page) ? intval($page) : 1;if($page<=0){$page=1;}$offset = ($page - 1) * $pagesize;$content_total = $content_tag->count(array('catid'=>'32','where'=>$where,'order'=>'updatetime DESC','limit'=>$offset.",".$pagesize,'action'=>'lists',));$pages = pages($content_total, $page, $pagesize, '');$data = $content_tag->lists(array('catid'=>'32','where'=>$where,'order'=>'updatetime DESC','limit'=>$offset.",".$pagesize,'action'=>'lists',));}?>	
				 <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
					<li>
                       <a target="_blank" href="/dongman<?php echo $r['id'];?>"><img width='140' height='170' src="<?php echo $r['thumb'];?>"></a>
                       <a target="_blank" title="<?php echo $r['title'];?>" href="/dongman<?php echo $r['id'];?>"><?php echo str_cut($r[title],35,'');?></a>
					</li>
				 <?php $n++;}unset($n); ?>
			 <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                </ul>
            </div>
            <div id="search_fy" class="sy_tbb" style="margin:0;"><?php echo $pages;?></div>
        </div>
        <div class="middle_div1_ri">
        	<div class="tu"><?php include template("content","250ad"); ?></div>
            <div class="xh_man">
				<div class="rs_dm_tit">
					<h3>最新更新动漫</h3>
				</div>
                <div class="xh_man_txt">
                	<ul>
						<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=f885f9312ae14491657de65579d89bfc&action=table_list&table=dongman&order=updatetime+DESC&num=4\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'dongman','order'=>'updatetime DESC','limit'=>'4',));}?>
						  <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
						  <li>
                        	<a href="/dongman<?php echo $r['id'];?>" title="<?php echo $r['title'];?>" target="_blank"><img width='80' height='115' src="<?php echo $r['thumb'];?>"></a>
                            <a href="/dongman<?php echo $r['id'];?>" title="<?php echo $r['title'];?>" target="_blank"><?php echo str_cut($r[title],15,'');?></a>
						  </li>
						  <?php $n++;}unset($n); ?>
						<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                    </ul>
                </div>
            </div>
            <div class="jiebao">
				<div class="rs_dm_tit">
					<h3>漫画资讯<span style="float: right;font-size: 12px;font-weight: normal;padding-right: 5px;"><a href="/zixun">更多&gt;&gt;</a></span></h3>
				</div>
				<div class="jiebao_txt">
					<ul>
						<?php $where_jb = "catid ='19' AND status = '99'";?>
						<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=036bb374fee0abd308550b00e5a87aa2&action=table_list&table=news&fields=id%2Ctitle%2Curl&where=%24where_jb&order=updatetime+DESC&num=11&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'news','fields'=>'id,title,url','where'=>$where_jb,'order'=>'updatetime DESC',)).'036bb374fee0abd308550b00e5a87aa2');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'news','fields'=>'id,title,url','where'=>$where_jb,'order'=>'updatetime DESC','limit'=>'11',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
							<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
								<li><a target="_blank" href="<?php echo $r['url'];?>" title= "<?php echo $r['title'];?>"><?php echo str_cut($r[title],45,'..');?></a></li>
							<?php $n++;}unset($n); ?>
						<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
					</ul>
				</div>
           </div>
        </div>
    </div>

<?php include template("content","footer"); ?>
