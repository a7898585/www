<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php include template("content","header"); ?>
<!--main-->
<?php $where = "status = '99' AND type_id NOT IN(".dimplode(dexplode(NOT_ALLOW)).")";?>	
<?php $footer_txt = "漫画城网为您提供在线漫画阅读、动漫观看，还有最新的漫画资讯、漫画捷报和最齐全的漫画排行榜，并且还为您提供漫画下载器——漫画城软件的下载，是您轻松快速体验高清漫画、动漫的最好选择。";?>
<?php $m= getboxname('12','type_id'); ?> 
<?php $region= getboxname('12','region');?> 

<script src="<?php echo JS_PATH;?>dww3.min.js"></script>
<div class="content">
    <div class="home_banner">
        <div class="home_banner_le">
            <!-- 幻灯片 -->
            <div id="slider" class="mod-slide-s4">
                <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=0f0ec6b5e1d1c9195472d7808b725e30&action=lists&catid=75&order=updatetime+DESC&num=4\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('catid'=>'75','order'=>'updatetime DESC','limit'=>'4',));}?>
                <ul class="mod-slide-content J_content">
                    <?php $i = 1;?>
                    <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                    <li <?php if($i > 1) { ?>style="display: none;"<?php } else { ?>style="display: block;"<?php } ?>>
                        <a href="<?php echo $r['url'];?>" title="<?php echo $r['title'];?>" target="_blank"><img alt="<?php echo $r['title'];?>" src="<?php echo $r['thumb'];?>" class="big_img"></a>
                        <div><h2><?php echo $r['title'];?></h2><?php echo str_cut($r[description],80,'...');?></div>
                    </li>
                    <?php $i++;?>
                    <?php $n++;}unset($n); ?>
                </ul>
                <ul class="mod-slide-trigger J_nav">
                    <?php $i = 1;?>
                    <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                    <li <?php if($i == 1) { ?>class="hover"<?php } ?>>
                        <a href="<?php echo $r['url'];?>" title="<?php echo $r['title'];?>" target="_blank"><img src="<?php echo $r['thumb'];?>" class="thumb_img"></a>
                    </li>
                    <?php $i++;?>
                    <?php $n++;}unset($n); ?>
                </ul>
                <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
            </div>
            <script>
                KISSDW.slide("#slider");
            </script>
            <!-- 幻灯片end -->
            <!--原生广告-->

            <!--原生广告-->

        </div>
        <div class="home_banner_ri">
            <div class="xl">
                <div class="xl_t">
                    <h3><a target="_blank" href="/manhua">漫画城  &gt;&gt;</a></h3>
                    <p class="rm">
                        <a href="/lianzai" target="_blank" style="color:#FF0000;">热门连载</a>	<a href="/wanjie" target="_blank" style="color:#FF0000;">完结漫画</a>	
                        <?php $n=1; if(is_array($m)) foreach($m AS $n => $l) { ?>
                        <?php if(!in_array($n,dexplode(NOT_ALLOW))) { ?>
                        <a href="/type<?php echo $n;?>/" target="_blank"><?php echo $l;?></a>
                        <?php } ?>
                        <?php $n++;}unset($n); ?>
                    </p>
                    <p class="dq">地区：
                        <?php $n=1; if(is_array($region)) foreach($region AS $n => $l) { ?>
                        <a href="/mh_diqu<?php echo $n;?>/" target="_blank"><?php echo $l;?></a>
                        <?php $n++;}unset($n); ?>
                    </p>
                    <p class="zm">字母索引：
                        <?php $Q=NULL;?>
                        <?php for ($i=65; $i<=90; $i++) $Q[]=chr($i);?>
                        <?php $n=1;if(is_array($Q)) foreach($Q AS $r) { ?>
                        <a href="/index<?php echo $r;?>/"><?php echo $r;?></a>
                        <?php $n++;}unset($n); ?>
                    </p>
                </div>
                <div class="xl_t">
                    <h3><a target="_blank" href="/dongman">动漫城  &gt;&gt;</a></h3>
                    <p class="rm1">
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=dff55dd2a1d5357c731d5ff553c04897&action=category&catid=32&order=listorder+ASC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'category')) {$data = $content_tag->category(array('catid'=>'32','order'=>'listorder ASC','limit'=>'20',));}?>
                        <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                        <a href="<?php echo $r['url'];?>" target="_blank"><?php echo $r['catname'];?></a>
                        <?php $n++;}unset($n); ?>
                        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                    </p>
                    <p class="dq">
                        <?php $sql = "select linkageid,name from tt_linkage where keyid=3360 order by listorder desc";?>
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"get\" data=\"op=get&tag_md5=792af174a6532d1c0d87d56e6f760d1b&sql=%24sql\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}pc_base::load_sys_class("get_model", "model", 0);$get_db = new get_model();$r = $get_db->sql_query("$sql LIMIT 20");while(($s = $get_db->fetch_next()) != false) {$a[] = $s;}$data = $a;unset($a);?>
                        <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                        <a target="_blank" href="/dm_diqu<?php echo $r['linkageid'];?>"><?php echo $r['name'];?></a>
                        <?php $n++;}unset($n); ?>
                        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                    </p>
                </div>
            </div><!--原生广告-->

            <!--原生广告-->
        </div>

    </div>
