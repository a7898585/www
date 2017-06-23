<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php $w="id=$manhuaid"?>
<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=e7b734dfd5525937880616a5b7f6a4ad&action=lists&catid=13&where=%24w&num=1\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('catid'=>'13','where'=>$w,'limit'=>'1',));}?>
	<?php $n=1;if(is_array($data)) foreach($data AS $r1) { ?>
		<?php $mh_title = $r1[title];?>
	<?php $n++;}unset($n); ?>
<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>

<?php $SEO['title']=$mh_title.$title."漫画_".$mh_title."漫画".$title."_".$mh_title."漫画-漫画城";?>
<?php $SEO['keyword']=$mh_title.$title."，".$title."漫画";?>
<?php $SEO['description']="漫画城提供".$mh_title.$title."、".$mh_title."漫画".$title."第一时间更新，同时也提供".$mh_title.$title."、".$mh_title."漫画的情报、图透等信息，漫画城是一个综合的".$mh_title."在线漫画阅读网站。";?>
<?php include template("content","header"); ?>
<style type="text/css" >
.photo_prev a{cursor:url(<?php echo IMG_PATH;?>beta/prev.cur), auto;}
.photo_next a{cursor:url(<?php echo IMG_PATH;?>beta/next.cur), auto;}
</style>
<div class="left c666" style="padding-top:5px;">您现在的位置：&nbsp;<a class="LinkPath" href="<?php echo siteurl();?>">首页</a>&nbsp;&gt;&nbsp;<a class="LinkPath" href="/manhua">在线漫画</a>
		&gt;&nbsp;<a class="LinkPath" href="/manhua<?php echo $manhuaid;?>"><?php echo $mh_title;?></a>
		&gt;&nbsp;<?php echo $title;?>
    <div class="center" id='Article'>
		<h1><?php echo $mh_title;?><?php echo $title;?></h1>
		<script type="text/javascript">
/*漫画城文字广告940*150，创建于2013-5-30*/
var cpro_id = "u1294693";
</script>
<script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>
		<div class="vie_ins">很抱歉，“<?php echo $mh_title;?>”目前暂不开放在线观看， 请下载<a href="/api.php?op=download">漫画城</a>软件观看该漫画 。 </div>
	</div>    
	
	</div>
    <div class="middle_div1">
    	<div class="middle_div1_le" style="border:none;">
             <div class="clear1"></div>
		  </div>
        </div>
        <div class="middle_div1_ri">
        	<div class="tu"><?php include template("content","250ad"); ?></div>
            <div class="xh_man">
					<div class="rs_dm_tit">
						<h3>您可能会喜欢的漫画</h3>
					</div>

                <div class="xh_man_txt">
                	<ul>
					<?php $where = "typeid='$manhua_typeid' AND status='99' AND id != $manhuaid"?>
					<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=69b06e154ecb63cf26a1bbe548b45554&action=table_list&table=Cartoon&fields=id%2Ccatid%2Ctitle%2Cthumb&where=%24where&order=views+DESC&num=6&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'Cartoon','fields'=>'id,catid,title,thumb','where'=>$where,'order'=>'views DESC',)).'69b06e154ecb63cf26a1bbe548b45554');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','fields'=>'id,catid,title,thumb','where'=>$where,'order'=>'views DESC','limit'=>'6',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
						<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                    	<li>
                        	<a target="_blank" title="<?php echo $r['title'];?>" href="/manhua<?php echo $r['id'];?>"><img width=80 height=115 src="<?php echo thumb($r[thumb], 80, 115, 0);?>"></a>
                            <a target="_blank" title="<?php echo $r['title'];?>" href="/manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],20,'');?></a>
                        </li>
							<?php $n++;}unset($n); ?>
							<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>

                    </ul>
                </div>
            </div>
        </div>
    </div>
<div id="load_pic" style="display:none;" rel="<?php echo IMG_PATH;?>msg_img/loading_d.gif">
</div>
<script type="text/javascript" src="<?php echo JS_PATH;?>show_picture.js"></script>
<script type="text/javascript" src="<?php echo APP_PATH;?>api.php?op=count&id=<?php echo $id;?>&modelid=<?php echo $modelid;?>&type=<?php echo $parameters;?>"></script>
<?php include template("content","footer"); ?>