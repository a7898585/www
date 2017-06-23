<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php $m= getboxname('12','type_id'); ?> 
<?php if(empty($typeid)) { ?>
	<?php $SEO['title']="漫画排行榜_漫画人气排行榜-漫画城";?>
	<?php $SEO['keyword']="漫画排行榜，漫画人气排行榜，热门漫画，最新漫画排行榜";?>
	<?php $SEO['description']="漫画城漫画排行榜提供最新最热门的漫画排行、漫画人气排行榜、动漫排行榜，是最权威的漫画排行榜。";?>
<?php } else { ?>
	<?php $SEO['title']=$m[$typeid]."漫画排行榜_".$m[$typeid]."漫画人气排行榜-漫画城";?>
	<?php $SEO['keyword']=$m[$typeid]."漫画排行榜，最新".$m[$typeid]."漫画排行榜，热门".$m[$typeid]."漫画排行榜";?>
	<?php $SEO['description']="漫画城".$m[$typeid]."漫画排行榜提供最新最热门的".$m[$typeid]."漫画排行，是最权威的".$m[$typeid]."漫画排行榜。";?>
<?php } ?>
<?php include template("content","header"); ?>
    <div class="middle_div6">
    	<div class="middle_div6_le">
        	<div class="phdh">
            	<div class="phdh_tit"><h3>排行导航</h3></div>
                <div class="phdh_txt">
                	<ul>	
						<li <?php if(empty($typeid)) { ?>class="o"<?php } ?>><a href="/rank">全部漫画排行榜</a></li>
						<?php $n=1; if(is_array($m)) foreach($m AS $n => $l) { ?>
								<?php if(!in_array($n,dexplode(NOT_ALLOW))) { ?>
									<li <?php if($typeid == $n) { ?>class="o"<?php } ?>><a href="/rank<?php echo $n;?>"><?php echo $l;?>漫画排行榜</a></li>
								<?php } ?>
						<?php $n++;}unset($n); ?>
                    </ul>
                </div>
            </div>
			




        </div>
        <div class="middle_div6_ri">
        	<div class="phb">
            	<div class="phb_tit">
                	<ul id = 'tab_a'>                
						<li onmouseover="secBoard('tab_a','infolist_a',1,'on','');" class="on"><strong>全 部</strong></li>
						<li onmouseover="secBoard('tab_a','infolist_a',2,'on','');" class=""><strong>日 榜</strong></li>   
						<li onmouseover="secBoard('tab_a','infolist_a',3,'on','');" class=""><strong>周 榜</strong></li>                
						<li onmouseover="secBoard('tab_a','infolist_a',4,'on','');" class=""><strong>月 榜</strong></li>   
                    </ul>
                </div>
                <div style="display:block;" class="phb_txt">
                	<div class="px">
                    	<span style="padding-left:45px; padding-right:45px;">类 型</span>
						<span style="padding-right:120px;padding-left: 50px;">漫画名称</span>
						<span>浏览次数</span>
						<span style="padding-right:0px;">更新时间</span>
                    </div>
                    <div class="px_n">
                    	<ul id = 'infolist_a_1' style="display:block">
						<?php $i=1;?>
						<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=c156c0c6c10ffcb2bffd340485e9c8c0&action=hits2&catid=13&num=20&where=%24wherearr%5B1%5D&order=views+DESC&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('catid'=>'13','where'=>$wherearr[1],'order'=>'views DESC',)).'c156c0c6c10ffcb2bffd340485e9c8c0');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'hits2')) {$data = $content_tag->hits2(array('catid'=>'13','where'=>$wherearr[1],'order'=>'views DESC','limit'=>'20',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
						<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                        	<li>
								<span style="width: 170px;"><?php echo $i;?>、<a href="/type<?php echo $r['type_id'];?>">[<?php echo $m[$r['type_id']];?>]</a></span>
								<span style="width: 180px;"><a href="/manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],30,'..');?></a></span>
								<span style="width: 200px;"><?php echo $r['views'];?>人看过</span>
								<span><?php echo date('Y-m-d',$r[updatetime]);?></span>
							</li>
						<?php $i++;?>
						<?php $n++;}unset($n); ?>
                        </ul>
						
						<ul id = 'infolist_a_2' style="display:none">
						<?php $i=1;?>
						<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=80b2ea7953fcb457b5a4cfff6cbdacb0&action=hits2&catid=13&num=20&where=%24wherearr%5B1%5D&order=dayviews+DESC&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('catid'=>'13','where'=>$wherearr[1],'order'=>'dayviews DESC',)).'80b2ea7953fcb457b5a4cfff6cbdacb0');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'hits2')) {$data = $content_tag->hits2(array('catid'=>'13','where'=>$wherearr[1],'order'=>'dayviews DESC','limit'=>'20',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
						<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                        	<li>
								<span style="width: 170px;"><?php echo $i;?>、<a href="/type<?php echo $r['type_id'];?>">[<?php echo $m[$r['type_id']];?>]</a></span>
								<span style="width: 180px;"><a href="/manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],30,'..');?></a></span>
								<span style="width: 200px;"><?php echo $r['dayviews'];?>人看过</span>
								<span><?php echo date('Y-m-d',$r[updatetime]);?></span>
							</li>
						<?php $i++;?>
						<?php $n++;}unset($n); ?>
                        </ul>
						
						<ul id = 'infolist_a_3' style="display:none">
						<?php $i=1;?>
						<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=383662230718c89cc955595c5df53d71&action=hits2&catid=13&num=20&where=%24wherearr%5B1%5D&order=weekviews+DESC&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('catid'=>'13','where'=>$wherearr[1],'order'=>'weekviews DESC',)).'383662230718c89cc955595c5df53d71');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'hits2')) {$data = $content_tag->hits2(array('catid'=>'13','where'=>$wherearr[1],'order'=>'weekviews DESC','limit'=>'20',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
						<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                        	<li>
								<span style="width: 170px;"><?php echo $i;?>、<a href="/type<?php echo $r['type_id'];?>">[<?php echo $m[$r['type_id']];?>]</a></span>
								<span style="width: 180px;"><a href="/manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],30,'..');?></a></span>
								<span style="width: 200px;"><?php echo $r['weekviews'];?>人看过</span>
								<span><?php echo date('Y-m-d',$r[updatetime]);?></span>
							</li>
						<?php $i++;?>
						<?php $n++;}unset($n); ?>
                        </ul>
						
						<ul id = 'infolist_a_4' style="display:none">
						<?php $i=1;?>
						<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=a03e34860ac22419975af24993776db7&action=hits2&catid=13&num=20&where=%24wherearr%5B1%5D&order=monthviews+DESC&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('catid'=>'13','where'=>$wherearr[1],'order'=>'monthviews DESC',)).'a03e34860ac22419975af24993776db7');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'hits2')) {$data = $content_tag->hits2(array('catid'=>'13','where'=>$wherearr[1],'order'=>'monthviews DESC','limit'=>'20',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
						<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                        	<li>
								<span style="width: 170px;"><?php echo $i;?>、<a href="/type<?php echo $r['type_id'];?>">[<?php echo $m[$r['type_id']];?>]</a></span>
								<span style="width: 180px;"><a href="/manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],30,'..');?></a></span>
								<span style="width: 200px;"><?php echo $r['monthviews'];?>人看过</span>
								<span><?php echo date('Y-m-d',$r[updatetime]);?></span>
							</li>
						<?php $i++;?>
						<?php $n++;}unset($n); ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include template("content","footer"); ?>