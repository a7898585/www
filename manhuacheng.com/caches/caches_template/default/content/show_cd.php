<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php $w="id=$manhuaid"?>
<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=f16797a1a65041b74ff2b3f3d4b6a703&action=lists&catid=13&id=%24manhuaid&num=1\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('catid'=>'13','id'=>$manhuaid,'limit'=>'1',));}?>
<?php $n=1;if(is_array($data)) foreach($data AS $r1) { ?>
<?php $mh_title = $r1[title];?>
<?php $n++;}unset($n); ?>
<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
<?php $SEO['title']=$mh_title.$title."漫画_".$mh_title."漫画".$title."_".$mh_title."漫画-漫画城";?>
<?php $SEO['site_title']="";?>
<?php $SEO['keyword']=$mh_title.$title."，".$title."漫画";?>
<?php $SEO['description']="漫画城提供".$mh_title.$title."、".$mh_title."漫画".$title."第一时间更新，同时也提供".$mh_title.$title."、".$mh_title."漫画的情报、图透等信息，漫画城是一个综合的".$mh_title."在线漫画阅读网站。";?>
<?php $footer_txt="漫画城与您分享漫画".$mh_title."，以便漫画爱好者研究漫画".$mh_title."的画法技巧和构图方式。";?>

<?php include template("content","header"); ?>
<style>
    .photo_prev a{cursor:url(<?php echo IMG_PATH;?>beta/prev.cur), auto;}
    .photo_next a{cursor:url(<?php echo IMG_PATH;?>beta/next.cur), auto;}
    #big-pic img{ width:100%; height:auto}
