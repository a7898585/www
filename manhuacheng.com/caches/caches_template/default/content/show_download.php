<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php include template("content","header"); ?>
    <div class="home_banner1">
    <div class="about_soft">软件版本：<?php echo $version;?><br>
      更新时间：<?php echo substr($inputtime,0,10);?><br>
      文件大小：<?php echo $filesize;?><br>
      支持系统：<?php echo $systems;?><br>
      <div class="download"><script>document.write('<a href="http://www.manhuacheng.com/api.php?op=download&u='+document.referrer+'" target="_blank" ></a>');</script></div>
    </div>
    </div>
   	<div class="middle_div1">
    	<div class="middle_div1_le">
        	<div class="mhc">
            	<div class="mhc_tit"><h3><?php echo $title;?></h3></div>
                <div class="mhc_txt">
				<?php echo $content;?>
                </div>
            </div>
        </div>
        <div class="middle_div1_ri">
            <script type="text/javascript">
    /*漫画城下载页右侧矩形广告250*250 创建于 2015-06-01*/
    var cpro_id = "u2131304";
</script>
<script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>
        	<div class="xl_rj">
				<div class="rs_dm_tit">
					<h3>漫画城系列软件</h3>
				</div>
                <div class="xl_rj_txt">
                	<div class="content_box box2">
						<ul>
						  <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=55dcbc7a9becb683fb780a18029fc747&action=lists&catid=%24catid&num=5&order=id+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('catid'=>$catid,'order'=>'id DESC','limit'=>'5',));}?>
							  <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
								<li><a href="<?php echo $r['url'];?>"><?php echo $r['title'];?></a></li>
							  <?php $n++;}unset($n); ?>
						  <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
						</ul>
   				    </div>
                </div>
            </div>
			<div style="text-align: center;margin-top:5px;">
				<script type="text/javascript">
    /*漫画城下载页系列软件250*250 创建于 2015-06-01*/
    var cpro_id = "u2131306";
</script>
<script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>
			</div>
            <div class="jiebao">
				<div class="rs_dm_tit">
					<h3>周边资讯</h3>
				</div>
                    <div class="jiebao_txt">
                    	<ul>
						<?php $where = "catid IN('55','19') AND status = '99'";?>
						<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=a112d981ef3131aab055797f938468fe&action=table_list&table=news&fields=id%2Ctitle%2Curl&where=%24where&order=updatetime+DESC&num=6&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'news','fields'=>'id,title,url','where'=>$where,'order'=>'updatetime DESC',)).'a112d981ef3131aab055797f938468fe');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'news','fields'=>'id,title,url','where'=>$where,'order'=>'updatetime DESC','limit'=>'6',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
							<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
								<li><a target="_blank" href="<?php echo $r['url'];?>" title= "<?php echo $r['title'];?>"><?php echo str_cut($r[title],50,'..');?></a></li>
							<?php $n++;}unset($n); ?>
						<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                        </ul>
                    </div>
             </div>
			 <div style="text-align: center;margin-top:5px;">
			 <script type="text/javascript">
    /*漫画城下载页周边资讯250*250 创建于 2015-06-01*/
    var cpro_id = "u2131309";
</script>
<script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>
			</div>
             <div class="to_mh">
				<div class="rs_dm_tit">
					<h3>今日漫画推荐<span style="float: right;font-size: 12px;font-weight: normal;padding-right: 5px;"><a href="/manhua">更多&gt;&gt;</a></span></h3>
				</div>
                <div class="to_mh_txt">
                	<ul>
					<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=52af0d1aace473746c79b627101e2230&action=position&posid=14&order=updatetime+DESC&sort=desc&num=10\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'position')) {$data = $content_tag->position(array('posid'=>'14','order'=>'updatetime DESC','sort'=>'desc','limit'=>'10',));}?>
						<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
							<li>
								<a target="_blank" title="<?php echo $r['title'];?>" href="/manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],50,'..');?></a>
							</li>
						<?php $n++;}unset($n); ?>
					<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                    </ul>
                </div>
             </div>              
        </div>
    </div>
<?php include template("content","footer"); ?>