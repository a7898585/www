<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php $m = getboxname('12','type_id');?>
<?php $footer_txt="漫画城在线漫画频道为您提供".$title."漫画第一时间更新，还有最新的".$title."漫画资讯、".$title."漫画捷报，漫画城是综合的".$title."在线漫画阅读网站。";?>
<?php include template("content","header"); ?>

<style>
    .new_img {
        position: absolute;
        left: 60px;
    }

    .mh_copy1{
        position: relative;
    }
</style>

<div class="left c666" style="padding-top:5px;">您现在的位置：&nbsp;<a class="LinkPath" href="<?php echo siteurl();?>">首页</a>&nbsp;&gt;&nbsp;<a class="LinkPath" href="/manhua">在线漫画</a>&nbsp;&gt;&nbsp;<?php echo $title;?>
    <div class="middle_div3">
        <div class="middle_div3_le">
            <div class="mh_zz">
                <dl>
                    <dt><img src="<?php echo $thumb;?>" border="0" alt="<?php echo $title;?>"  width="120" height="160"/></dt>
                    <dd>
                        <span id="hits" style="display:none"><?php echo $views;?> 次</span>
                        <p>作者：<?php echo $Author;?></p>
                        <p>漫画状态：
                            <?php if($state==1) { ?>
                            <a href="/wanjie">完结</a>
                            <?php } else { ?>
                            <a href="/lianzai">连载</a>
                            <?php } ?>
                        </p>
                        <p>漫画类型：<a href="/type<?php echo $type_id;?>/">
                                <?php echo $m[$type_id];?></a></p>
                        <p>区域：<a href="/mh_diqu<?php echo $region['1'];?>"><?php echo $region['0'];?></a></p>
                        <p>更新时间：<?php echo date("Y年m月d日",strtotime($updatetime));?></p>
                        <?php if($state ==0) { ?>
                        <?php $last_m = date("Y年m月d日",mktime(0,0,0,date("m",strtotime($updatetime)),date("d",strtotime($updatetime))+7,date("Y",strtotime($updatetime))) );?>
                        <?php } else { ?>
                        <?php $last_m = '已完结';?>
                        <?php } ?>
                        <p>下次更新：<?php echo $last_m;?></p>
                    </dd>
                </dl>
            </div>
            <div style="text-align: center;padding-top:5px">
                <script type="text/javascript">
                    /*漫画城漫画简介同类型相关资讯200*200 创建于 2015-06-01*/
                    var cpro_id = "u2131283";
                </script>
                <script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>
            </div>
            <div class="tl_man middle_div1_ri" style="margin-bottom:5px;">
                <div class="rs_dm_tit">
                    <h3><?php echo $title;?>相关资讯</h3>
                </div>

                <div class="jiebao_txt">
                    <ul><?php $stitle= str_cutword($title,4,'');?>
                        <?php $where_jb = "status = '99' AND title LIKE '%$stitle%'";?>
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=ca2ae5362d053ada2117a7ff68bca444&action=table_list&table=news&fields=id%2Ctitle%2Curl&where=%24where_jb&order=updatetime+DESC&num=10\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'news','fields'=>'id,title,url','where'=>$where_jb,'order'=>'updatetime DESC','limit'=>'10',));}?>
                        <?php if(!empty($data)) { ?>
                        <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                        <li><a target="_blank" href="<?php echo $r['url'];?>" title= "<?php echo $r['title'];?>"><?php echo str_cut($r[title],40,'...');?></a></li>
                        <?php $n++;}unset($n); ?>
                        <?php } else { ?>
                        <li><p>暂无"<?php echo $title;?>"相关资讯</p></li>
                        <?php } ?>
                        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                    </ul>
                </div>
            </div>
            <div style="text-align: center;">
                <script type="text/javascript">
                    /*漫画城左侧列表矩形广告200*200 创建于 2015-06-01*/
                    var cpro_id = "u2131280";
                </script>
                <script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>
            </div>
            <div class="tl_man middle_div1_ri">
                <div class="rs_dm_tit">
                    <h3>同类型漫画</h3>
                </div>

                <div class="tl_man_txt">
                    <ul>
                        <?php $where = "type_id='$type_id' AND status ='99' AND id != $id";?>
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=8406adf7298b6b9b7a3586e70867dcff&action=table_list&table=Cartoon&fields=id%2Ccatid%2Ctitle%2Cthumb&where=%24where&order=views+DESC&num=6\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','fields'=>'id,catid,title,thumb','where'=>$where,'order'=>'views DESC','limit'=>'6',));}?>
                        <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                        <li>
                            <a href="/manhua<?php echo $r['id'];?>" title="<?php echo $r['title'];?>" target="_blank"><img width=80 height=115 src="<?php echo $r['thumb'];?>" /></a>
                            <a href="/manhua<?php echo $r['id'];?>" title="<?php echo $r['title'];?>" target="_blank"><?php echo str_cut($r[title],20,'');?></a>
                        </li>
                        <?php $n++;}unset($n); ?>
                        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                    </ul>
                </div>
            </div>

        </div>
        <div class="middle_div3_ri">
            <div class="middle_ten">
                <div class="middle_ten_le">
                    <div class="mos">
                        <div class="mos_tit">
                            <i class="icon"></i>
                            <h1><?php echo $title;?></h1>
                            <p style="background:#eee;float: right; color:#FF0000;padding: 0 10px; letter-spacing:1px"><span>预计更新时间:</span><?php echo $last_m;?></p>
                        </div>
                        <div class="mos_txt">
                            <div class="mos_txt_n">
                                <span style="font-weight:bold; color:#666666;"><?php echo $title;?>简介：</span>
                                <?php if(!empty($content)) { ?>
                                <?php if(strlen(strip_tags($content)) < 610) { ?>
                                <span><?php echo strip_tags($content);?></span>
                                <?php } else { ?>
                                <span id="LM1"><?php echo str_cut(strip_tags($content),610,'...');?><a onClick="javascript:ShowFLT(2)" href="javascript:void(null)">详细</a></span>
                                <span id="LM2" style="display:none"><?php echo strip_tags($content);?><a onClick="javascript:ShowFLT(1) " href="javascript:void(null)">收起</a></span>
                                <?php } ?>
                                <?php } else { ?>
                                <span>暂无漫画简介</span>
                                <?php } ?>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="middle_ten_ri">
                    <div class="tu"><script type="text/javascript">
                        /*漫画城漫画简介内容页右上角250*250 创建于 2015-06-01*/
                        var cpro_id = "u2131148";
                        </script>
                        <script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script></div>

                </div>
            </div>
            <div class="clear1"></div>
            <script type="text/javascript" src="<?php echo JS_PATH;?>ZeroClipboard.js"></script>
            <div class="clipBox">漫友注意啦：支持<strong><?php echo $title;?></strong>漫画，就赶快把阅读地址：<input onclick="copyToClipBoard(this)" value="<?php echo get_url();?>" class="clipInput" id="copy_text"><input type="button" onclick="copyToClipBoard(this)" value="复制" class="clipBtn" id="copy_btn">在博客、QQ、论坛签名档上告诉你的好友们</div>
            <div class="clear1"></div>
            <div class="clipBox" style="color:red;">由于近日漫画版权问题，请<a href="http://manhuacheng.com/download/" target="_blank" style="color:blue;text-decoration:underline;">下载客户端</a>后观看，给大家带来不便请谅解</div>

            <div style="margin-top:0px;" class="today">
                <div class="today_tit">
                    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"get\" data=\"op=get&tag_md5=1531cdefc706f45c9a2b0d84560757e6&sql=select+%2A+from+tt_CartoonDetail+where+manhuaid%3D%24id+group+by+title+order+by+listorder+%2Cshoworder+asc&num=9999\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}pc_base::load_sys_class("get_model", "model", 0);$get_db = new get_model();$r = $get_db->sql_query("select * from tt_CartoonDetail where manhuaid=$id group by title order by listorder ,showorder asc LIMIT 9999");while(($s = $get_db->fetch_next()) != false) {$a[] = $s;}$data = $a;unset($a);?>
                    <?php $mh_list = array_chunk($data,100);?>
                    <ul id="tab_list"> 
                        <?php $n=1; if(is_array($mh_list)) foreach($mh_list AS $key => $r) { ?>
                        <?php $key ++?>
                        <?php $start= current($r);?>
                        <?php $end= end($r);?>
                        <li title="<?php echo $start['title'];?>-<?php echo $end['title'];?>" class="<?php if(($key == 1)) { ?>on<?php } ?>" onClick="secBoard('tab_list','infolist_list',<?php echo $key;?>,'on','');GetSize();"><strong><?php echo $start['title'];?>-<?php echo $end['title'];?></strong></li>         
                        <?php $n++;}unset($n); ?>
                    </ul>
                </div>
                <div class="today_t">
                    <?php $n=1; if(is_array($mh_list)) foreach($mh_list AS $key => $v) { ?>
                    <?php $key++?>
                    <ul id="infolist_list_<?php echo $key;?>" class="nr6 lan2" style="display:<?php if(($key == 1)) { ?>block<?php } else { ?>none<?php } ?>">		
                        <?php $n=1;if(is_array($v)) foreach($v AS $val) { ?>
                        <li>
                            <a title="<?php echo $title;?><?php echo $val['title'];?>" id="<?php echo $val['id'];?>" href="<?php echo getCartoondetailUrl($id,$val[id]);?>" target="_blank"><?php echo str_cut($val[title],20,'');?></a>
                        </li>  
                        <?php $n++;}unset($n); ?>
                    </ul>
                    <?php $n++;}unset($n); ?>
                </div>
                <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
            </div>

            <div style="margin-top:12px;">
                <script type="text/javascript">
                    /*漫画城漫画简介卷章列表下770*90 创建于 2015-06-01*/
                    var cpro_id = "u2131264";
                </script>
                <script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>
            </div>

            <div class="pianduan" style="margin-top:12px;">
                <div class="pianduan_tit">
                    <i class="icon"></i>
                    <h3><?php echo $title;?>精彩片段</h3>       
                </div>
                <div class="pianduan_txt">
                    <ul class="mod-pic">
                        <?php $where_manhua="manhuaid=$id";?>
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=455c596474c7363c63d8bfec7440fbcc&action=lists&catid=14&where=%24where_manhua&num=5&order=views+desc\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('catid'=>'14','where'=>$where_manhua,'order'=>'views desc','limit'=>'5',));}?>
                        <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                        <li><a href="/cartoondetail<?php echo $r['id'];?>" title="<?php echo $title;?>" target="_blank"><img width="120" height="155" src="<?php echo $thumb;?>"><span><?php echo $r['title'];?></span><p><?php echo $title;?></p><p><?php echo $r['title'];?></p></a></li>
                        <?php $n++;}unset($n); ?>
                        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                    </ul>
                </div>
            </div>
            <!-- <div class="kn" style="margin:12px auto;">
                               <div class="kn_tit">
                                       <i class="icon"></i>
                                       <h3>您可能会喜欢的动漫</h3>       
                          </div>
                  <div class="kn_txt">
                               <ul class="mod-pic">
                                       <li><a href="#" title="源君物语" target="_blank"><img width="120" height="155" alt="" src="img/003.png"><p>源君物语</p></a></li>
                                        <li><a href="#" title="给遙不可及的你" target="_blank"><img width="120" height="155" alt="" src="img/003.png"><p>给遙不可及的你</p></a></li>
                                        <li><a href="#" title="源君物语" target="_blank"><img width="120" height="155" alt="" src="img/003.png"><p>源君物语</p></a></li>
                                        <li><a href="#" title="给遙不可及的你" target="_blank"><img width="120" height="155" alt="" src="img/003.png"><p>给遙不可及的你</p></a></li>
                            <li><a href="#" title="源君物语" target="_blank"><img width="120" height="155" alt="" src="img/003.png"><p>源君物语</p></a></li>
                         </ul>
                  </div>
            </div> -->

            <div class="clear1"></div>
            <script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>
        </div>
    </div>
    <script type="text/javascript">
        ZeroClipboard.setMoviePath("/statics/js/ZeroClipboard.swf");
        var clip = new ZeroClipboard.Client(); // 新建一个对象
        clip.setHandCursor( true ); // 设置鼠标为手型
        //clip.setText("Hello World！"); // 设置要复制的文本。
        // 注册一个 button，参数为 id。点击这个 button 就会复制。
        //这个 button 不一定要求是一个 input 按钮，也可以是其他 DOM 元素。
        clip.glue("copy_btn"); // 和上一句位置不可调换
        clip.addEventListener( "mouseOver", function(client) {
            var text = document.title +'\r\n'+ document.getElementById("copy_text").value;

            client.setText( text ); // 重新设置要复制的值
        });
        clip.addEventListener( 'complete', function(client, text) {
            alert(text+"\r\n复制成功,发给你的好友一起分享吧!");//复制完成后
        } );

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
        //function copyToClipBoard(el){
        //	var clipBoardContent = document.title +'\r\n'+ location.href +'';
        //	window.clipboardData.setData("Text",clipBoardContent);
        //	alert(clipBoardContent+'\r\n复制成功,发给你的好友一起分享吧!');
        //}
        // function SetHeight(height) {  
        //alert(height);
        //	document.getElementById("manhua_iframe").style.height=height+"px";
        //}
    </script>
    <script language="JavaScript" src="<?php echo APP_PATH;?>api.php?op=count&id=<?php echo $id;?>&modelid=<?php echo $modelid;?>&type=<?php echo $parameters;?>"></script>
    <?php include template("content","footer"); ?>