</div>
<div class="clear1"></div>
<div class="zuijin">
    <div class="zuijin_tit">
        <i class="icon"></i>
        <h3>最近更新</h3>
        <span><a href="/manhua" target="_blank">更多>></a></span> 
        <ul>
            <?php $n=1; if(is_array($m)) foreach($m AS $n => $l) { ?>
            <?php if(!in_array($n,dexplode(NOT_ALLOW))) { ?>
            <li><a href="/type<?php echo $n;?>/" target="_blank"><?php echo $l;?></a></li>
            <?php } ?>
            <?php $n++;}unset($n); ?>
        </ul>    

    </div>
    <div class="zuijin_txt">
        <ul class="mod-pic">
            <?php $sql = "select cd.web_url,cd.id,c.title as c_title,c.thumb,cd.title,cd.manhuaid from (select * from tt_CartoonDetail where manhuaid != 0 order by id desc limit 800) as cd LEFT JOIN tt_Cartoon as c ON cd.manhuaid = c.id where cd.catid = '14'  AND c.status = '99' AND c.type_id NOT IN(".dimplode(dexplode(NOT_ALLOW)).") GROUP BY c.id order by cd.id desc";?>
            <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"get\" data=\"op=get&tag_md5=da93596d8b9ce7168056e3ef7abcfc67&sql=%24sql&num=7\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}pc_base::load_sys_class("get_model", "model", 0);$get_db = new get_model();$r = $get_db->sql_query("$sql LIMIT 7");while(($s = $get_db->fetch_next()) != false) {$a[] = $s;}$data = $a;unset($a);?>
            <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
            <li><a target="_blank" title="<?php echo $r['c_title'];?>-<?php echo $r['title'];?>" href="<?php echo getCartoondetailUrl($r[manhuaid],$r[id]);?>"><img width="105" height="150" src="<?php echo $r['thumb'];?>"><span><?php echo $r['title'];?></span><em><?php echo str_cut($r[c_title],25,'');?></em></a></li>
            <?php $n++;}unset($n); ?>
            <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>

        </ul>
    </div>
