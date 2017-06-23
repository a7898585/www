<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php $SEO['title']= $title."_".$title."全集_".$title."在线观看_".$title."动画片";?>
<?php $SEO['keyword']= $title."，".$title."动漫电影，".$title."动漫下载-漫画城";?>
<?php $SEO['description']= $title."，".str_cut($description,130,'...');?>
<?php $footer_txt = "漫画城动漫城频道为您提供".$title."动漫的第一时间更新，还有最新的".$title."动漫资讯、".$title."动漫捷报，漫画城是综合的".$title."在线动漫观看网站。"?>

<?php include template("content","header"); ?>
<?php include template("content","dm_nav"); ?>
    <div class="left c666">您现在的位置：&nbsp;<a href="/" class="LinkPath">首页</a>&nbsp;&gt;&nbsp;<a href="/dongman" class="LinkPath">动漫城</a>&nbsp;&gt;&nbsp;<?php echo $title;?></div>
    <div class="middle_div1">
    	<div class="middle_div1_le" style="border:none;">
        	<div class="tt">
            	<div class="tt_t">
                	<div class="tt_t_le"><img width=205 height=235 src="<?php echo $thumb;?>" /></div>
                    <div class="tt_t_ri">
                        	<h1 style="font-size:18px; font-weight:bold; padding:5px 15px 5px 0px;"><?php echo $title;?></h1>
                        <p>
                        	<span><em style="font-size:12px; font-weight:bold;">类别：</em><a href="<?php echo $CATEGORYS[$catid]['url'];?>"><?php echo $CATEGORYS[$catid]['catname'];?></a></span>
                            <span style="margin-left:15px;"><em style="font-size:12px; font-weight:bold;">地区：</em><a href="dm_diqu<?php echo $CartoonArea['id'];?>" style="padding-right:20px; padding-left:10px;"><?php echo $CartoonArea['name'];?></a></span>
                        </p>
                        <p style="height:auto; line-height:30px;">
						<span id="hits" style="display:none"><?php echo $views;?> 次</span>
                        	<em style="font-size:12px; font-weight:bold;">简介：</em> 
							<em class="description">
								<?php if(!empty($content)) { ?>
								<?php if(strlen(strip_tags($content)) < 550) { ?>
									<span><?php echo strip_tags($content);?></span>
									<?php } else { ?>
									<span id="LM1"><?php echo str_cut(strip_tags($content),550,'...');?><a onClick="javascript:ShowFLT(2)" href="javascript:void(null)">详细</a></span>
									<span id="LM2" style="display:none"><?php echo strip_tags($content);?><a onClick="javascript:ShowFLT(1) " href="javascript:void(null)">收起</a></span>
									<?php } ?>
								<?php } else { ?>
									<span>暂无动漫简介</span>
								<?php } ?>
							</em>
                        </p>
                    </div>
                </div>
				<script type="text/javascript">
