<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php include template("content","header"); ?>
<div class="left c666">您现在的位置：&nbsp;<a class="LinkPath" href="/">首页</a>&nbsp;&gt;&nbsp;<a class="LinkPath" href="<?php echo $CATEGORYS[$catid]['url'];?>"><?php echo $CATEGORYS[$catid]['catname'];?></a>&nbsp;&gt;&nbsp;<?php echo $title;?></div>
<div class="middle_div1">
        <div class="middle_div1_le" style="border:none;">
            <div class="listpic2">
<div class="content001">
            <h1><?php echo $title;?></h1>
            <ul style="margin-left: 150px;">
              <li>来源：<?php echo $copyfrom;?></li>
              <li>作者：<?php echo $username;?></li>
              <li>发布时间：<?php echo $inputtime;?></li>
              <span id="hits" style="display:none"><?php echo $views;?> 次</span>
            </ul>
           
            <p class="info1">&nbsp;&nbsp;&nbsp;&nbsp;导读：<?php echo $description;?></p>
<div class="content002"><?php echo $content;?></div>
<div class="share">
<!-- Baidu Button BEGIN -->
    <div id="bdshare" class="bdshare_t bds_tools get-codes-bdshare">
        <span class="bds_more">分享到：</span>
        <a class="bds_qzone"></a>
        <a class="bds_tsina"></a>
        <a class="bds_tqq"></a>
        <a class="bds_renren"></a>
        <a class="shareCount"></a>
    </div>
<script type="text/javascript" id="bdshare_js" data="type=tools&amp;uid=6448618" ></script>
<script type="text/javascript" id="bdshell_js"></script>
<script type="text/javascript">
    document.getElementById("bdshell_js").src = "http://share.baidu.com/static/js/shell_v2.js?cdnversion=" + new Date().getHours();
</script>
<!-- Baidu Button END -->
</div>
<div class="clear1"></div>
<div style="border-bottom:1px solid #D6D6D6; width:700px; text-align:center;margin-left: 10px; padding-top: 10px;"></div>
<div class="clear1"></div>
<div class="daoh">
   <ul>    
    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=04edb43f3d0f03d372c5e90960af1c79&action=previous_next&id=%24id&modelid=1&catid=%24catid&return=data\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'previous_next')) {$data = $content_tag->previous_next(array('id'=>$id,'modelid'=>'1','catid'=>$catid,'limit'=>'20',));}?>
      <li><span style="font-size:14px; font-weight:bold;">上一篇：</span><a href="<?php echo $data['previous']['url'];?>" title="<?php echo $data['previous']['title'];?>"><?php echo str_cut($data['previous']['title'],60,'..');?></a></li>
      <li style="float:right;"><span style="font-size:14px; font-weight:bold;">下一篇：</span><a href="<?php echo $data['next']['url'];?>"  title="<?php echo $data['next']['title'];?>"><?php echo str_cut($data['next']['title'],60,'..');?></a></li>
    <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
   </ul>
</div>

<a href="http://www.yunzhuan.com" target="_blank" title="前往云赚公益平台" rel="nofollow"><img src="/statics/images/yz1.png" alt="云赚平台"></img></a>

</div>
</div>

<div class="clear1"></div>

</div>
        <div class="middle_div1_ri">
            <div class="tu"><?php include template("content","250ad"); ?></div>
<?php include template("content","block_ranking"); ?>
<div style="text-align: center;margin-top:5px">
<script type="text/javascript">
/*漫画城250*250，创建于2013-5-3*/
var cpro_id = "u1276126";
</script>
<script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>
</div>

             <div class="xh_man">
             
                <div class="rs_dm_tit">
                    <h3>今日推荐</h3>
                </div>

                <div class="xh_man_txt">
                    <ul>
                        <?php $where = "status=99 AND state = 1 AND type_id NOT IN(".dimplode(dexplode(NOT_ALLOW)).")";?>		
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=cab475194341192e6ddd09533d40d181&action=table_list&table=Cartoon&fields=id%2Ccatid%2Ctitle%2Cthumb&where=%24where&order=updatetime+DESC&num=4&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'Cartoon','fields'=>'id,catid,title,thumb','where'=>$where,'order'=>'updatetime DESC',)).'cab475194341192e6ddd09533d40d181');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','fields'=>'id,catid,title,thumb','where'=>$where,'order'=>'updatetime DESC','limit'=>'4',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
                            <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                                <li>
                                    <a href="/manhua<?php echo $r['id'];?>" title ="<?php echo $r['title'];?>" target="_blank"><img width=80 height=115 src="<?php echo $r['thumb'];?>"></a>
                                    <a href="/manhua<?php echo $r['id'];?>" title ="<?php echo $r['title'];?>" target="_blank"><?php echo str_cut($r[title],6,'');?></a>
                                </li>
                            <?php $n++;}unset($n); ?>
                        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                    </ul>
                </div>
            </div>
            <div class="jiebao">
                    <div class="rs_dm_tit">
                        <h3><?php echo $CATEGORYS[$catid]['catname'];?></h3>
                    </div>
                    <div class="jiebao_txt">
                        <ul>
                        <?php $where = "catid ='".$catid."' AND status = '99'";?>
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=26264ba2a0493dd46d28f011caf179ba&action=table_list&table=news&fields=id%2Ctitle%2Curl&where=%24where&order=updatetime+DESC&num=10&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'news','fields'=>'id,title,url','where'=>$where,'order'=>'updatetime DESC',)).'26264ba2a0493dd46d28f011caf179ba');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'news','fields'=>'id,title,url','where'=>$where,'order'=>'updatetime DESC','limit'=>'10',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
                            <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                                <li><a target="_blank" href="<?php echo $r['url'];?>" title= "<?php echo $r['title'];?>"><?php echo str_cut($r[title],50,'..');?></a></li>
                            <?php $n++;}unset($n); ?>
                        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                        </ul>
                    </div>
                </div>
           
        </div>
    </div>
<script language="JavaScript" src="<?php echo APP_PATH;?>api.php?op=count&id=<?php echo $id;?>&modelid=<?php echo $modelid;?>&type=<?php echo $parameters;?>"></script>
<?php include template("content","footer"); ?>