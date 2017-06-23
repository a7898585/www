<?php defined('IN_OLCMS') or exit('No permission resources.'); ?>        <div class="middle_div1_ri">
            <div class="tu"><?php include template("content","250ad"); ?></div>
            <div class="xh_man">
                <div class="rs_dm_tit">
                    <h3>最新更新漫画</h3>
                </div>
                <div class="xh_man_txt">
                    <ul>
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=28da3a074f92e58b40482f99cf42ed5a&action=table_list&table=Cartoon&where=%24where&order=updatetime+DESC&num=4&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('table'=>'Cartoon','where'=>$where,'order'=>'updatetime DESC',)).'28da3a074f92e58b40482f99cf42ed5a');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'table_list')) {$data = $content_tag->table_list(array('table'=>'Cartoon','where'=>$where,'order'=>'updatetime DESC','limit'=>'4',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
                          <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                          <li>
                            <a href="/manhua<?php echo $r['id'];?>" title="<?php echo $r['title'];?>" target="_blank"><img width='80' height='115' src="<?php echo $r['thumb'];?>"></a>
                            <a href="/manhua<?php echo $r['id'];?>" title="<?php echo $r['title'];?>" target="_blank"><?php echo str_cut($r[title],15,'');?></a>
                          </li>
                          <?php $n++;}unset($n); ?>
                        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                    </ul>
                </div>
            </div>
            <script type="text/javascript">
    /*漫画城列表页漫画捷报250*250 创建于 2015-06-01*/
    var cpro_id = "u2131314";
</script>
<script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>
            <div class="jiebao">
                <div class="rs_dm_tit">
                    <h3>漫画捷报<span style="float: right;font-size: 12px;font-weight: normal;padding-right: 5px;"><a href="/jiebao">更多&gt;&gt;</a></span></h3>
                </div>
                    <div class="jiebao_txt">
                        <ul>
                                <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=cfeae36811db1b76663fd4d008aef735&action=lists&catid=55&order=updatetime+DESC&num=10&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('catid'=>'55','order'=>'updatetime DESC',)).'cfeae36811db1b76663fd4d008aef735');if(!$data = tpl_cache($tag_cache_name,3600)){$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('catid'=>'55','order'=>'updatetime DESC','limit'=>'10',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data','','template',3600);}}?>
                                <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                                    <li><a target="_blank" href="<?php echo $r['url'];?>" title= "<?php echo $r['title'];?>"><?php echo str_cut($r[title],45,'..');?></a></li>
                                <?php $n++;}unset($n); ?>
                            <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                        </ul>
                    </div>
                </div>
                
             <a href="http://www.yunzhuan.com" target="_blank" title="前往云赚公益平台" rel="nofollow"><img src="/statics/images/yz2.png" alt="云赚平台"></img></a>
            
        </div>
