<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php include template("content","header"); ?>
<!--main-->    <div class="middle_div1">
    	<div class="middle_div1_le" style="border:none;">
        	<div class="mhzx middle_div1_ri">
			
				<div class="rs_dm_tit" style="width:100%">
					<h3><?php echo $CATEGORYS[$catid]['catname'];?></h3>
				</div>
			<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=3656e07f32902d0870e7252a99cc8090&action=lists&catid=%24catid&num=7&order=id+DESC&page=%24page\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$pagesize = 7;$page = intval($page) ? intval($page) : 1;if($page<=0){$page=1;}$offset = ($page - 1) * $pagesize;$content_total = $content_tag->count(array('catid'=>$catid,'order'=>'id DESC','limit'=>$offset.",".$pagesize,'action'=>'lists',));$pages = pages($content_total, $page, $pagesize, '');$data = $content_tag->lists(array('catid'=>$catid,'order'=>'id DESC','limit'=>$offset.",".$pagesize,'action'=>'lists',));}?>
				<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
					<div class="mhzx_txt">
						<div class="mhzx_txt_le"><a href="<?php echo $r['url'];?>" target="_blank"><img width='200' height='130' src="<?php echo thumb($r[thumb], 200, 130, 0);?>"/></a></div>
						<div class="mhzx_txt_ri">
							<div class="to">
								<i class="icon"></i>
								<h3><a href="<?php echo $r['url'];?>" target="_blank" ><?php echo $r['title'];?></a></h3>
							</div>
							<p><?php echo $r['description'];?><a href="<?php echo $r['url'];?>" target="_blank">[阅读全文]</a></p>
							<div class="pl">
								<div class="pl_le"><span>浏览:<?php echo $r['views'];?></span></div>
							</div>
						</div>
					</div>
				<?php $n++;}unset($n); ?>
			<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>

                <div class="ad02 page" id="search_fy">
				<?php echo $pages;?>     	   
				</div>	
            </div>
        </div>
        
        
        <div class="middle_div1_ri">
        	<div class="tu"><?php include template("content","250ad"); ?></div>
		<?php include template("content","block_ranking"); ?>

            
                <div class="xh_man">
				<div class="rs_dm_tit">
					<h3>完结漫画推荐</h3>
				</div>
                <div class="xh_man_txt">
                	<ul>
						<?php $where = "status=99 AND state = 1 AND type_id NOT IN(".dimplode(dexplode(NOT_ALLOW)).")";?>		
						<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=69b06e154ecb63cf26a1bbe548b45554&action=table_list&table=Cartoon&fields=id%2Ccatid%2Ctitle%2Cthumb&where=%24where&order=views+DESC&num=6&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'Cartoon','fields'=>'id,catid,title,thumb','where'=>$where,'order'=>'views DESC',)).'69b06e154ecb63cf26a1bbe548b45554');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','fields'=>'id,catid,title,thumb','where'=>$where,'order'=>'views DESC','limit'=>'6',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
						<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
						<li>
                        	<a href="/manhua<?php echo $r['id'];?>" title ="<?php echo $r['title'];?>" target="_blank"><img width=80 height=115 src="<?php echo $r['thumb'];?>"></a>
                            <a href="/manhua<?php echo $r['id'];?>" title ="<?php echo $r['title'];?>" target="_blank"><?php echo str_cut($r[title],15,'');?></a>
                        </li>

							<?php $n++;}unset($n); ?>
							<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>

                    </ul>
                </div>
            </div>
        </div>
    </div>

<?php include template("content","footer"); ?>