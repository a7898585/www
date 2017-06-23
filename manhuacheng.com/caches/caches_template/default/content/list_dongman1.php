<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php include template("content","header"); ?>
<?php $footer_txt= "漫画城动漫城频道拥有上万集高清晰画质的在线动漫、好看的动漫、火影忍者动漫，漫画城是最齐全的动漫大全，国内最大的动漫城。";?>
<style>
.dongman_tit1 li {
    width: 75px;
}
.today1_txt1 li{
	height:207px;
}
</style>
	<?php include template("content","dm_nav"); ?>
    <div class="zj">
        	<div class="zj_tit">
           		<i class="icon"></i>
           		<h3>最近更新</h3>       
          	</div>
            <div class="zj_txt">
                <ul class="mod-pic2">
					<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=e70f1f8d2a1c89da944f8be352125813&action=table_list&table=dongman&fields=id%2Ctitle%2Cthumb&status=99&order=inputtime+DESC&num=8&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'dongman','fields'=>'id,title,thumb','status'=>'99','order'=>'inputtime DESC',)).'e70f1f8d2a1c89da944f8be352125813');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'dongman','fields'=>'id,title,thumb','status'=>'99','order'=>'inputtime DESC','limit'=>'8',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
					<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
						    <li><a target="_blank" title="<?php echo $r['title'];?>" href="/dongman<?php echo $r['id'];?>"><img width="100" height="90" src="<?php echo $r['thumb'];?>"><p><?php echo str_cut($r[title],25,'');?></p></a></li>
					<?php $n++;}unset($n); ?>
					<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
              	</ul>
                   </div>
             </div>
    <div class="middle">
    	<div class="middle_le">
        	<div class="today">
                	<div class="today_tit">
                    	 
                    	<ul id="tab_a">                
                        	<li onmouseover="secBoard('tab_a','infolist_a',1,'on','');" class="on"><strong>今日动漫推荐</strong></li>                
							<li onmouseover="secBoard('tab_a','infolist_a',2,'on','');"><strong>经典完结动漫</strong></li>  
                                 
                        </ul>
                    </div>
                    <div class="today_txt">
                    	<ul id="infolist_a_1" style="display:block">
						<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=9256605d49dd630f20288d827d6a0603&action=position&posid=16&order=updatetime+DESC&sort=desc&num=12\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'position')) {$data = $content_tag->position(array('posid'=>'16','order'=>'updatetime DESC','sort'=>'desc','limit'=>'12',));}?>
						<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                       		<li>
                            	<a target="_blank" title="{<?php echo $r['title'];?>" href="/dongman<?php echo $r['id'];?>"><img  width='115' height='130' src="<?php echo $r['thumb'];?>"></a>
                                <a target="_blank"  title="<?php echo $r['title'];?>" href="/dongman<?php echo $r['id'];?>"><?php echo str_cut($r[title],25,'');?></a>
                            </li>
						<?php $n++;}unset($n); ?>
						<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                        </ul>
                        <ul id="infolist_a_2" style="display:none">
                        	<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=cd3f2033e3cc45ea047f59d9bcf1a530&action=table_list&table=dongman&fields=id%2Ctitle%2Cthumb&status=99&state=1&order=views+DESC&num=12&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'dongman','fields'=>'id,title,thumb','status'=>'99','state'=>'1','order'=>'views DESC',)).'cd3f2033e3cc45ea047f59d9bcf1a530');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'dongman','fields'=>'id,title,thumb','status'=>'99','state'=>'1','order'=>'views DESC','limit'=>'12',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
							<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
									<li>
									<a target="_blank" title="<?php echo $r['title'];?>" href="/dongman<?php echo $r['id'];?>"><img width="105" height="120" src="<?php echo $r['thumb'];?>"></a>
									<a href="/dongman<?php echo $r['id'];?>" title="<?php echo $r['title'];?>" target="_blank"><?php echo str_cut($r[title],25,'');?></a></li>
							<?php $n++;}unset($n); ?>
							<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                        </ul>
                    </div>
                </div>
                <div class="today1" >
                	<div class="today1_tit">
                    	<ul id ="tab_c">                
							<li class="on" onmouseover="secBoard('tab_c','infolist_c',1,'on','');"><strong>日韩动漫</strong></li> 
							<li onmouseover="secBoard('tab_c','infolist_c',2,'on','');"><strong>欧美动漫</strong></li>  
							<li onmouseover="secBoard('tab_c','infolist_c',3,'on','');"><strong>港台动漫</strong></li>     
							<li onmouseover="secBoard('tab_c','infolist_c',4,'on','');"><strong>国产动漫</strong></li>   
                        </ul>

                    </div>
                     <div class="today1_txt">
                    	<ul class="today1_txt1" id="infolist_c_1" style="display:block">
						<?php $where1 = " status = 99 AND CartoonArea = 3361";?>
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=e6a138f0f8df61db5f432875209c3c39&action=table_list&table=dongman&where=%24where1&order=updatetime+DESC&num=10&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'dongman','where'=>$where1,'order'=>'updatetime DESC',)).'e6a138f0f8df61db5f432875209c3c39');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'dongman','where'=>$where1,'order'=>'updatetime DESC','limit'=>'10',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
						  <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
							<li>
								<a href="/dongman<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><img width='115' height='130' src="<?php echo $r['thumb'];?>"/></a>
								<a href="/dongman<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],25,'');?></a>
							</li>
						  <?php $n++;}unset($n); ?>
						<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                        </ul>
						<ul class="today1_txt1" id="infolist_c_2" style="display:none">
						<?php $where1 = " status = 99 AND CartoonArea = 3363";?>
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=e6a138f0f8df61db5f432875209c3c39&action=table_list&table=dongman&where=%24where1&order=updatetime+DESC&num=10&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'dongman','where'=>$where1,'order'=>'updatetime DESC',)).'e6a138f0f8df61db5f432875209c3c39');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'dongman','where'=>$where1,'order'=>'updatetime DESC','limit'=>'10',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
						  <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
							<li>
								<a href="/dongman<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><img width='115' height='130' src="<?php echo $r['thumb'];?>"/></a>
								<a href="/dongman<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],25,'');?></a>
							</li>
						  <?php $n++;}unset($n); ?>
						<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                        </ul>
						<ul class="today1_txt1" id="infolist_c_3" style="display:none">
						<?php $where1 = " status = 99 AND CartoonArea = 3362";?>
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=e6a138f0f8df61db5f432875209c3c39&action=table_list&table=dongman&where=%24where1&order=updatetime+DESC&num=10&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'dongman','where'=>$where1,'order'=>'updatetime DESC',)).'e6a138f0f8df61db5f432875209c3c39');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'dongman','where'=>$where1,'order'=>'updatetime DESC','limit'=>'10',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
						  <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
							<li>
								<a href="/dongman<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><img width='115' height='130' src="<?php echo $r['thumb'];?>"/></a>
								<a href="/dongman<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],25,'');?></a>
							</li>
						  <?php $n++;}unset($n); ?>
						<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                        </ul>
						<ul class="today1_txt1" id="infolist_c_4" style="display:none">
						<?php $where1 = " status = 99 AND CartoonArea = 3364";?>
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=e6a138f0f8df61db5f432875209c3c39&action=table_list&table=dongman&where=%24where1&order=updatetime+DESC&num=10&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'dongman','where'=>$where1,'order'=>'updatetime DESC',)).'e6a138f0f8df61db5f432875209c3c39');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'dongman','where'=>$where1,'order'=>'updatetime DESC','limit'=>'10',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
						  <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
							<li>
								<a href="/dongman<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><img width='115' height='130' src="<?php echo $r['thumb'];?>"/></a>
								<a href="/dongman<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],25,'');?></a>
							</li>
						  <?php $n++;}unset($n); ?>
						<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                        </ul>
                    </div>
                </div>
        </div>
        <div class="middle_ri middle_div1_ri">
			<div class="eveday" style="width: 223px;">
				<div class="rs_dm_tit">
					<h3 style="color:black">每日更新</h3>
				</div>
                <div class="eveday_txt">
                    <div class="eveday_txt_r">
                    	<ul>
							<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=a59e5c1d853ed450ad955589807700c3&action=table_list&table=dongman&fields=id%2Ctitle&order=inputtime+DESC&num=5&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'dongman','fields'=>'id,title','order'=>'inputtime DESC',)).'a59e5c1d853ed450ad955589807700c3');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'dongman','fields'=>'id,title','order'=>'inputtime DESC','limit'=>'5',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
								<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
			                       <li><a href="/dongman<?php echo $r['id'];?>/"><?php echo str_cut($r[title],50,'');?></a></li>
								<?php $n++;}unset($n); ?>
							<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>                        
                       </ul>
                    </div>
                </div>
            </div>
			<div class="jiebao" style="width: 223px;margin-top: 5px;">
				<div class="rs_dm_tit">
					<h3 style="color:black">漫画资讯<span style="float: right;font-size: 12px;font-weight: normal;padding-right: 5px;"><a href="/zixun">更多&gt;&gt;</a></span></h3>
				</div>
                <div class="jiebao_txt">
					<ul>						
					<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=c44939dd3db374ce7474d8d4e81dbdec&action=lists&catid=19&num=8&order=updatetime+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('catid'=>'19','order'=>'updatetime DESC','limit'=>'8',));}?>
						<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
							<li><a target="_blank" href="<?php echo $r['url'];?>" title= "<?php echo $r['title'];?>"><?php echo str_cut($r[title],45,'...');?></a></li>
						<?php $n++;}unset($n); ?>
					<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
					</ul>
                </div>
			</div>
            <div class="rs_dm">
            	<div class="rs_dm_tit"><h3 style="color:black" >人气动漫排行</h3></div>
                <div class="rs_dm_txt">
                	<ul>
						<?php $class= array(1=>'first',2=>'second',3=>'third')?>
						<?php $i=1;?>
						<?php $where_dongman = "status=99";?>		
						<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=5f1d2ddec59f5ddf11acdc6c1f566d5c&action=table_list&table=dongman&fields=id%2Ccatid%2Ctitle&where=%24where_dongman&order=views+DESC&num=10&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'dongman','fields'=>'id,catid,title','where'=>$where_dongman,'order'=>'views DESC',)).'5f1d2ddec59f5ddf11acdc6c1f566d5c');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'dongman','fields'=>'id,catid,title','where'=>$where_dongman,'order'=>'views DESC','limit'=>'10',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
						<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
								<li><i target="_blank" class="<?php echo $class[$i];?>"><?php echo $i;?></i><a href="/dongman<?php echo $r['id'];?>/"><?php echo str_cut($r[title],25,"");?></a></li>
							<?php $i++?>
						<?php $n++;}unset($n); ?>
						<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="dongman1">
                	<div class="dongman_tit1">
                    	<ul id ="tab_d">   
						  <?php $i =1 ;?>
						  <?php $class= array(1=>'on')?>
						   <?php $n=1;if(is_array($catlist)) foreach($catlist AS $r) { ?>
							<?php if($i <= 10) { ?>
							  <li onmouseover="secBoard('tab_d','infolist_d',<?php echo $i;?>,'on','');"class="<?php echo $class[$i];?>"><strong><?php echo $r['catname'];?></strong></li>
							<?php } else { ?>
							 <?php break;?>
							<?php } ?>
						   <?php $i++;?>
						   <?php $n++;}unset($n); ?>
						  <?php unset($i);unset($class);?>
                        </ul>
                    </div>
                    <div class="dongman_txt1">
						<?php $i =1 ;?>
						   <?php $n=1;if(is_array($catlist)) foreach($catlist AS $r) { ?>
							<?php if($i <= 10) { ?>
								<ul <?php if($i > 1) { ?>style="display:none"<?php } ?> id="infolist_d_<?php echo $i;?>" class="dongman_txt11">
									<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=d9d53a72feae9a8a77e92b7b5baba8e2&action=lists&modelid=17&status=99&catid=%24r%5Bcatid%5D&order=views+desc&num=7\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('modelid'=>'17','status'=>'99','catid'=>$r[catid],'order'=>'views desc','limit'=>'7',));}?>
										<?php $n=1;if(is_array($data)) foreach($data AS $val) { ?>
										<li>
											<a target="_blank" title="<?php echo $val['title'];?>" href="/dongman<?php echo $val['id'];?>"><img width=115 height=130 src="<?php echo $val['thumb'];?>"></a>
											<a target="_blank" title="<?php echo $val['title'];?>" href="/dongman<?php echo $val['id'];?>"><?php echo str_cut($val[title],30,'');?></a>
										</li>
										<?php $n++;}unset($n); ?>
									<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
								</ul>
							<?php } else { ?>
							 <?php break;?>
							<?php } ?>
						   <?php $i++;?>
						 <?php $n++;}unset($n); ?>
                    </div>
                </div>
     <div class="tuku">
     	<div class="tuku_tit"><i class="icon"></i><h3>动漫图库</h3></div>
        <div class="tuku_txt">
        	<ul>
				<?php $sql = "SELECT thumb FROM tt_dongman WHERE id >= ((SELECT MAX(id) FROM tt_dongman)-(SELECT MIN(id) FROM tt_dongman)) * RAND() + (SELECT MIN(id) FROM tt_dongman) AND status=99";?>
				<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"get\" data=\"op=get&tag_md5=eece0b6d1294cb71991001fd981efbcb&sql=%24sql&num=6&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('sql'=>$sql,)).'eece0b6d1294cb71991001fd981efbcb');if(!$data = tpl_cache($tag_cache_name,3600)){pc_base::load_sys_class("get_model", "model", 0);$get_db = new get_model();$r = $get_db->sql_query("$sql LIMIT 6");while(($s = $get_db->fetch_next()) != false) {$a[] = $s;}$data = $a;unset($a);if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
					<?php $n=1;if(is_array($data)) foreach($data AS $val) { ?>
					 <li><img width=146 height=150 src="<?php echo $val['thumb'];?>"></li>
					<?php $n++;}unset($n); ?>
				<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
            </ul>
        </div>
     </div>
     <div class="lianjie">
        	<div class="lianjie_tit"><h3>友情链接</h3></div>
            <div class="lianjie_txt">
            	<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"link\" data=\"op=link&tag_md5=c7af35f3a2e6d9bdf7ceb479aa1bb056&action=lists&typeid=23&linktype=0&order=desc&num=50&return=dat\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$link_tag = pc_base::load_app_class("link_tag", "link");if (method_exists($link_tag, 'lists')) {$dat = $link_tag->lists(array('typeid'=>'23','linktype'=>'0','order'=>'desc','limit'=>'50',));}?>
					<?php $n=1; if(is_array($dat)) foreach($dat AS $key => $v) { ?>
					 <a href="<?php echo $v['url'];?>" title="<?php echo $v['name'];?>"  target="_blank"><?php echo $v['name'];?> </a> 
					 <?php if($v != end($dat)) { ?>&nbsp;|&nbsp;<?php } ?>
					<?php $n++;}unset($n); ?>
				<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
            </div>
        </div>
<?php include template("content","footer"); ?>