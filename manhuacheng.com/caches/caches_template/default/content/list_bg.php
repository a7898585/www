<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php include template("content","header"); ?>
<?php $footer_txt = "漫画城在线漫画频道为您提供最新、最热门的在线漫画阅读，有热门连载漫画、完结漫画、少年热血、少女爱情等包括火影忍者漫画、海贼王漫画、死神漫画等非常好看的漫画。";?>
<style type="text/css">
    body,ul,li { padding:0; margin:0}
    ul,li { list-style:none}
    .a-scroll { position:relative; margin:20px auto 0px auto; width:998px;}
    .a-scroll .prev,.a-scroll .next { position:absolute; display:block; width:50px; height:130px; background-color:#666666;top:0; color:#FFF; text-align:center; }
    .a-scroll .prev { left:0;cursor:pointer; background:url("<?php echo IMG_PATH;?>img/fx.png") no-repeat;margin-top: 60px;margin-left: 20px;}
    .a-scroll .next { right:0;cursor:pointer; background:url("<?php echo IMG_PATH;?>img/fx1.png") no-repeat;margin-top: 60px;margin-right: -5px;}
    .a-list { position:relative; width:880px; height:150px; margin-left:65px; overflow:hidden}
    .a-list ul { width:9999px; position:absolute;}
    .a-list ul a { width:100px; height:120px; color:#000000; text-decoration:none; }
    .a-list li { float:left; display:inline; width:100px; margin-right:10px; height:150px; text-align:center; }
    .a-list ul a img { width:98px; height:118px; padding:2px; }
    .a-list ul a span { font-size:12px; color:#666666; display:inline-block; margin:5px 0px 0px 0px; }
    .a-list ul a:hover span { text-decoration:underline; color:#ff9900; }
    .a-list ul a:hover img { border:1px solid #999999; padding:1px; }
</style>

<?php include template("content","mh_nav"); ?>

<?php $where = "status = '99' AND type_id NOT IN(".dimplode(dexplode(NOT_ALLOW)).")";?>		
<!--main-->
<div class="zjgx">
    <div class="zjgx_tit">
        <i class="icon"></i>
        <h3>最近更新</h3>
    </div>
    <div class="zjgx_txt">
        <div class="tp_n">
            <div class="a-scroll">
                <span class="prev">&nbsp;</span>
                <span class="next">&nbsp;</span>
                <div class="a-list">
                    <ul>
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=776d7c5165735e03fcbe35e22af68500&action=table_list&table=Cartoon&where=%24where&order=inputtime+DESC&num=15&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'Cartoon','where'=>$where,'order'=>'inputtime DESC',)).'776d7c5165735e03fcbe35e22af68500');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','where'=>$where,'order'=>'inputtime DESC','limit'=>'15',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
                        <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                        <li><a href="/manhua<?php echo $r['id'];?>" title="<?php echo $r['title'];?>" target="_blank"><img src="<?php echo $r['thumb'];?>"><span><?php echo str_cut($r[title],22,'');?></span></a></li>
                        <?php $n++;}unset($n); ?>
                        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="xian"></div>
        <div class="clear1"></div>

        <div class="sj">
            <span>最新上架</span>
            <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=8b2ce34bf11e747b34042a285e709bcc&action=table_list&table=Cartoon&where=%24where&order=inputtime+DESC&num=10&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'Cartoon','where'=>$where,'order'=>'inputtime DESC',)).'8b2ce34bf11e747b34042a285e709bcc');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','where'=>$where,'order'=>'inputtime DESC','limit'=>'10',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
            <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
            <a href="/manhua<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],16,'');?></a>
            <?php $n++;}unset($n); ?>
            <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
        </div>
        <div class="sj">
            <span>完结漫画</span>
            <?php $where_wanjie = $where." AND state = 1";?>		
            <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=de757688fe9f4114b79210828f50ee5f&action=table_list&table=Cartoon&where=%24where_wanjie&order=updatetime+DESC&num=10&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'Cartoon','where'=>$where_wanjie,'order'=>'updatetime DESC',)).'de757688fe9f4114b79210828f50ee5f');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','where'=>$where_wanjie,'order'=>'updatetime DESC','limit'=>'10',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
            <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
            <a href="/manhua<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],15,'');?></a>
            <?php $n++;}unset($n); ?>
            <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>

            <a href="/wanjie" target="_blank" style="color:#0000FF;">更多</a>
        </div>
        <div class="sj" style="border-bottom:none;">
            <span>连载漫画</span>
            <?php $where_lianzai = $where." AND state = 0";?>		
            <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=c01abb9f978fc2122a3603ca795507d8&action=table_list&table=Cartoon&where=%24where_lianzai&order=updatetime+DESC&num=10&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'Cartoon','where'=>$where_lianzai,'order'=>'updatetime DESC',)).'c01abb9f978fc2122a3603ca795507d8');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','where'=>$where_lianzai,'order'=>'updatetime DESC','limit'=>'10',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
            <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
            <a href="/manhua<?php echo $r['id'];?>" title = '<?php echo $r['title'];?>' target="_blank"><?php echo str_cut($r[title],15,'');?></a>
            <?php $n++;}unset($n); ?>
            <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>

            <a href="/lianzai" target="_blank" style="color:#0000FF;">更多</a>
        </div>
    </div>
