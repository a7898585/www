<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php include template("content","header"); ?>
<!--main-->    
<script src="<?php echo JS_PATH;?>dww3.min.js"></script>
<div class="middle_div1">
    	<div class="middle_div1_le" style="border:none;">
			   <div class="zixun_banner_le">
				<!-- 幻灯片 -->
				<div id="slider" class="mod-slide-s4">
					<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=98bb998d3de60b6f8a23e276f4ea1d45&action=position&posid=17&order=updatetime+DESC&sort=desc&num=4\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'position')) {$data = $content_tag->position(array('posid'=>'17','order'=>'updatetime DESC','sort'=>'desc','limit'=>'4',));}?>
					<ul class="mod-slide-content J_content">
						<?php $i = 1;?>
						<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
							<li <?php if($i > 1) { ?>style="display: none;"<?php } else { ?>style="display: block;"<?php } ?>>
							<a href="/zixun<?php echo $r['id'];?>/" title="<?php echo $r['title'];?>" target="_blank"><img alt="<?php echo $r['title'];?>" src="<?php echo $r['thumb'];?>" class="big_img"></a>
							<div><h2><?php echo str_cut($r[title],45,'');?></h2><?php echo str_cut($r[description],80,'...');?></div>
						</li>
						<?php $i++;?>
						<?php $n++;}unset($n); ?>
					</ul>
					<ul class="mod-slide-trigger J_nav">
						<?php $i = 1;?>
						<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
						<li <?php if($i == 1) { ?>class="hover"<?php } ?>>
							<a href="/zixun<?php echo $r['id'];?>/" title="<?php echo $r['title'];?>" target="_blank"><img src="<?php echo $r['thumb'];?>" class="thumb_img" ></a>
						</li>
						<?php $i++;?>
						<?php $n++;}unset($n); ?>
					</ul>
					<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
				</div>
                                <div style="margin-bottom:10px;"><?php echo GetAd(4);?></div>
				<script>
					KISSDW.slide("#slider");
				</script>
				<!-- 幻灯片end -->
			</div>

        	<div class="mhzx middle_div1_ri">
				<div class="rs_dm_tit" style="width:100%">
					<h3><?php echo $CATEGORYS[$catid]['catname'];?></h3>
				</div>
			<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=b3067ea51ca638220bc448608993651d&action=lists&catid=%24catid&num=7&moreinfo=1&order=id+DESC&page=%24page\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$pagesize = 7;$page = intval($page) ? intval($page) : 1;if($page<=0){$page=1;}$offset = ($page - 1) * $pagesize;$content_total = $content_tag->count(array('catid'=>$catid,'moreinfo'=>'1','order'=>'id DESC','limit'=>$offset.",".$pagesize,'action'=>'lists',));$pages = pages($content_total, $page, $pagesize, '');$data = $content_tag->lists(array('catid'=>$catid,'moreinfo'=>'1','order'=>'id DESC','limit'=>$offset.",".$pagesize,'action'=>'lists',));}?>
				<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
					<div class="mhzx_txt">
						<div class="mhzx_txt_le"><a href="/zixun<?php echo $r['id'];?>/" target="_blank"><img width='200' height='130' src="<?php echo thumb($r['thumb'], 200, 130, 0);?>"/></a></div>
						<div class="mhzx_txt_ri">

							<div class="to">
								<i class="icon"></i>
								<h3>
									[<a href="<?php echo $CATEGORYS[$r['catid']]['url'];?>" target="_blank" ><?php echo $CATEGORYS[$r['catid']]['catname'];?></a>]
									<a href="/zixun<?php echo $r['id'];?>/" target="_blank" title="<?php echo $r['title'];?>"><?php echo str_cut($r[title],55,'');?></a>
								</h3>
							</div>
							<div class="pl_le2"><span><?php echo date('Y-m-d H:i',$r[inputtime]);?></span><span>来源:<?php if(empty($r[copyfrom])) { ?>网络<?php } else { ?><?php echo $r['copyfrom'];?><?php } ?></span></div>
							<p><?php echo $r['description'];?><a href="/zixun<?php echo $r['id'];?>/" target="_blank">[阅读全文]</a></p>
						</div>
					</div>
				<?php $n++;}unset($n); ?>
			<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>

                <div class="ad02 page" id="search_fy">
				<?php echo $pages;?>     	   
				</div>	
            </div>
        </div>

		<?php include template("content","block_right_zixun"); ?>
	</div>
<?php include template("content","footer"); ?>