/*漫画城动漫728*90，创建于2013-5-30*/
var cpro_id = "u1294697";
</script>
<script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>
                <div class="mh_copy1">
                    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"get\" data=\"op=get&tag_md5=f5904c6a7d188f19408f1738a8a55a49&sql=select+%2A+from+tt_dongmanDetail+where+CartoonID%3D%24id+group+by+title+order+by+listorder+asc&num=9999\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}pc_base::load_sys_class("get_model", "model", 0);$get_db = new get_model();$r = $get_db->sql_query("select * from tt_dongmanDetail where CartoonID=$id group by title order by listorder asc LIMIT 9999");while(($s = $get_db->fetch_next()) != false) {$a[] = $s;}$data = $a;unset($a);?>
					<?php $dm_list = array_chunk($data,100);?>
                	<div class="today_tit">
                    	<ul id="tab_list"> 
							<?php $n=1;if(is_array($dm_list)) foreach($dm_list AS $r) { ?>
								<?php $key ++?>
								<?php $start= current($r);?>
								<?php $end= end($r);?>
								<li title="<?php echo $start['title'];?>-<?php echo $end['title'];?>" class="<?php if(($key == 1)) { ?>on<?php } ?>" onClick="secBoard('tab_list','infolist_list',<?php echo $key;?>,'on','');GetSize();"><strong><?php echo $start['title'];?>-<?php echo $end['title'];?></strong></li>   
							<?php $n++;}unset($n); ?>
						</ul>
                    </div>
                    <div class="today_t">
						<?php $n=1; if(is_array($dm_list)) foreach($dm_list AS $key => $v) { ?>
							<?php $key++?>
							<ul id="infolist_list_<?php echo $key;?>" class="nr6 lan2" style="display:<?php if(($key == 1)) { ?>block<?php } else { ?>none<?php } ?>">		
								<?php $n=1;if(is_array($v)) foreach($v AS $val) { ?>
									<li>
										<a title="<?php echo $title;?><?php echo $val['title'];?>" id="<?php echo $val['id'];?>" href="/dongman<?php echo $id;?>-<?php echo $val['id'];?>" target="_blank"><?php echo str_cut($val[title],25,'');?></a>
									</li>  
								<?php $n++;}unset($n); ?>
							</ul>
						<?php $n++;}unset($n); ?>
                    </div>
				   <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
		 </div>
       </div>

        </div>

		<div class="middle_div1_ri">
        	<div class="tu"><?php include template("content","250ad"); ?></div>
			
			<div class="xh_man">
				<div class="rs_dm_tit">
						<h3><?php echo $title;?>相关资讯</h3>
					</div>

					<div class="jiebao_txt">
						<ul><?php $stitle= str_cutword($title,3,'');?>
						<?php $where_jb = "status = '99' AND title LIKE '%$stitle%'";?>
							<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=5a04d78991844dd3ae2f9ef694f96c5c&action=table_list&table=news&fields=id%2Ctitle%2Curl&where=%24where_jb&order=updatetime+DESC&num=10&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'news','fields'=>'id,title,url','where'=>$where_jb,'order'=>'updatetime DESC',)).'5a04d78991844dd3ae2f9ef694f96c5c');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'news','fields'=>'id,title,url','where'=>$where_jb,'order'=>'updatetime DESC','limit'=>'10',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
								<?php if(!empty($data)) { ?>
								<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
									<li><a target="_blank" href="<?php echo $r['url'];?>" title= "<?php echo $r['title'];?>"><?php echo str_cut($r[title],45,'...');?></a></li>
								<?php $n++;}unset($n); ?>
								<?php } else { ?>
									<li><p>暂无"<?php echo $title;?>"相关资讯</p></li>
								<?php } ?>
							<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
						</ul>
					</div>
			</div>

            <div class="xh_man ">
				<div class="rs_dm_tit">
					<h3>同类型推荐</h3>
				</div>
                <div class="xh_man_txt">
                	<ul>		
					<?php $where_sql = "id != '$id' AND status=99 AND catid='$catid'";?>
					<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=3b0e9e081e42b0ff54d90cf325f41d72&action=lists&modelid=17&where=%24where_sql&order=updatetime+desc&num=6\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('modelid'=>'17','where'=>$where_sql,'order'=>'updatetime desc','limit'=>'6',));}?>
						<?php $n=1;if(is_array($data)) foreach($data AS $val) { ?>
                    	<li>
                        	<a target="_blank" title="<?php echo $val['title'];?>" href="/dongman<?php echo $val['id'];?>"><img width=80 height=115 src="<?php echo $val['thumb'];?>"></a>
                            <a target="_blank" title="<?php echo $val['title'];?>" href="/dongman<?php echo $val['id'];?>"><?php echo str_cut($val[title],18,'');?></a>
                        </li>
						<?php $n++;}unset($n); ?>
					<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                    </ul>
                </div>
            </div>
           
        </div>
    </div>
	<script type="text/javascript">

var number=2; //定义条目数

function LMYC() {
var lbmc;
    for (i=1;i<=number;i++) {
        lbmc = eval('LM' + i);
        lbmc.style.display = 'none';
    }
}
 
function ShowFLT(i) {
    lbmc = eval('LM' + i);
    if (lbmc.style.display == 'none') {
        LMYC();
        lbmc.style.display = '';
    }
    else {
        lbmc.style.display = 'none';
    }
}
function copyToClipBoard(el){
	var clipBoardContent = document.title +'\r\n'+ location.href +'';
	window.clipboardData.setData("Text",clipBoardContent);
	alert(clipBoardContent+'\r\n复制成功,发给你的好友一起分享吧!');
}
</script>
<script language="JavaScript" src="<?php echo APP_PATH;?>api.php?op=count&id=<?php echo $id;?>&modelid=<?php echo $modelid;?>&type=<?php echo $parameters;?>"></script>
<?php include template("content","footer"); ?>