</div>
<div class="middle_div1">
    <div class="middle_div1_le" style="border:none;">
        <div class="today">
            <div class="today_tit">
                <span>
                    <?php $n=1; if(is_array($m)) foreach($m AS $n => $l) { ?>
                    <?php if($n<6) { ?>
                    <a href="/type<?php echo $n;?>/" target="_blank"><?php echo $l;?></a>&nbsp;|
                    <?php } ?>
                    <?php $n++;}unset($n); ?>
                    <a href="#" style="padding-left:15px; color:#0000FF;">更多&gt;&gt;</a></span>
                <ul id="tab_a">                
                    <li class="on" onmouseover="secBoard('tab_a','infolist_a',1,'on','');"><strong>今日漫画</strong></li>                
                    <li onmouseover="secBoard('tab_a','infolist_a',2,'on','');"><strong>最新章节</strong></li>  
                </ul>
            </div>
            <div class="today_txt">
                <ul id="infolist_a_1" style="display:block">
                    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=52af0d1aace473746c79b627101e2230&action=position&posid=14&order=updatetime+DESC&sort=desc&num=10\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'position')) {$data = $content_tag->position(array('posid'=>'14','order'=>'updatetime DESC','sort'=>'desc','limit'=>'10',));}?>
                    <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                    <li>
                        <a target="_blank" title="{<?php echo $r['title'];?>" href="/manhua<?php echo $r['id'];?>"><img  width='115' height='130' src="<?php echo $r['thumb'];?>"></a>
                        <a target="_blank"  title="<?php echo $r['title'];?>" href="/manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],25,'');?></a>
                    </li>
                    <?php $n++;}unset($n); ?>
                    <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                </ul>
                <ul id="infolist_a_2" style="display:none">
                    <?php $sql = "select cd.web_url,cd.id,c.title as c_title,c.thumb,cd.title from tt_Cartoon as c left join (select * from tt_CartoonDetail where manhuaid != 0 order by id desc limit 500) as cd  ON cd.manhuaid=c.id where cd.catid = '14' AND c.status = '99' AND c.type_id NOT IN(".dimplode(dexplode(NOT_ALLOW)).") GROUP BY c.id order by cd.id desc";?>
                    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"get\" data=\"op=get&tag_md5=8ccf0ba4ab159401e2a4a1ab6bb4439c&sql=%24sql&num=10&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('sql'=>$sql,)).'8ccf0ba4ab159401e2a4a1ab6bb4439c');if(!$data = tpl_cache($tag_cache_name,3600)){pc_base::load_sys_class("get_model", "model", 0);$get_db = new get_model();$r = $get_db->sql_query("$sql LIMIT 10");while(($s = $get_db->fetch_next()) != false) {$a[] = $s;}$data = $a;unset($a);if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
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
        <!--按地区-->
        <div class="today1">
            <div class="today1_tit">
                <ul id="tab_b">   
                    <?php $region= getboxname('12','region');?> 
                    <?php $n=1; if(is_array($region)) foreach($region AS $n => $l) { ?>
                    <?php if($n==1) { ?>
                    <li class="on" onmouseover="secBoard('tab_b','regionlist_b',<?php echo $n;?>,'on','');"><strong><?php echo $l;?></strong></li>
                    <?php } else { ?>
                    <li onmouseover="secBoard('tab_b','regionlist_b',<?php echo $n;?>,'on','');"><strong><?php echo $l;?></strong></li>   
                    <?php } ?>
                    <?php $n++;}unset($n); ?>
                </ul>
            </div>
            <div class="today1_txt">
                <?php $n=1; if(is_array($region)) foreach($region AS $n => $l) { ?>
                <?php if($n==1) { ?>
                <?php $style = "display:block";?>
                <?php } else { ?>
                <?php $style = "display:none";?>
                <?php } ?>
                <ul id="regionlist_b_<?php echo $n;?>" class="today1_txt1" style="<?php echo $style;?>">
                    <?php $region_sql = $where." AND region = $n";?>		
                    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=f1d124e855e13151dc3f71f05a95deb9&action=table_list&table=Cartoon&where=%24region_sql&order=updatetime+DESC&num=10&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'Cartoon','where'=>$region_sql,'order'=>'updatetime DESC',)).'f1d124e855e13151dc3f71f05a95deb9');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','where'=>$region_sql,'order'=>'updatetime DESC','limit'=>'10',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
                    <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                    <li>
                        <a target="_blank" title="{<?php echo $r['title'];?>" href="/manhua<?php echo $r['id'];?>"><img  width='115' height='130' src="<?php echo $r['thumb'];?>"></a>
                        <a target="_blank"  title="<?php echo $r['title'];?>" href="/manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],25,'');?></a>
                    </li>
                    <?php $n++;}unset($n); ?>
                    <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                </ul>
                <?php $n++;}unset($n); ?>
            </div>
        </div>
        <!--按地区end-->
    </div>
    <div class="middle_div1_ri">
        <div class="eveday">
            <div class="rs_dm_tit">
                <h3>每日更新</h3>
            </div>
            <div class="eveday_txt">
                <!-- <div class="eveday_txt_l">
                <ul>
                        <li class="on" onmouseover="changeTab(7,0)"><strong>一</strong></li> 
                    <li class="" onmouseover="changeTab(7,1)"><strong>二</strong></li> 
                    <li class="" onmouseover="changeTab(7,2)"><strong>三</strong></li> 
                    <li class="" onmouseover="changeTab(7,3)"><strong>四</strong></li> 
                    <li class="" onmouseover="changeTab(7,4)"><strong>五</strong></li> 
                    <li class="" onmouseover="changeTab(7,5)"><strong>六</strong></li> 
                    <li class="" onmouseover="changeTab(7,6)"><strong>七</strong></li> 
                </ul>
            </div> -->
                <div class="eveday_txt_r">
                    <ul>
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=27a14c869a75e327d6bbe6bbeabf5513&action=table_list&table=Cartoon&fields=id%2Ctitle&where=%24where&order=inputtime+DESC&num=8&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'Cartoon','fields'=>'id,title','where'=>$where,'order'=>'inputtime DESC',)).'27a14c869a75e327d6bbe6bbeabf5513');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','fields'=>'id,title','where'=>$where,'order'=>'inputtime DESC','limit'=>'8',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
                        <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                        <li><a href="/manhua<?php echo $r['id'];?>/"><?php echo str_cut($r[title],50,'');?></a></li>
                        <?php $n++;}unset($n); ?>
                        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>                        
                    </ul>
                </div>
            </div>
        </div>
        <div class="jiebao">
            <div class="rs_dm_tit">
                <h3>漫画捷报<span style="float: right;font-size: 12px;font-weight: normal;padding-right: 5px;"><a href="/jiebao">更多&gt;&gt;</a></span></h3>
            </div>

            <div class="jiebao_txt">
                <ul>						
                    <?php $where_jb = "catid ='55' AND status = '99'";?>
                    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=4e1db628fb5463773ab04b2d7fea7d42&action=table_list&table=news&fields=id%2Ctitle%2Curl&where=%24where_jb&order=updatetime+DESC&num=8&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'news','fields'=>'id,title,url','where'=>$where_jb,'order'=>'updatetime DESC',)).'4e1db628fb5463773ab04b2d7fea7d42');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'news','fields'=>'id,title,url','where'=>$where_jb,'order'=>'updatetime DESC','limit'=>'8',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
                    <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                    <li><a target="_blank" href="<?php echo $r['url'];?>" title= "<?php echo $r['title'];?>"><?php echo str_cut($r[title],50,'...');?></a></li>
                    <?php $n++;}unset($n); ?>
                    <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                </ul>
            </div>
        </div>
        <div class="rs_dm" style="margin-top: 10px;">
            <div class="rs_dm_tit"><h3>漫画人气排行榜</h3></div>
            <div class="rs_dm_txt">
                <ul>
                    <?php $class= array(1=>'first',2=>'second',3=>'third')?>
                    <?php $i=1;?>
                    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=255a2b713418219209c0178e0e214df9&action=hits2&catid=13&num=10&where=%24where&order=views+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'hits2')) {$data = $content_tag->hits2(array('catid'=>'13','where'=>$where,'order'=>'views DESC','limit'=>'10',));}?>
                    <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                    <li><i class="<?php echo $class[$i];?>"><?php echo $i;?></i><a title = '<?php echo $r['title'];?>' href="/manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],25,'');?></a></li>
                    <?php $i++?>
                    <?php $n++;}unset($n); ?>
                    <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="clear1"></div>
