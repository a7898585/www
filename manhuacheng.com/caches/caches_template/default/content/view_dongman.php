<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php $SEO['title']= $views['title']."_".$views['title']."_".$views['title']."动漫在线观看-漫画城";?>
<?php $SEO['keyword']= $views['title']."，".$views['title']."动漫电影，".$views['title']."动漫下载";?>
<?php $SEO['description']=$views['title']."是漫画城为".$title."动漫迷提供".$views['title']."最新在线观看。";?>
<?php $footer_txt="漫画城为您提供".$views['title']."，动漫".$views['title']."播放，".$views['title']."在线观看。";?>


<?php include template("content","header"); ?>
<style type="text/css">
    body,ul,li { padding:0; margin:0}
    ul,li { list-style:none}
    .a-scroll { position:relative; margin:5px auto; width:680px;}
    .a-scroll .prev,.a-scroll .next { position:absolute; display:block; width:50px; height:100px; background-color:#666666;top:0; color:#FFF; text-align:center; }
    .a-scroll .prev { left:0;cursor:pointer; background:url("<?php echo IMG_PATH;?>img/fx.jpg") no-repeat;}
    .a-scroll .next { right:0;cursor:pointer; background:url("<?php echo IMG_PATH;?>img/fx1.jpg") no-repeat;}
    .a-list { position:relative; width:550px; height:100px; margin-left:68px; overflow:hidden}
    .a-list ul { width:9999px; position:absolute;margin-top: 6px;}
    .a-list ul a { width:101px; height:67px; color:#000000; text-decoration:none; }
    .a-list li { float:left; display:inline; width:101px; margin-right:10px; height:67px; text-align:center; }
    .a-list ul a img { width:98px; height:65px; padding:2px; }
    .a-list ul a span { font-size:12px; color:#666666; display:inline-block; margin:5px 0px 0px 0px; }
    .a-list ul a:hover span { text-decoration:underline; color:#ff9900; }
    .a-list ul a:hover img { border:1px solid #999999; padding:1px; }
    .a-list ul .on { border:2px solid #F90; padding:1px; text-decoration:underline; color:#ff9900;  }

</style>
<div class="left c666">您现在的位置：&nbsp;<a class="LinkPath" href="/">首页</a>&nbsp;&gt;&nbsp;<a class="LinkPath" href="/dongman">动漫城</a>&nbsp;&gt;&nbsp;<a class="LinkPath" href="/dongman<?php echo $id;?>"><?php echo $title;?></a>&nbsp;&gt;&nbsp;<?php echo $views['title'];?></div>
<div class="middle_div1">
    <div class="middle_div1_le" style="border:none;">
        <div class="bf">
            <div class="bf_tit">
                <i class="icon"></i>
                <span id="hits" style="display:none"><?php echo $views['views'];?> 次</span>
                <h1><?php echo $title;?><span style="padding-left:10px; font-size:12px; font-weight:bold;"><?php echo $views['title'];?></span></h1> 
            </div>
            <div class="bf_txt">
                <div class="shiping">             
                    <embed type="application/x-shockwave-flash" src="<?php if($uuu) { ?><?php } else { ?><?php echo $uuu;?><?php echo $views['url'];?><?php } ?>" id="movie_player" name="movie_player" bgcolor="#FFFFFF" quality="high" allowfullscreen="true" flashvars="isShowRelatedVideo=false&amp;showAd=0&amp;show_pre=1&amp;show_next=1&amp;isAutoPlay=false&amp;isDebug=false&amp;UserID=&amp;winType=interior&amp;playMovie=true&amp;MMControl=false&amp;MMout=false" pluginspage="http://www.macromedia.com/go/getflashplayer" width="650" height="550" />	</embed>
                </div>
                
        <script type="text/javascript">
            /*漫画城动漫728*90，创建于2013-5-30*/
            var cpro_id = "u1294697";
        </script>
        <script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>
                <div class="tp_n">
                    <div class="a-scroll">
                        <span class="prev">&nbsp;</span>
                        <span class="next">&nbsp;</span>
                        <div class="a-list">
                            <ul>
                                <?php $where = "CartoonID=".$id." AND id >= ".$vid." AND status = 99";?>
                                <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=1767063efadc79feb6b292e1bb4a64b2&action=table_list&table=dongmanDetail&where=%24where&order=id+asc&num=7\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'dongmanDetail','where'=>$where,'order'=>'id asc','limit'=>'7',));}?>
                                <?php $n=1;if(is_array($data)) foreach($data AS $val) { ?>
                                <li <?php if($val['id'] == $vid) { ?>class="on"<?php } ?>>
                                    <a title="<?php echo $val['title'];?>" href="/dongman<?php echo $id;?>-<?php echo $val['id'];?>"><img src="<?php echo $val['thumb'];?>" title="<?php echo $val['title'];?>">
                                        <span><?php echo $val['title'];?></span>
                                    </a>
                                </li>
                                <?php $n++;}unset($n); ?>
                                <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                                <?php $where = 'CartoonID='.$id.' AND id < '.$vid.' AND status = 99';?>
                                <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=8175ee0d030308a70a234fc9f31d4285&action=table_list&table=dongmanDetail&where=%24where&order=id+asc&num=8\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'dongmanDetail','where'=>$where,'order'=>'id asc','limit'=>'8',));}?>
                                <?php $n=1;if(is_array($data)) foreach($data AS $val) { ?>
                                <li>
                                    <a title="<?php echo $val['title'];?>" href="/dongman<?php echo $id;?>-<?php echo $val['id'];?>"><img src="<?php echo $val['thumb'];?>" title="<?php echo $val['title'];?>">
                                        <span><?php echo $val['title'];?></span>
                                    </a>
                                </li>
                                <?php $n++;}unset($n); ?>
                                <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="kn1">
            <div class="kn1_tit">
                <i class="icon"></i>
                <h3>喜欢《<?php echo $title;?>》的也在看</h3>       
            </div>
            <div class="kn1_txt">
                <ul class="mod-pic">
                    <?php $sql = "SELECT id,title,thumb FROM tt_dongman WHERE id >= ((SELECT MAX(id) FROM tt_dongman)-(SELECT MIN(id) FROM tt_dongman)) * RAND() + (SELECT MIN(id) FROM tt_dongman) AND status=99 AND id !='$id' AND catid='$catid'";?>
                    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"get\" data=\"op=get&tag_md5=239fde3fafe496af856c9af8d5c9d644&sql=%24sql&num=5\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}pc_base::load_sys_class("get_model", "model", 0);$get_db = new get_model();$r = $get_db->sql_query("$sql LIMIT 5");while(($s = $get_db->fetch_next()) != false) {$a[] = $s;}$data = $a;unset($a);?>
                    <?php $n=1;if(is_array($data)) foreach($data AS $val) { ?>
                    <li><a target="_blank" title="<?php echo $val['title'];?>" href="/dongman<?php echo $val['id'];?>"><img width="120" height="155" src="<?php echo $val['thumb'];?>"><p><?php echo str_cut($val[title],18,'...');?></p></a></li>
                    <?php $n++;}unset($n); ?>
                    <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                </ul>
            </div>
        </div>
    </div>
    <div class="middle_div1_ri">
        <div class="tu"><?php include template("content","250ad"); ?></div>
        <div class="xh_man">
            <div class="rs_dm_tit">
                <h3>同类型推荐</h3>
            </div>
            <div class="xh_man_txt">
                <ul>
                    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=76ce65db84ce8f249295b29ca570db2a&action=lists&modelid=17&where=%24where_sql&order=updatetime+desc&num=4\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('modelid'=>'17','where'=>$where_sql,'order'=>'updatetime desc','limit'=>'4',));}?>
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
        <div class="rs_dm">
            <div class="rs_dm_tit"><h3>人气动漫排行</h3></div>
            <div class="rs_dm_txt">
                <ul>
                    <?php $i=1;?>
                    <?php $class= array(1=>'first',2=>'second',3=>'third')?>
                    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=e733650ca8d0752e2c07dbeeddb84632&action=table_list&table=dongman&where=status%3D99&order=views+DESC&num=10\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'dongman','where'=>'status=99','order'=>'views DESC','limit'=>'10',));}?>
                    <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                    <li><i class="<?php echo $class[$i];?>"><?php echo $i;?></i><a href="/dongman<?php echo $r['id'];?>"><?php echo str_cut($r[title],25,"");?></a></li>
                    <?php $i++?>
                    <?php $n++;}unset($n); ?>
                    <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                </ul>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript">
            function DY_scroll(wraper, prev, next, a, speed, or)
            {
                var wraper = $(wraper);
                var prev = $(prev);
                var next = $(next);
                var a = $(a).find('ul');
                var w = a.find('li').outerWidth(true);
                var s = speed;
                next.click(function ()
                {
                    a.animate({'margin-left': -w}, function ()
                    {
                        a.find('li').eq(0).appendTo(a);
                        a.css({'margin-left': 0});
                    });
                });
                prev.click(function ()
                {
                    a.find('li:last').prependTo(a);
                    a.css({'margin-left': -w});
                    a.animate({'margin-left': 0});
                });
                if (or == true)
                {
                    ad = setInterval(function () {
                        next.click();
                    }, s * 1000);
                    wraper.hover(function () {
                        clearInterval(ad);
                    }, function () {
                        ad = setInterval(function () {
                            next.click();
                        }, s * 1000);
                    });

                }
            }
            DY_scroll('.a-scroll', '.prev', '.next', '.a-list', 3, false);// true为自动播放，不加此参数或false就默认不自动
</script>
<script language="JavaScript" src="<?php echo APP_PATH;?>api.php?op=count&id=<?php echo $vid;?>&modelid=18&type=<?php echo $parameters;?>"></script>
<?php include template("content","footer"); ?>