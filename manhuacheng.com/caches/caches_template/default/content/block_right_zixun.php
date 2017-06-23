<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><div class="middle_div1_ri">
    <div class="phb">
        <div class="rs_dm_tit">
            <h3>排行榜</h3>
        </div>
        <div class="phb_txt">
            <ul>
                <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=90ad96ba1cb896212f8693e4112d84a5&action=lists&catid=%24top_parentid&num=10&order=views+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('catid'=>$top_parentid,'order'=>'views DESC','limit'=>'10',));}?>
                <?php $i=1;?>
                <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                <?php if($i < 4) { ?>
                <li>
                    <i class="first2"><?php echo $i;?></i>
                    <a href="/zixun<?php echo $r['id'];?>/" title="<?php echo $r['title'];?>" target="_blank" class="phb_txtc"><?php echo str_cut($r['title'],40,'');?></a>
                    <span class="phb_txt_hs"><?php echo $r['views'];?></span>
                </li> 
                <?php } else { ?>
                <li>
                    <i class="first3"><?php echo $i;?></i>
                    <a href="/zixun<?php echo $r['id'];?>/" title="<?php echo $r['title'];?>" target="_blank" class="phb_txtc"><?php echo str_cut($r['title'],40,'');?></a>
                    <span class="phb_txt_hs"><?php echo $r['views'];?></span>
                </li> 
                <?php } ?>
                <?php if($i == 1) { ?>
                <li>
                    <div class="phb_img">
                        <a title="<?php echo $r['title'];?>" target="_blank" href="/zixun<?php echo $r['id'];?>/"><img src="<?php echo thumb($r['thumb'],110,80);?>" /></a>
                        <div class="phb_img_kj"><span>NO.<?php echo $i;?></span></div>
                    </div>								
                </li>
                <?php } ?>
                <?php $i++;?>
                <?php $n++;}unset($n); ?>
                <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
            </ul>                          
        </div>
    </div>

    <div style="margin-bottom:10px;"><?php echo GetAd(3);?></div>
    <!-- <div class="phb2">
            <div class="rs_dm_tit">
                    <h3>热门标签</h3>
            </div>
<div class="phb_txt2">
<a href="#" class="pt2c1">感动</a>
                <a href="#" class="pt2c2">雷人</a>  
                    <a href="#" class="pt2c1">感动</a>
                <a href="#" class="pt2c1">雷人</a> 
                    <a href="#" class="pt2c1">感动</a>
                <a href="#" class="pt2c2">雷人</a> 
                    <a href="#" class="pt2c1">感动</a>
                <a href="#" class="pt2c2">雷人</a>  
                    <a href="#" class="pt2c1">感动</a>
                <a href="#" class="pt2c1">雷人</a> 
                    <a href="#" class="pt2c1">感动</a>
                <a href="#" class="pt2c2">雷人</a> 
                    <a href="#" class="pt2c1">感动</a>
                <a href="#" class="pt2c2">雷人</a>  
                    <a href="#" class="pt2c1">感动</a>
                <a href="#" class="pt2c1">雷人</a> 
                    <a href="#" class="pt2c1">感动</a>
                <a href="#" class="pt2c2">雷人</a> 
                    <a href="#" class="pt2c1">感动</a>
                <a href="#" class="pt2c2">雷人</a>  
                    <a href="#" class="pt2c1">感动</a>
                <a href="#" class="pt2c1">雷人</a> 
                    <a href="#" class="pt2c1">感动</a>
                <a href="#" class="pt2c2">雷人</a>                 
</div>
</div> -->

    <div class="phb2" style="margin-bottom:10px;">
        <div class="rs_dm_tit">
            <h3>热门动画</h3>
        </div>
        <div class="phb_txt">
            <ul class="phb_txt_bj">
                <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=9d52bf0583c8a26750bf2a671fe57a08&action=lists&catid=32&num=10&order=views+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('catid'=>'32','order'=>'views DESC','limit'=>'10',));}?>
                <?php $i=1;?>
                <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                <li>
                    <i class="<?php if($i < 4 ) { ?>first2<?php } else { ?>first3<?php } ?>"><?php echo $i;?></i>
                    <a href="/dongman<?php echo $r['id'];?>" title="<?php echo $r['title'];?>" target="_blank" class="phb_txtc"><?php echo str_cut($r['title'],40,'');?></a>
                    <span class="phb_txt_hs"><?php echo $r['views'];?></span>
                </li>
                <?php $i++?>
                <?php $n++;}unset($n); ?>
                <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>

            </ul>                          
        </div>
    </div>
    <div style="margin-bottom:10px;"><?php echo GetAd(3);?></div>
    <div class="xh_man">
        <div class="rs_dm_tit">
            <h3>动漫推荐</h3>
        </div>
        <div class="mhtj">
            <ul>
                <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=50a28ede0babb23d0d74638d4dfe9080&action=position&posid=16&order=updatetime+DESC&sort=desc&num=6\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'position')) {$data = $content_tag->position(array('posid'=>'16','order'=>'updatetime DESC','sort'=>'desc','limit'=>'6',));}?>
                <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                <li>
                    <a title="<?php echo $r['title'];?>" target="_blank" href="/dongman<?php echo $r['id'];?>"><img width=98 height=112 src="<?php echo $r['thumb'];?>" /></a>
                    <div class="mhtj_c"><a title="<?php echo $r['title'];?>" target="_blank" href="/dongman<?php echo $r['id'];?>"><?php echo str_cut($r[title],22,'');?></a></div>
                </li>
                <?php $n++;}unset($n); ?>
                <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
            </ul>
        </div>
    </div>

    <div class="xh_man">
        <div class="rs_dm_tit">
            <h3>漫画推荐</h3>
        </div>
        <div class="xh_man_txt">
            <ul>
                <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=67fddeae77b57faeb351dc003fe7c5fb&action=position&posid=14&order=updatetime+DESC&sort=desc&num=6\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'position')) {$data = $content_tag->position(array('posid'=>'14','order'=>'updatetime DESC','sort'=>'desc','limit'=>'6',));}?>
                <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                <li>
                    <a href="/manhua<?php echo $r['id'];?>" title ="<?php echo $r['title'];?>" target="_blank"><img width=80 height=115 src="<?php echo $r['thumb'];?>"></a>
                    <a href="/manhua<?php echo $r['id'];?>" title ="<?php echo $r['title'];?>" target="_blank"><?php echo str_cut($r[title],15,'');?></a>
                </li>
                <?php $n++;}unset($n); ?>
                <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>

            </ul>
        </div>
    </div>
</div>