<!--按分类-->
<div class="dongman1">
    <div class="dongman1_tit">
        <ul id="tab_c">
            <?php $i =1 ;?>
            <?php $class= array(1=>'on')?>
            <?php $n=1; if(is_array($m)) foreach($m AS $n => $l) { ?>
            <?php if(!in_array($n,dexplode(NOT_ALLOW))) { ?>
            <li onmouseover="secBoard('tab_c','typelist_c',<?php echo $n;?>,'on','');" class="<?php echo $class[$i];?>"><strong><?php echo $l;?></strong></li>	
            <?php } ?>
            <?php $i++;?>
            <?php $n++;}unset($n); ?>
            <?php unset($i);unset($class);?>
        </ul>
    </div>
    <div class="dongman1_txt">
        <?php $i =1 ;?>
        <?php $n=1; if(is_array($m)) foreach($m AS $n => $l) { ?>
        <?php if(!in_array($n,dexplode(NOT_ALLOW))) { ?>
        <ul id="typelist_c_<?php echo $n;?>" class="dongman1_txt1" style="<?php if($i!=1) { ?>display:none<?php } ?>">
            <?php $type_sql = $where." AND type_id = $n";?>		
            <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=199c87dcb301db10d5c628c1328bcc24&action=table_list&table=Cartoon&where=%24type_sql&order=updatetime+DESC&num=7&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'Cartoon','where'=>$type_sql,'order'=>'updatetime DESC',)).'199c87dcb301db10d5c628c1328bcc24');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','where'=>$type_sql,'order'=>'updatetime DESC','limit'=>'7',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
            <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
            <li>
                <a target="_blank" title="{<?php echo $r['title'];?>" href="/manhua<?php echo $r['id'];?>"><img  width='115' height='130' src="<?php echo $r['thumb'];?>"></a>
                <a target="_blank"  title="<?php echo $r['title'];?>" href="/manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],25,'');?></a>
            </li>
            <?php $n++;}unset($n); ?>
            <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
        </ul>
        <?php } ?>
        <?php $i++;?>
        <?php $n++;}unset($n); ?>
        <?php unset($i);?>
    </div>