</div>
<div class="middle_div">
    <div class="middle_div_le">
        <div class="today">
            <div  class="today_tit">
                <span><a href="/manhua">更多>></a></span>
                <ul id="tab_a">                
                    <li onmouseover="secBoard('tab_a','infolist_a',1,'on','');" class="on"><strong>今日漫画</strong></li> <li onmouseover="secBoard('tab_a','infolist_a',2,'on','');" class=""><strong>最新章节</strong></li>  

                </ul>
            </div>
            <div class="today_txt">
                <ul id="infolist_a_1" style="display:block">
                    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=e9d1c768a1e78079c58b0c45b1a623f3&action=position&posid=14&order=updatetime++DESC&sort=desc&num=15\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'position')) {$data = $content_tag->position(array('posid'=>'14','order'=>'updatetime  DESC','sort'=>'desc','limit'=>'15',));}?>
                    <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                    <li>
                        <a target="_blank" title="<?php echo $r['title'];?>" href="/manhua<?php echo $r['id'];?>"><img  width='115' height='130' src="<?php echo $r['thumb'];?>"></a>
                        <a target="_blank"  title="<?php echo $r['title'];?>" href="/manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],25,'');?></a>
                    </li>
                    <?php $n++;}unset($n); ?>
                    <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                </ul>
                <ul id="infolist_a_2" style="display:none">
                    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"get\" data=\"op=get&tag_md5=63a24785abd4af6ba98b330b60208216&sql=%24sql+&num=15\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}pc_base::load_sys_class("get_model", "model", 0);$get_db = new get_model();$r = $get_db->sql_query("$sql  LIMIT 15");while(($s = $get_db->fetch_next()) != false) {$a[] = $s;}$data = $a;unset($a);?>
                    <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                    <li>
                        <a target="_blank"  title="<?php echo $r['c_title'];?>-<?php echo $r['title'];?>"  href="<?php echo getCartoondetailUrl($r[manhuaid],$r[id]);?>"><img width='115' height='130' src="<?php echo $r['thumb'];?>"></a>
                        <a target="_blank"  title="<?php echo $r['c_title'];?>-<?php echo $r['title'];?>"  href="<?php echo getCartoondetailUrl($r[manhuaid],$r[id]);?>"><?php echo str_cut($r[c_title],25,'');?></a>
                    </li>
                    <?php $n++;}unset($n); ?>
                    <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                </ul>
            </div>
        </div>
        <div class="today1">
            <div class="today1_tit">
                <span><a href="/manhua">更多>></a></span>  
                <ul id= "tab_b">                
                    <li onmouseover="secBoard('tab_b','infolist_b',1,'on','');" class="on"><strong>完结漫画</strong></li>                
                    <li onmouseover="secBoard('tab_b','infolist_b',2,'on','');" ><strong>连载漫画</strong></li>     
                </ul>
            </div>
            <div class="today1_txt">
                <div id="infolist_b_1" style="display:block" >
                    <ul class="today1_txt1">
                        <?php $where_wanjie = $where." AND state = 1";?>		
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=738bb57dd895da389fa1bb20df537267&action=table_list&table=Cartoon&where=%24where_wanjie&order=updatetime+DESC&num=20\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','where'=>$where_wanjie,'order'=>'updatetime DESC','limit'=>'20',));}?>
                        <?php $i = 1;?>
                        <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                        <?php if($i <= 10) { ?>
                        <li>
                            <a href="/manhua<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><img width='115' height='130' src="<?php echo $r['thumb'];?>"/></a>
                            <a href="/manhua<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],25,'');?></a>
                        </li>
                        <?php } ?>
                        <?php if($i == 10) { ?></ul><ul class="today1_txt2"><?php } ?>
                        <?php if($i > 10) { ?>
                        <li><span><?php echo date('Y-m-d',$r[updatetime]);?></span><a href="/manhua<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],50,'');?></a></li>
                        <?php } ?>
                        <?php $i++;?>
                        <?php $n++;}unset($n); ?>
                        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                    </ul>
                </div>
                <div id="infolist_b_2" style="display:none">
                    <ul class="today1_txt1">
                        <?php $where_lianzai = $where." AND state = 0";?>		
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=623be6fe01521f038f6905e9ad19e277&action=table_list&table=Cartoon&where=%24where_lianzai&order=updatetime+DESC&num=20\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','where'=>$where_lianzai,'order'=>'updatetime DESC','limit'=>'20',));}?>
                        <?php $i = 1;?>
                        <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                        <?php if($i <= 10) { ?>
                        <li>
                            <a href="/manhua<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><img width='115' height='130' src="<?php echo $r['thumb'];?>"/></a>
                            <a href="/manhua<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],25,'');?></a>
                        </li>
                        <?php } ?>
                        <?php if($i == 10) { ?></ul><ul class="today1_txt2"><?php } ?>
                        <?php if($i > 10) { ?>
                        <li><span><?php echo date('Y-m-d',$r[updatetime]);?></span><a href="/manhua<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],50,'');?></a></li>
                        <?php } ?>
                        <?php $i++;?>
                        <?php $n++;}unset($n); ?>
                        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                    </ul>
                </div>

            </div>
        </div>

        <!-- <div class="today1">
                <div class="today1_tit">
                <span><a href="/manhua">更多>></a></span>  
                <ul id= "tab_b">                
                        <li onmouseover="secBoard('tab_c','infolist_c',1,'on','');" class="on"><strong>国产动漫</strong></li>                
                                                <li onmouseover="secBoard('tab_c','infolist_c',2,'on','');" ><strong>港台动漫</strong></li>  
                                                <li onmouseover="secBoard('tab_c','infolist_c',3,'on','');" ><strong>日韩动漫</strong></li>                
                                                <li onmouseover="secBoard('tab_c','infolist_c',4,'on','');" ><strong>欧美动漫</strong></li>   
                </ul>
            </div>
            <div class="today1_txt">
                                        <div id="infolist_c_1" style="display:block" >
                                                <ul class="today1_txt1">
                                                <?php $where_wanjie = $where." AND state = 1";?>		
                                                <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=1ebaa427c55e9b1c58d68bdb7e994dca&action=table_list&table=Cartoon&where=%24where_wanjie&order=updatetime+DESC&num=15\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','where'=>$where_wanjie,'order'=>'updatetime DESC','limit'=>'15',));}?>
                                                <?php $i = 1;?>
                                                  <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                                                        <?php if($i <= 5) { ?>
                                                        <li>
                                                                <a href="/manhua<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><img width='115' height='130' src="<?php echo $r['thumb'];?>"/></a>
                                                                <a href="/manhua<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],25,'');?></a>
                                                        </li>
                                                        <?php } ?>
                                                <?php if($i == 5) { ?></ul><ul class="today1_txt2"><?php } ?>
                                                        <?php if($i > 5) { ?>
                                                        <li><span><?php echo date('Y-m-d',$r[updatetime]);?></span><a href="/manhua<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],50,'');?></a></li>
                                                        <?php } ?>
                                                 <?php $i++;?>
                                                 <?php $n++;}unset($n); ?>
                                                <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                                                </ul>
                                        </div>
                                        <div id="infolist_c_2" style="display:none">
                                                <ul class="today1_txt1">
                                                <?php $where_lianzai = $where." AND state = 0";?>		
                                                <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=08b6d045850b3d815aef6d3866e3f28a&action=table_list&table=Cartoon&where=%24where_lianzai&order=updatetime+DESC&num=15\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','where'=>$where_lianzai,'order'=>'updatetime DESC','limit'=>'15',));}?>
                                                <?php $i = 1;?>
                                                  <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                                                        <?php if($i <= 5) { ?>
                                                        <li>
                                                                <a href="/manhua<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><img width='115' height='130' src="<?php echo $r['thumb'];?>"/></a>
                                                                <a href="/manhua<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],25,'');?></a>
                                                        </li>
                                                        <?php } ?>
                                                <?php if($i == 5) { ?></ul><ul class="today1_txt2"><?php } ?>
                                                        <?php if($i > 5) { ?>
                                                        <li><span><?php echo date('Y-m-d',$r[updatetime]);?></span><a href="/manhua<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],50,'');?></a></li>
                                                        <?php } ?>
                                                 <?php $i++;?>
                                                 <?php $n++;}unset($n); ?>
                                                <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                                                </ul>
                                        </div>
                <div id="infolist_c_3" style="display:none" >
                                                <ul class="today1_txt1">
                                                <?php $where_wanjie = $where." AND state = 1";?>		
                                                <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=1ebaa427c55e9b1c58d68bdb7e994dca&action=table_list&table=Cartoon&where=%24where_wanjie&order=updatetime+DESC&num=15\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','where'=>$where_wanjie,'order'=>'updatetime DESC','limit'=>'15',));}?>
                                                <?php $i = 1;?>
                                                  <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                                                        <?php if($i <= 5) { ?>
                                                        <li>
                                                                <a href="/manhua<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><img width='115' height='130' src="<?php echo $r['thumb'];?>"/></a>
                                                                <a href="/manhua<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],25,'');?></a>
                                                        </li>
                                                        <?php } ?>
                                                <?php if($i == 5) { ?></ul><ul class="today1_txt2"><?php } ?>
                                                        <?php if($i > 5) { ?>
                                                        <li><span><?php echo date('Y-m-d',$r[updatetime]);?></span><a href="/manhua<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],50,'');?></a></li>
                                                        <?php } ?>
                                                 <?php $i++;?>
                                                 <?php $n++;}unset($n); ?>
                                                <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                                                </ul>
                                        </div>
                                        <div id="infolist_c_4" style="display:none">
                                                <ul class="today1_txt1">
                                                <?php $where_lianzai = $where." AND state = 0";?>		
                                                <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=08b6d045850b3d815aef6d3866e3f28a&action=table_list&table=Cartoon&where=%24where_lianzai&order=updatetime+DESC&num=15\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','where'=>$where_lianzai,'order'=>'updatetime DESC','limit'=>'15',));}?>
                                                <?php $i = 1;?>
                                                  <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                                                        <?php if($i <= 5) { ?>
                                                        <li>
                                                                <a href="/manhua<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><img width='115' height='130' src="<?php echo $r['thumb'];?>"/></a>
                                                                <a href="/manhua<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],25,'');?></a>
                                                        </li>
                                                        <?php } ?>
                                                <?php if($i == 5) { ?></ul><ul class="today1_txt2"><?php } ?>
                                                        <?php if($i > 5) { ?>
                                                        <li><span><?php echo date('Y-m-d',$r[updatetime]);?></span><a href="/manhua<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],50,'');?></a></li>
                                                        <?php } ?>
                                                 <?php $i++;?>
                                                 <?php $n++;}unset($n); ?>
                                                <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                                                </ul>
                                        </div>
            </div>
        </div> -->
        <div class="dongman">
            <div class="dongman_tit">
                <span><a href="/dongman">更多&gt;&gt;</a></span>  
                <ul id ="tab_c">                
                    <li class="on" onmouseover="secBoard('tab_c','infolist_c',1,'on','');"><strong>国产动漫</strong></li>                
                    <li onmouseover="secBoard('tab_c','infolist_c',2,'on','');"><strong>港台动漫</strong></li>                
                    <li onmouseover="secBoard('tab_c','infolist_c',3,'on','');"><strong>日韩动漫</strong></li>                
                    <li onmouseover="secBoard('tab_c','infolist_c',4,'on','');"><strong>欧美动漫</strong></li>  
                </ul>
            </div>
            <div class="dongman_txt">
                <ul class="dongman_txt1" id="infolist_c_1" style="display:block">
                    <?php $where1 = " status = 99 AND CartoonArea = 3364";?>
                    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=11351c17ab0e6c91f8f77e987a6ebb09&action=table_list&table=dongman&where=%24where1&order=updatetime+DESC&num=15\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'dongman','where'=>$where1,'order'=>'updatetime DESC','limit'=>'15',));}?>
                    <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                    <li>
                        <a href="/dongman<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><img width='115' height='130' src="<?php echo $r['thumb'];?>"/></a>
                        <a href="/dongman<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],25,'');?></a>
                    </li>
                    <?php $n++;}unset($n); ?>
                    <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                </ul>
                <ul class="dongman_txt1" id="infolist_c_2" style="display:none">
                    <?php $where1 = " status = 99 AND CartoonArea = 3362";?>
                    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=11351c17ab0e6c91f8f77e987a6ebb09&action=table_list&table=dongman&where=%24where1&order=updatetime+DESC&num=15\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'dongman','where'=>$where1,'order'=>'updatetime DESC','limit'=>'15',));}?>
                    <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                    <li>
                        <a href="/dongman<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><img width='115' height='130' src="<?php echo $r['thumb'];?>"/></a>
                        <a href="/dongman<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],25,'');?></a>
                    </li>
                    <?php $n++;}unset($n); ?>
                    <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                </ul>
                <ul class="dongman_txt1" id="infolist_c_3" style="display:none">
                    <?php $where1 = " status = 99 AND CartoonArea = 3361";?>
                    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=11351c17ab0e6c91f8f77e987a6ebb09&action=table_list&table=dongman&where=%24where1&order=updatetime+DESC&num=15\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'dongman','where'=>$where1,'order'=>'updatetime DESC','limit'=>'15',));}?>
                    <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                    <li>
                        <a href="/dongman<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><img width='115' height='130' src="<?php echo $r['thumb'];?>"/></a>
                        <a href="/dongman<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],25,'');?></a>
                    </li>
                    <?php $n++;}unset($n); ?>
                    <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                </ul>
                <ul class="dongman_txt1" id="infolist_c_4" style="display:none">
                    <?php $where1 = " status = 99 AND CartoonArea = 3363";?>
                    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=11351c17ab0e6c91f8f77e987a6ebb09&action=table_list&table=dongman&where=%24where1&order=updatetime+DESC&num=15\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'dongman','where'=>$where1,'order'=>'updatetime DESC','limit'=>'15',));}?>
                    <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                    <li>
                        <a href="/dongman<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><img width='115' height='130' src="<?php echo $r['thumb'];?>"/></a>
                        <a href="/dongman<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],25,'');?></a>
                    </li>
                    <?php $n++;}unset($n); ?>
                    <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                </ul>
            </div>
        </div>

    </div>
    <div class="middle_div_ri">
        <div class="manhua" style="margin-bottom:10px;">
            <div class="jiebao_txt" id="list_gun">
                <ul id="list_gun1">
                    <?php $id=rand(1,32000);?>
                    <marquee onMouseOver=this.stop() onMouseOut=this.start() scrollamount=4 scrolldelay=1 direction=up width=250 height=300>
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"get\" data=\"op=get&tag_md5=af86e2ebe08e0658a78d761d49d06d02&sql=select+%2A+from+tt_news+where+id%3E%24id&num=100\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}pc_base::load_sys_class("get_model", "model", 0);$get_db = new get_model();$r = $get_db->sql_query("select * from tt_news where id>$id LIMIT 100");while(($s = $get_db->fetch_next()) != false) {$a[] = $s;}$data = $a;unset($a);?>
                        <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                        <li><a target="_blank" href="<?php echo $r['url'];?>" title= "<?php echo $r['title'];?>"><?php echo str_cut($r[title],50,'..');?></a></li>
                        <?php $n++;}unset($n); ?>
                        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                    </marquee>    
                </ul>  
                <ul id="list_gun2"></ul>  
            </div>
        </div>

        <div class="manhua">
            <div class="manhua_tit"><h3>漫画热门榜</h3></div>
            <div class="manhua_txt">
                <!-- 
                  <ul class="tang-title" id="tab_e">
                  
                    <li onmouseover="secBoard('tab_e','infolist_e',1,'tang-title-item tang-title-item-selected','tang-title-item first');"  class="tang-title-item tang-title-item-selected">
                                                    <a hidefocus="true" href="javascript:;">全部</a>
                                                    <div class="jiantou"></div>
                                            </li>
                    
                                            <li onmouseover="secBoard('tab_e','infolist_e',2,'tang-title-item tang-title-item-selected','tang-title-item first');" class="tang-title-item first">
                                                    <a hidefocus="true" href="javascript:;">今日</a>
                                                    <div class="jiantou"></div>
                                            </li>
                                            <li onmouseover="secBoard('tab_e','infolist_e',3,'tang-title-item tang-title-item-selected','tang-title-item first');" class="tang-title-item first">
                                                    <a hidefocus="true" href="javascript:;">本周</a>
                                                    <div class="jiantou">
                                            </div>
                                            </li>
                                            <li onmouseover="secBoard('tab_e','infolist_e',4,'tang-title-item tang-title-item-selected','tang-title-item first');" class="tang-title-item first">
                                                    <a hidefocus="true" href="javascript:;">本月</a>
                                                    <div class="jiantou"></div>
                                            </li>	
                                            
                                    </ul>-->	
                <div class="tang-body-item tang-body-item-selected">

                    <ol id="infolist_e_1" class="top-list-all" style="display:block">

                        <?php $i=1;?>
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=7bf8142bc0d92e12034bc39745c7d2c4&action=hits1&catid=13&num=10&where=%24where&order=views+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'hits1')) {$data = $content_tag->hits1(array('catid'=>'13','where'=>$where,'order'=>'views DESC','limit'=>'10',));}?>
                        <?php $n=1;if(is_array($manhua)) foreach($manhua AS $r) { ?>
                        <?php if($i==1) { ?>
                        <li class="poster">
                            <dl>
                                <dt>
                                <a target="_blank" href="/manhua<?php echo $r['id'];?>">
                                    <img width='116' height='65' src="<?php echo $r['thumb'];?>">
                                    <span class="poster-no"></span>
                                </a>
                                </dt>
                                <dd class="poster-title">
                                    <a title="<?php echo $r['title'];?>" target="_blank" href="/manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],25,"");?></a>
                                </dd>
                                <dd class="poster-brief"><?php echo str_cut(strip_tags($r[description]),50,"...");?></dd>
                                <dd class="poster-info" style="float: right;padding-right: 10px;line-height: 3px;"><?php echo $r['views'];?>次</dd>
                            </dl>
                        </li>
                        <?php } else { ?>
                        <li class="list  list-2">
                            <a title="<?php echo $r['title'];?>" target="_blank" href="/manhua<?php echo $r['id'];?>">
                                <span class="list-no topthree"><?php echo $i;?></span>
                                <span class="list-title"><?php echo str_cut($r[title],40,"");?></span>
                                <span class="list-info"><?php echo $r['views'];?>次</span>
                            </a>
                        </li>
                        <?php } ?>
                        <?php $i++?>
                        <?php $n++;}unset($n); ?> 
                    </ol>

                    <ol id="infolist_e_2" class="top-list-all" style="display:none">
                        <?php $i=1;?>
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=7bf8142bc0d92e12034bc39745c7d2c4&action=hits1&catid=13&num=10&where=%24where&order=views+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'hits1')) {$data = $content_tag->hits1(array('catid'=>'13','where'=>$where,'order'=>'views DESC','limit'=>'10',));}?>
                        <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                        <?php if($i ==1) { ?>
                        <li class="poster">
                            <dl>
                                <dt>
                                <a target="_blank" href="/manhua<?php echo $r['id'];?>">
                                    <img width='116' height='65' src="<?php echo $r['thumb'];?>">
                                    <span class="poster-no"></span>
                                </a>
                                </dt>
                                <dd class="poster-title">
                                    <a title="<?php echo $r['title'];?>" target="_blank" href="/manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],25,"");?></a>
                                </dd>
                                <dd class="poster-brief"><?php echo str_cut(strip_tags($r[description]),50,"...");?></dd>
                                <dd class="poster-info" style="float: right;padding-right: 10px;line-height: 3px;"><?php echo $r['views'];?>次</dd>
                            </dl>
                        </li>
                        <?php } else { ?>
                        <li class="list  list-2">
                            <a title="<?php echo $r['title'];?>" target="_blank" href="/manhua<?php echo $r['id'];?>">
                                <span class="list-no topthree"><?php echo $i;?></span>
                                <span class="list-title"><?php echo str_cut($r[title],40,"");?></span>
                                <span class="list-info"><?php echo $r['views'];?>次</span>
                            </a>
                        </li>
                        <?php } ?>
                        <?php $i++?>
                        <?php $n++;}unset($n); ?>
                        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                    </ol>


                    <ol id="infolist_e_3" class="top-list-all" style="display:none">
                        <?php $i=1;?>
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=7bf8142bc0d92e12034bc39745c7d2c4&action=hits1&catid=13&num=10&where=%24where&order=views+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'hits1')) {$data = $content_tag->hits1(array('catid'=>'13','where'=>$where,'order'=>'views DESC','limit'=>'10',));}?>
                        <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                        <?php if($i ==1) { ?>
                        <li class="poster">
                            <dl>
                                <dt>
                                <a target="_blank" href="/manhua<?php echo $r['id'];?>">
                                    <img width='116' height='65' src="<?php echo $r['thumb'];?>">
                                    <span class="poster-no"></span>
                                </a>
                                </dt>
                                <dd class="poster-title">
                                    <a title="<?php echo $r['title'];?>" target="_blank" href="/manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],25,"");?></a>
                                </dd>
                                <dd class="poster-brief"><?php echo str_cut(strip_tags($r[description]),50,"...");?></dd>
                                <dd class="poster-info" style="float: right;padding-right: 10px;line-height: 3px;"><?php echo $r['views'];?>次</dd>
                            </dl>
                        </li>
                        <?php } else { ?>
                        <li class="list  list-2">
                            <a title="<?php echo $r['title'];?>" target="_blank" href="/manhua<?php echo $r['id'];?>">
                                <span class="list-no topthree"><?php echo $i;?></span>
                                <span class="list-title"><?php echo str_cut($r[title],40,"");?></span>
                                <span class="list-info"><?php echo $r['views'];?>次</span>
                            </a>
                        </li>
                        <?php } ?>
                        <?php $i++?>
                        <?php $n++;}unset($n); ?>
                        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                    </ol>


                    <ol id="infolist_e_4" class="top-list-all" style="display:none">
                        <?php $i=1;?>
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=7bf8142bc0d92e12034bc39745c7d2c4&action=hits1&catid=13&num=10&where=%24where&order=views+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'hits1')) {$data = $content_tag->hits1(array('catid'=>'13','where'=>$where,'order'=>'views DESC','limit'=>'10',));}?>
                        <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                        <?php if($i ==1) { ?>
                        <li class="poster">
                            <dl>
                                <dt>
                                <a target="_blank" href="/manhua<?php echo $r['id'];?>">
                                    <img width='116' height='65' src="<?php echo $r['thumb'];?>">
                                    <span class="poster-no"></span>
                                </a>
                                </dt>
                                <dd class="poster-title">
                                    <a title="<?php echo $r['title'];?>" target="_blank" href="/manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],25,"");?></a>
                                </dd>
                                <dd class="poster-brief"><?php echo str_cut(strip_tags($r[description]),50,"...");?></dd>
                                <dd class="poster-info" style="float: right;padding-right: 10px;line-height: 3px;"><?php echo $r['views'];?>次</dd>
                            </dl>
                        </li>
                        <?php } else { ?>
                        <li class="list  list-2">
                            <a title="<?php echo $r['title'];?>" target="_blank" href="/manhua<?php echo $r['id'];?>">
                                <span class="list-no topthree"><?php echo $i;?></span>
                                <span class="list-title"><?php echo str_cut($r[title],40,"");?></span>
                                <span class="list-info"><?php echo $r['views'];?>次</span>
                            </a>
                        </li>
                        <?php } ?>
                        <?php $i++?>
                        <?php $n++;}unset($n); ?>
                        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                    </ol>

                </div>
            </div>
        </div>				
        <div class="manhua" style="margin-top:10px;">
            <div class="manhua_tit"><h3>动漫热门榜</h3></div>
            <div class="manhua_txt">
                <!-- 
                  <ul class="tang-title" id="tab_f">
                  
                    <li onmouseover="secBoard('tab_f','infolist_f',1,'tang-title-item tang-title-item-selected','tang-title-item first');"  class="tang-title-item tang-title-item-selected">
                                                    <a hidefocus="true" href="javascript:;">全部</a>
                                                    <div class="jiantou"></div>
                                            </li>
                                    
                                            <li onmouseover="secBoard('tab_f','infolist_f',2,'tang-title-item tang-title-item-selected','tang-title-item first');" class="tang-title-item first">
                                                    <a hidefocus="true" href="javascript:;">今日</a>
                                                    <div class="jiantou"></div>
                                            </li>
                                            <li onmouseover="secBoard('tab_f','infolist_f',3,'tang-title-item tang-title-item-selected','tang-title-item first');" class="tang-title-item first">
                                                    <a hidefocus="true" href="javascript:;">本周</a>
                                                    <div class="jiantou">
                                            </div>
                                            </li>
                                            <li onmouseover="secBoard('tab_f','infolist_f',4,'tang-title-item tang-title-item-selected','tang-title-item first');" class="tang-title-item first">
                                                    <a hidefocus="true" href="javascript:;">本月</a>
                                                    <div class="jiantou"></div>
                                            </li>	
                -->				
                </ul>
                <div class="tang-body-item tang-body-item-selected">

                    <ol id="infolist_f_1" class="top-list-all" style="display:block">

                        <!--  
                               <?php $i=1;?>
                               <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=a4a1b7913677f9f91fac7e70684369fd&action=hits1&catid=32&num=10&order=views+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'hits1')) {$data = $content_tag->hits1(array('catid'=>'32','order'=>'views DESC','limit'=>'10',));}?>  -->
                        <?php $n=1;if(is_array($dongman)) foreach($dongman AS $r) { ?>
                        <?php if($i ==1) { ?>
                        <li class="poster">
                            <dl>
                                <dt>
                                <a target="_blank" href="/dongman<?php echo $r['id'];?>">
                                    <img width='116' height='65' src="<?php echo $r['thumb'];?>">
                                    <span class="poster-no"></span>
                                </a>
                                </dt>
                                <dd class="poster-title">
                                    <a title="<?php echo $r['title'];?>" target="_blank" href="/dongman<?php echo $r['id'];?>"><?php echo str_cut($r[title],25,"");?></a>
                                </dd>
                                <dd class="poster-brief"><?php echo str_cut(strip_tags($r[description]),50,"...");?></dd>
                                <dd class="poster-info" style="float: right;padding-right: 10px;line-height: 3px;"><?php echo $r['views'];?>次</dd>
                            </dl>
                        </li>
                        <?php } else { ?>
                        <li class="list  list-2">
                            <a title="<?php echo $r['title'];?>" target="_blank" href="/dongman<?php echo $r['id'];?>">
                                <span class="list-no topthree"><?php echo $i;?></span>
                                <span class="list-title"><?php echo str_cut($r[title],40,"");?></span>
                                <span class="list-info"><?php echo $r['views'];?>次</span>
                            </a>
                        </li>
                        <?php } ?>
                        <?php $i++?>
                        <?php $n++;}unset($n); ?>
                    </ol>

                    <!-- 
                            <ol id="infolist_f_2" class="top-list-all" style="display:none">
                            <?php $i=1;?>
                            <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=a4a1b7913677f9f91fac7e70684369fd&action=hits1&catid=32&num=10&order=views+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'hits1')) {$data = $content_tag->hits1(array('catid'=>'32','order'=>'views DESC','limit'=>'10',));}?>
                            <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                                    <?php if($i ==1) { ?>
                                            <li class="poster">
                                            <dl>
                                                    <dt>
                                                            <a target="_blank" href="/dongman<?php echo $r['id'];?>">
                                                                    <img width='116' height='65' src="<?php echo $r['thumb'];?>">
                                                                    <span class="poster-no"></span>
                                                            </a>
                                                    </dt>
                                                    <dd class="poster-title">
                                                            <a title="<?php echo $r['title'];?>" target="_blank" href="/dongman<?php echo $r['id'];?>"><?php echo str_cut($r[title],25,"");?></a>
                                                    </dd>
                                                    <dd class="poster-brief"><?php echo str_cut(strip_tags($r[description]),50,"...");?></dd>
                                                    <dd class="poster-info" style="float: right;padding-right: 10px;line-height: 3px;"><?php echo $r['views'];?>次</dd>
                                            </dl>
                                    </li>
                                    <?php } else { ?>
                                            <li class="list  list-2">
                                            <a title="<?php echo $r['title'];?>" target="_blank" href="/dongman<?php echo $r['id'];?>">
                                                    <span class="list-no topthree"><?php echo $i;?></span>
                                                    <span class="list-title"><?php echo str_cut($r[title],40,"");?></span>
                                                    <span class="list-info"><?php echo $r['views'];?>次</span>
                                            </a>
                                    </li>
                                    <?php } ?>
                                    <?php $i++?>
                            <?php $n++;}unset($n); ?>
                            <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                            </ol>
                            
                            
                            <ol id="infolist_f_3" class="top-list-all" style="display:none">
                            <?php $i=1;?>
                            <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=a4a1b7913677f9f91fac7e70684369fd&action=hits1&catid=32&num=10&order=views+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'hits1')) {$data = $content_tag->hits1(array('catid'=>'32','order'=>'views DESC','limit'=>'10',));}?>
                            <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                                    <?php if($i ==1) { ?>
                                            <li class="poster">
                                            <dl>
                                                    <dt>
                                                            <a target="_blank" href="/dongman<?php echo $r['id'];?>">
                                                                    <img width='116' height='65' src="<?php echo $r['thumb'];?>">
                                                                    <span class="poster-no"></span>
                                                            </a>
                                                    </dt>
                                                    <dd class="poster-title">
                                                            <a title="<?php echo $r['title'];?>" target="_blank" href="/dongman<?php echo $r['id'];?>"><?php echo str_cut($r[title],25,"");?></a>
                                                    </dd>
                                                    <dd class="poster-brief"><?php echo str_cut(strip_tags($r[description]),50,"...");?></dd>
                                                    <dd class="poster-info" style="float: right;padding-right: 10px;line-height: 3px;"><?php echo $r['views'];?>次</dd>
                                            </dl>
                                    </li>
                                    <?php } else { ?>
                                            <li class="list  list-2">
                                            <a title="<?php echo $r['title'];?>" target="_blank" href="/dongman<?php echo $r['id'];?>">
                                                    <span class="list-no topthree"><?php echo $i;?></span>
                                                    <span class="list-title"><?php echo str_cut($r[title],40,"");?></span>
                                                    <span class="list-info"><?php echo $r['views'];?>次</span>
                                            </a>
                                    </li>
                                    <?php } ?>
                                    <?php $i++?>
                            <?php $n++;}unset($n); ?>
                            <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                            </ol>
                            
            
                            <ol id="infolist_f_4" class="top-list-all" style="display:none">
                            <?php $i=1;?>
                            <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=a4a1b7913677f9f91fac7e70684369fd&action=hits1&catid=32&num=10&order=views+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'hits1')) {$data = $content_tag->hits1(array('catid'=>'32','order'=>'views DESC','limit'=>'10',));}?>
                            <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                                    <?php if($i ==1) { ?>
                                            <li class="poster">
                                            <dl>
                                                    <dt>
                                                            <a target="_blank" href="/dongman<?php echo $r['id'];?>">
                                                                    <img width='116' height='65' src="<?php echo $r['thumb'];?>">
                                                                    <span class="poster-no"></span>
                                                            </a>
                                                    </dt>
                                                    <dd class="poster-title">
                                                            <a title="<?php echo $r['title'];?>" target="_blank" href="/dongman<?php echo $r['id'];?>"><?php echo str_cut($r[title],25,"");?></a>
                                                    </dd>
                                                    <dd class="poster-brief"><?php echo str_cut(strip_tags($r[description]),50,"...");?></dd>
                                                    <dd class="poster-info" style="float: right;padding-right: 10px;line-height: 3px;"><?php echo $r['views'];?>次</dd>
                                            </dl>
                                    </li>
                                    <?php } else { ?>
                                            <li class="list  list-2">
                                            <a title="<?php echo $r['title'];?>" target="_blank" href="/dongman<?php echo $r['id'];?>">
                                                    <span class="list-no topthree"><?php echo $i;?></span>
                                                    <span class="list-title"><?php echo str_cut($r[title],40,"");?></span>
                                                    <span class="list-info"><?php echo $r['views'];?>次</span>
                                            </a>
                                    </li>
                                    <?php } ?>
                                    <?php $i++?>
                            <?php $n++;}unset($n); ?>
                            <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                            </ol>
                    -->

                </div>
            </div>
        </div>


        <div class="clear1"></div>
        <div class="manhua" style="margin-top:10px;">
            <div class="manhua_tit"><h3>最新专题</h3></div>
            <div class="manhua_txt">
                <div class="txt_pic">
                    <a href="#"><img src="/statics/images/img/logo.gif"></a>
                    <a href="#"><img src="/statics/images/img/logo.gif"></a>
                    <a href="#"><img src="/statics/images/img/logo.gif"></a>
                </div>
            </div>
        </div>

        <div class="jiebao">
            <div class="jiebao_tit">  
                <span><a href="/jiebao">更多&gt;&gt;</a></span>
                <ul id="tab_d">                
                    <li class="on" onmouseover="secBoard('tab_d','infolist_d',1,'on','');"><strong>漫画捷报</strong></li>                
                    <li class="" onmouseover="secBoard('tab_d','infolist_d',2,'on','');"><strong>漫画资讯</strong></li>      
                </ul>
            </div>
            <div class="jiebao_txt">
                <ul id="infolist_d_1" style="display:block">
                    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=d086123a8416c046afb597566df9c9b7&action=lists&catid=55&order=updatetime+DESC&num=10\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('catid'=>'55','order'=>'updatetime DESC','limit'=>'10',));}?>
                    <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                    <li><a target="_blank" href="<?php echo $r['url'];?>" title= "<?php echo $r['title'];?>"><?php echo str_cut($r[title],50,'..');?></a></li>
                    <?php $n++;}unset($n); ?>
                    <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                </ul>
                <ul id="infolist_d_2" style="display:none">
                    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=3c2ae2f6c472e17e40a950cdb01f9916&action=lists&catid=19&order=updatetime+DESC&num=10\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('catid'=>'19','order'=>'updatetime DESC','limit'=>'10',));}?>
                    <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                    <li><a target="_blank" href="<?php echo $r['url'];?>" title= "<?php echo $r['title'];?>"><?php echo str_cut($r[title],50,'..');?></a></li>
                    <?php $n++;}unset($n); ?>
                    <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                </ul>
            </div>
        </div>

        <div class="manhua" style="margin-top:10px;">
            <a href="http://www.manhuacheng.com/download/" target="_blank"><img src="/statics/images/img/manhua_down.jpg"></a>
        </div>

    </div><div class="clear1"></div>
    <!-- <div class="comics_img">
            <a href="/dongman3550" title="越狱兔" target="_blank">
                    <img src="/uploadfile/20150620/s20150620011243518.jpg">
            </a>
            <a href="/dongman2925" title="阿U" target="_blank">
                    <img width="115" height="130" src="/uploadfile/cover/20130522/201305221838576.jpg">
            </a>
            <a href="/dongman3550" title="越狱兔" target="_blank">
                    <img src="/uploadfile/20150620/s20150620011243518.jpg">
            </a>
            <a href="/dongman2925" title="阿U" target="_blank">
                    <img width="115" height="130" src="/uploadfile/cover/20130522/201305221838576.jpg">
            </a>
            <a href="/dongman3550" title="越狱兔" target="_blank">
                    <img src="/uploadfile/20150620/s20150620011243518.jpg">
            </a>
            <a href="/dongman2925" title="阿U" target="_blank">
                    <img width="115" height="130" src="/uploadfile/cover/20130522/201305221838576.jpg">
            </a>
            <a href="/dongman3550" title="越狱兔" target="_blank">
                    <img src="/uploadfile/20150620/s20150620011243518.jpg">
            </a>
    </div> -->

    <script type="text/javascript" src="/statics/js/jquery.1.4.2-min.js"></script>
    <script type="text/javascript" src="/statics/js/marquee.js"></script>
    <div id="marquee1" class="marqueeleft">
        <div style="width:8000px;">
            <ul id="marquee1_1">
                <?php $id=rand(661,3600);?>
                <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"get\" data=\"op=get&tag_md5=c3f4689080ef8606372fb65b8b91e481&sql=select+%2A+from+tt_dongman+where+id%3E%24id&num=30\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}pc_base::load_sys_class("get_model", "model", 0);$get_db = new get_model();$r = $get_db->sql_query("select * from tt_dongman where id>$id LIMIT 30");while(($s = $get_db->fetch_next()) != false) {$a[] = $s;}$data = $a;unset($a);?>
                <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                <li>
                    <a class="pic" href="/dongman<?php echo $r['id'];?>"><img width="138" height="172" src="<?php echo $r['thumb'];?>"></a>
                </li>
                <?php $n++;}unset($n); ?>   
                <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
            </ul>
            <ul id="marquee1_2"></ul>
        </div>
    </div><!--marqueeleft end-->
    <script type="text/javascript">marqueeStart(1, "left");</script>



