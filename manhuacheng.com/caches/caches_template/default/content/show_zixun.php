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
                </ul>

                <p class="info1">&nbsp;&nbsp;&nbsp;&nbsp;导读：<?php echo $description;?></p>
                <div class="content002"><?php echo $content;?></div>
                <script>
                    var baiduImagePlus = {
                        unionId:'u2130997',
                        noLogo:true,
                        formList:[{formId:2},{formId:8}]
                    };
                </script>
                <script src="http://cpro.baidustatic.com/cpro/ui/i.js"></script>
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
                <script type="text/javascript">
                    var cpro_id="u2130980";
                    (window["cproStyleApi"] = window["cproStyleApi"] || {})[cpro_id]={at:"3",rsi0:"738",rsi1:"250",pat:"6",tn:"baiduCustNativeAD",rss1:"#FFFFFF",conBW:"1",adp:"1",ptt:"0",titFF:"%E5%AE%8B%E4%BD%93",titFS:"14",rss2:"#000000",titSU:"0",ptbg:"90",piw:"0",pih:"0",ptp:"0"}
                </script>
                <script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>
            </div>
        </div>

        <div class="clear1"></div>

    </div>
    
    <div class="middle_div1_ri" style='margin-top: -8px;'>
        <?php include template("content","block_ranking"); ?>
        <div style="text-align: center;margin-top:5px">
            <script type="text/javascript">
                /*漫画城资讯内容页右上角矩形广告250*250 创建于 2015-06-01*/
                var cpro_id = "u2130965";
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
                    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=96f0d6e2e83cb5dfd4ec810c582de390&action=table_list&table=Cartoon&fields=id%2Ccatid%2Ctitle%2Cthumb&where=%24where&order=updatetime+DESC&num=4\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','fields'=>'id,catid,title,thumb','where'=>$where,'order'=>'updatetime DESC','limit'=>'4',));}?>
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
        <script type="text/javascript">
            /*漫画城资讯内容漫画情报上250*250 创建于 2015-06-01*/
            var cpro_id = "u2130995";
        </script>
        <script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>
        <div class="jiebao">
            <div class="rs_dm_tit">
                <h3><?php echo $CATEGORYS[$catid]['catname'];?></h3>
            </div>
            <div class="jiebao_txt">
                <ul>
                    <?php $where = "catid ='".$catid."' AND status = '99'";?>
                    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=8d4dedf41b7c5e0a675fb1c19c5b524c&action=table_list&table=news&fields=id%2Ctitle%2Curl&where=%24where&order=updatetime+DESC&num=10\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'news','fields'=>'id,title,url','where'=>$where,'order'=>'updatetime DESC','limit'=>'10',));}?>
                    <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                    <li><a target="_blank" href="<?php echo $r['url'];?>" title= "<?php echo $r['title'];?>"><?php echo str_cut($r[title],50,'..');?></a></li>
                    <?php $n++;}unset($n); ?>
                    <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                </ul>
            </div>
        </div>
        <script type="text/javascript">
            /*漫画城资讯内容页右侧悬停250*250 创建于 2015-06-01*/
            var cpro_id = "u2130996";
        </script>
        <script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>
    </div>
</div>
<script language="JavaScript" src="<?php echo APP_PATH;?>api.php?op=count&id=<?php echo $id;?>&modelid=<?php echo $modelid;?>&type=<?php echo $parameters;?>"></script>
<?php include template("content","footer"); ?>