</div>
<!--按分类end-->


<script type="text/javascript">
    function DY_scroll(wraper,prev,next,a,speed,or)
    { 
        var wraper = $(wraper);
        var prev = $(prev);
        var next = $(next);
        var a = $(a).find('ul');
        var w = a.find('li').outerWidth(true);
        var s = speed;
        next.click(function()
        {
            a.animate({'margin-left':-w},function()
            {
                a.find('li').eq(0).appendTo(a);
                a.css({'margin-left':0});
            });
        });
        prev.click(function()
        {
            a.find('li:last').prependTo(a);
            a.css({'margin-left':-w});
            a.animate({'margin-left':0});
        });
        if (or == true)
        {
            ad = setInterval(function() { next.click();},s*1000);
            wraper.hover(function(){clearInterval(ad);},function(){ad = setInterval(function() { next.click();},s*1000);});

        }
    }
    DY_scroll('.a-scroll','.prev','.next','.a-list',3,false);// true为自动播放，不加此参数或false就默认不自动
</script>
<div class="lianjie">
    <div class="lianjie_tit"><h3>友情链接</h3></div>
    <div class="lianjie_txt">
        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"link\" data=\"op=link&tag_md5=9447040ca748a846007f23ef6dcc81cb&action=lists&typeid=17&linktype=0&order=desc&num=50&return=dat\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$link_tag = pc_base::load_app_class("link_tag", "link");if (method_exists($link_tag, 'lists')) {$dat = $link_tag->lists(array('typeid'=>'17','linktype'=>'0','order'=>'desc','limit'=>'50',));}?>
        <?php $n=1; if(is_array($dat)) foreach($dat AS $key => $v) { ?>
        <a href="<?php echo $v['url'];?>" title="<?php echo $v['name'];?>"  target="_blank"><?php echo $v['name'];?> </a> 
        <?php if($v != end($dat)) { ?>&nbsp;|&nbsp;<?php } ?>
        <?php $n++;}unset($n); ?>
        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
    </div>
</div>
<?php include template("content","footer"); ?>