</div><div class="clear1"></div>

<div class="tjmh">
    <div class="tjmh_tit">
        <i class="icon"></i>
        <h3>推荐漫画</h3>
        <ul id="tab_h">
            <?php $i=1;?>
            <?php $n=1; if(is_array($m)) foreach($m AS $n => $l) { ?>
            <?php if(!in_array($n,dexplode(NOT_ALLOW))) { ?>
            <li <?php if($i == 1) { ?>class="hover"<?php } ?> onmouseover="secBoard('tab_h','infolist_h',<?php echo $i;?>,'hover','');" ><?php echo $l;?></li>
            <?php $i++;?>
            <?php } ?>
            <?php $n++;}unset($n); ?>
        </ul>
    </div>
    <?php $k=1;?>
    <?php $n=1; if(is_array($m)) foreach($m AS $n => $l) { ?>
    <div class="tjmh_txt" style="<?php if($k != 1) { ?>display:none<?php } ?>" id="infolist_h_<?php echo $k;?>">
        <ol class="top-list">
            <?php $type_sql = "status = 99 AND type_id = $n";?>		
            <?php $j = 1;?>
            <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=df141dec86847e6a706ad528115e07e5&action=table_list&table=Cartoon&where=%24type_sql&order=views+DESC&num=50\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','where'=>$type_sql,'order'=>'views DESC','limit'=>'50',));}?>
            <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
            <li>
                <span><?php echo $j;?></span>
                <a target="_blank" title="<?php echo $r['title'];?>" href="manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],30,'');?></a>
            </li>
            <?php if($j%10 == 0) { ?></ol><ol class="top-list"><?php } ?>
            <?php $j++;?>
            <?php $n++;}unset($n); ?>
            <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
        </ol>
    </div>
    <?php $k++;?>
    <?php $n++;}unset($n); ?>
