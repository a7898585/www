<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php $l=$_GET[letter];?>
<?php $SEO['title']="漫画_".$l."字母漫画_最新".$l."字母漫画_".$l."字母在线漫画-漫画城";?>
<?php $SEO['keyword']="漫画,".$l."字母漫画,最新".$l."字母漫画,".$l."字母在线漫画，好看的".$l."字母漫画";?>
<?php $SEO['description']="漫画城在线漫画网为您提供最全、最新的".$l."字母漫画、最新".$l."字母漫画、".$l."字母在线漫画、好看的".$l."字母漫画，漫画城在线漫画网是国内更新速度最快、漫画资源最全的专业在线漫画网站！";?>
<?php $footer_txt = "漫画城".$l."字母频道为您提供最全、最新的".$l."字母漫画、最新".$l."字母漫画、".$l."字母在线漫画、好看的".$l."字母漫画，漫画城在线漫画网是国内更新速度最快、漫画资源最全的专业漫画网站！"?>

<?php include template("content","header"); ?>
<?php include template("content","mh_nav"); ?>
<div class="left c666">您现在的位置：&nbsp;<a href="." class="LinkPath">首页</a>&nbsp;&gt;&nbsp;<a href="/manhua" class="LinkPath">在线漫画</a>&nbsp;&gt;&nbsp;<?php echo $l;?>字母漫画列表</div>
    <div class="middle_div2" style="margin-top:5px;">
    	<div class="middle_div2_le">
            <div class="today">
                	<div class="today_tit">
                    	<ul id ="tab_c">                
                        	<li class="on" onmouseover="secBoard('tab_c','infolist_c',1,'on','');"><strong>连载漫画推荐</strong></li>                
							<li onmouseover="secBoard('tab_c','infolist_c',2,'on','');"><strong>完结漫画推荐</strong></li>  
                                 
                        </ul>
                    </div>
                    <div class="today_txt">
                    	<ul id="infolist_c_1" style="display:block">
			<?php $where_lianzai = $where." AND state = 0";?>		
			<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=6cdf9d8f8ecb96993de423fc77bd70e8&action=table_list&table=Cartoon&where=%24where_lianzai&order=views+DESC&num=5&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'Cartoon','where'=>$where_lianzai,'order'=>'views DESC',)).'6cdf9d8f8ecb96993de423fc77bd70e8');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','where'=>$where_lianzai,'order'=>'views DESC','limit'=>'5',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
			  <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
				 <li>
                    <a target="_blank" title = '<?php echo $r['title'];?>' href="/manhua<?php echo $r['id'];?>"><img width='100' height='135' src="<?php echo $r['thumb'];?>" ></a>
                    <a target="_blank" title = '<?php echo $r['title'];?>' href="/manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],16,'');?></a>
                 </li>
			  <?php $n++;}unset($n); ?>
			<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>

                        </ul>
                        <ul id="infolist_c_2" style="display:none">
			<?php $where_wanjie = $where." AND state = 1";?>		
			<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=96c760da0338776f4b42a95b787c0e99&action=table_list&table=Cartoon&where=%24where_wanjie&order=views+DESC&num=5&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'Cartoon','where'=>$where_wanjie,'order'=>'views DESC',)).'96c760da0338776f4b42a95b787c0e99');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','where'=>$where_wanjie,'order'=>'views DESC','limit'=>'5',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
			  <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
				 <li>
                    <a target="_blank" title = '<?php echo $r['title'];?>' href="/manhua<?php echo $r['id'];?>"><img width='100' height='135' src="<?php echo $r['thumb'];?>" ></a>
                    <a target="_blank" title = '<?php echo $r['title'];?>' href="/manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],16,'');?></a>
                 </li>
			  <?php $n++;}unset($n); ?>
			<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                        </ul>
                    </div>
                </div>
                <div class="inkk mato10"><div class="inbt2"><div class="new_h1"><?php echo $letter;?>字母漫画列表</div></div>
				<div class="syzm syzm_lj">				
				<span class="redzi">字母筛选：</span>
					<?php $Q=NULL;?>
					<?php for ($i=65; $i<=90; $i++) $Q[]=chr($i);?>
					<?php $n=1;if(is_array($Q)) foreach($Q AS $r) { ?>
						<a href="/index<?php echo $r;?>/"><?php echo $r;?></a>
					<?php $n++;}unset($n); ?>
				</div>		
                <div class="innr3"> 
                	<ul>
                    	<li style="background: none repeat scroll 0 0 #F3F5F7; border-bottom:none; height:30px; line-height:30px;">
							<span style="padding-left:5px;">漫画名称</span>
							<span style="padding-left:300px;">作者</span>
							<span>漫画人气</span>
							<span>更新时间</span>
                        </li>
					<?php $n=1;if(is_array($chardata)) foreach($chardata AS $r) { ?>
                        <li>
                        	<div class="mc"><a title="<?php echo $r['title'];?>" href="/manhua<?php echo $r['id'];?>" target="_blank"><?php echo $r['title'];?></a></div>
                            <div class="zz"><?php if(!empty($r[Author])) { ?><?php echo $r['Author'];?><?php } else { ?>暂无<?php } ?></div>
                            <div class="renqi"><?php echo $r['views'];?></div>
                            <div class="gxsj"><?php echo date('Y-m-d',$r[updatetime]);?></div>
                        </li> 
					<?php $n++;}unset($n); ?>
				  </ul>
                </div>    
                 <div class=" clear1"></div>            
                <div class="ad02" id="search_fy"><?php echo $pages;?></div>		
           </div>
        </div>
        
        
        <div class="middle_div2_ri">
        	<div class="inkk mato5">			
			<div class="inbt">				
				<div class="inbt_title">漫画排行</div>				
			</div>			
			<div id="cbc_1" class="innr4">                	
				<ul class="innr41">    					
				<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=5d0476e10d69cd9790067d1379b21668&action=table_list&table=Cartoon&where=%24where&order=views+DESC&num=30&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'Cartoon','where'=>$where,'order'=>'views DESC',)).'5d0476e10d69cd9790067d1379b21668');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','where'=>$where,'order'=>'views DESC','limit'=>'30',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
				  <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
	  				<li>
						<a title="<?php echo $r['title'];?>" href="/manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],30,'');?></a>
					</li>  
				  <?php $n++;}unset($n); ?>
				<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
				</ul>
			</div>					
			</div>
        </div>
    </div>

<?php include template("content","footer"); ?>