</style>
<div class="left c666" style="padding-top:5px;">您现在的位置：&nbsp;<a class="LinkPath" href="<?php echo siteurl();?>">首页</a>&nbsp;&gt;&nbsp;<a class="LinkPath" href="/manhua">在线漫画</a>
    &gt;&nbsp;<a class="LinkPath" href="/manhua<?php echo $manhuaid;?>"><?php echo $mh_title;?></a>
    &gt;&nbsp;<?php echo $title;?>
    <div class="center" id='Article'>
        <span id="hits" style="display:none"><?php echo $views;?> 次</span>
        <div class="center_h1" ><h1><?php echo $mh_title;?><?php echo $title;?></h1>
            <?php if($liulanqi=='ie'){
            echo '<h2>IE浏览器不兼容，请使用主流浏览器<strong style="color:red;">谷歌</strong>或者<strong style="color:red;">火狐</strong>观看，效果最佳。</h2>';            }?><span class="stat" id="picnum"></div>
        <div class="big-pic">
            <div id="big-pic"></div>
            <div class="photo_prev"><a id="photoPrev" title="&lt;上一页" class="btn_pphoto" target="_self" hidefocus="true" href="javascript:;" onclick="showpic('pre');"></a></div>
            <div class="photo_next"><a id="photoNext" title="下一页&gt;"class="btn_nphoto" target="_self" hidefocus="true" href="javascript:;" onclick="showpic('next')"></a></div>
            <a href="javascript:;" class="max" onclick="showpic('big');">查看原图</a>

            <div id="endSelect" style="display: none;">
                <div id="endSelClose" onclick="$('#endSelect').hide();"></div>
                <div class="bg"></div>
                <div class="E_Cont">
                    <p>您已经看完这话了</p>
                    <p>
                        <a id="rePlayBut" href="javascript:void(0)" onclick="showpic('next', 1);"></a>
                        <?php if(!empty($next_page)) { ?>
                        <a id="nextPicsBut" href="/cartoondetail<?php echo $next_page['id'];?>"></a>
                        <?php } else { ?><span style="font-size: 12px;padding-left: 20px;">已经是最后一话了</span><?php } ?>
                    </p>	
                </div>
            </div>

        </div>

         <script type="text/javascript">
            var cpro_id="u2131285";
            (window["cproStyleApi"] = window["cproStyleApi"] || {})[cpro_id]={at:"3",rsi0:"1000",rsi1:"150",pat:"1",tn:"baiduCustNativeAD",rss1:"#FFFFFF",conBW:"1",adp:"1",ptt:"0",titFF:"%E5%AE%8B%E4%BD%93",titFS:"14",rss2:"#000000",titSU:"0",tft:"0",tlt:"1",ptbg:"90",piw:"0",pih:"0",ptp:"0"}
        </script>
        <script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>
        
        <div class="list-pic">
            <div class="pre picbig pager">
                <?php if(!empty($previous_page)) { ?>
                <a target="_self" class="zsshangz" href="/cartoondetail<?php echo $previous_page['id'];?>">上一话</a>
                <?php } else { ?>已经是第一话了<?php } ?>
            </div>
            <a href="javascript:;" onclick="showpic('pre')" class="pre-bnt"><span></span></a>
            <div class="cont" style="position:relative">
                <ul class="cont picbig" id="pictureurls"  style="position:absolute">
                    <?php $n=1; if(is_array($CartoonImage)) foreach($CartoonImage AS $pic_k => $r) { ?>
                    <?php
                    if(!strstr($r[url] ,"/public/getimg?url")){
                        $r[url] = "".$r[url]; 
                    } ?>
                    <li><div class="img-wrap"><a href="javascript:;" hidefocus="true"><img src="<?php echo thumb($r[url], 105, 155, 0);?>" style="width:180px;height: 255px;" alt="<?php echo $r['alt'];?>" rel="<?php echo $r['url'];?>"/></a></div></li>
                    <?php $n++;}unset($n); ?>
                </ul>
            </div>
            <a href="javascript:;" onclick="showpic('next')" class="next-bnt"><span></span></a>
            <div class="next picbig pager">
                <?php if(!empty($next_page)) { ?>
                <a target="_self" class="zsxiaz" href="/cartoondetail<?php echo $next_page['id'];?>">下一话</a>
                <?php } else { ?>已经是最后一话了<?php } ?>
            </div>
        </div>    
       
    </div>
    <div class="middle_div1">
        <div class="middle_div1_le" style="border:none;">
            <!-- <div class="ship">
                            <div class="ship_tit">
                                    <i class="icon"></i>
                                    <h3>看过此漫画的还看了视频</h3>   
                    <span><a href="#" target="_blank">更多>></a></span>    
                       </div>
               <div class="ship_txt">
                            <ul class="mod-pic1">
                                    <li><a target="_blank" title="源君物语" href="#"><img src="<?php echo IMG_PATH;?>img/u102_normal.png" alt=""><p>源君物语</p></a></li>
                                     <li><a target="_blank" title="给遙不可及的你" href="#"><img src="<?php echo IMG_PATH;?>img/u102_normal.png" alt=""><p>给遙不可及的你</p></a></li>
                                     <li><a target="_blank" title="源君物语" href="#"><img src="<?php echo IMG_PATH;?>img/u102_normal.png" alt=""><p>源君物语</p></a></li>
                                     <li><a target="_blank" title="给遙不可及的你" href="#"><img src="<?php echo IMG_PATH;?>img/u102_normal.png" alt=""><p>给遙不可及的你</p></a></li>
                         <li><a target="_blank" title="源君物语" href="#"><img src="<?php echo IMG_PATH;?>img/u102_normal.png" alt=""><p>源君物语</p></a></li>
                                     <li><a target="_blank" title="给遙不可及的你" href="#"><img src="<?php echo IMG_PATH;?>img/u102_normal.png" alt=""><p>给遙不可及的你</p></a></li>
                         
                      </ul>
               </div>
         </div> -->
            <div class="clear1"></div>
        </div>
    </div>
    <div class="middle_div1_ri">
        
        <div class="xh_man">
            <div class="rs_dm_tit">
                <h3>您可能会喜欢的漫画</h3>
            </div>

            <div class="xh_man_txt">
                <ul>
                    <?php $where = "typeid='$manhua_typeid' AND status='99' AND id != $manhuaid"?>
                    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=8406adf7298b6b9b7a3586e70867dcff&action=table_list&table=Cartoon&fields=id%2Ccatid%2Ctitle%2Cthumb&where=%24where&order=views+DESC&num=6\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','fields'=>'id,catid,title,thumb','where'=>$where,'order'=>'views DESC','limit'=>'6',));}?>
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
<a id="load_pic" style="display:none;" rel="<?php echo IMG_PATH;?>msg_img/loading_d.gif">
</a>
<script>var liulanqi = '<?php echo $liulanqi;?>';</script>
<script type="text/javascript" src="<?php echo JS_PATH;?>show_picture.js"></script>
<script language="JavaScript" src="<?php echo APP_PATH;?>api.php?op=count&id=<?php echo $id;?>&modelid=<?php echo $modelid;?>&type=<?php echo $parameters;?>"></script>
<?php include template("content","footer"); ?>