</div>

</div>
<div class="lianjie">
    <div class="lianjie_tit">
        <span><a href="/link">更多&gt;&gt;</a></span>
        <ul id="tab_g">                
            <li class="on" onmouseover="secBoard('tab_g','infolist_g',1,'on','');"><strong>友情链接</strong></li>                
            <li class="" onmouseover="secBoard('tab_g','infolist_g',2,'on','');"><strong>网址导航</strong></li>      
            <li class="" onmouseover="secBoard('tab_g','infolist_g',3,'on','');"><strong>合作伙伴</strong></li>      
        </ul>
    </div>
    <div class="lianjie_txt" id="infolist_g_1">
        <?php $n=1;if(is_array($link)) foreach($link AS $v) { ?>
        <a href="<?php echo $v['url'];?>" title="<?php echo $v['name'];?>"  target="_blank"><?php echo $v['name'];?> </a> 
        <?php $n++;}unset($n); ?>
    </div>
    <div class="lianjie_txt" id="infolist_g_2" style="display:none">
        <?php $n=1;if(is_array($daohang)) foreach($daohang AS $v) { ?>
        <a href="<?php echo $v['url'];?>" title="<?php echo $v['name'];?>"  target="_blank"><?php echo $v['name'];?> </a> 
        <?php $n++;}unset($n); ?>
    </div>
    <div class="lianjie_txt" id="infolist_g_3" style="display:none">
        <?php $n=1;if(is_array($hezuo)) foreach($hezuo AS $v) { ?>
        <a href="<?php echo $v['url'];?>" title="<?php echo $v['name'];?>"  target="_blank"><?php echo $v['name'];?> </a> 
        <?php $n++;}unset($n); ?>
    </div>
</div>
</div>
<div class="clear1"></div>
<?php include template("content","footer"); ?>
</body>
</html>
