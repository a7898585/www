<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php include template("content","header"); ?>
<!--main-->    
<div class="middle_div1">
    	<div class="middle_div1_le" style="border:none;">
        	<div class="mhzx middle_div1_ri">
				<div class="rs_dm_tit" style="width:100%">
					<h3><?php echo $CATEGORYS[$catid]['catname'];?></h3>
				</div>
			<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=8b4cb33be597462fe6155be4cd415a26&action=lists&catid=%24catid&num=10&moreinfo=1&order=id+DESC&page=%24page\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$pagesize = 10;$page = intval($page) ? intval($page) : 1;if($page<=0){$page=1;}$offset = ($page - 1) * $pagesize;$content_total = $content_tag->count(array('catid'=>$catid,'moreinfo'=>'1','order'=>'id DESC','limit'=>$offset.",".$pagesize,'action'=>'lists',));$pages = pages($content_total, $page, $pagesize, '');$data = $content_tag->lists(array('catid'=>$catid,'moreinfo'=>'1','order'=>'id DESC','limit'=>$offset.",".$pagesize,'action'=>'lists',));}?>
				<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
					<div class="mhzx_txt">
						<div class="mhzx_txt_le"><a href="/zixun<?php echo $r['id'];?>/" target="_blank"><img width='200' height='130' src="<?php echo thumb($r[thumb], 200, 130, 0);?>"/></a></div>
						<div class="mhzx_txt_ri">

							<div class="to">
								<i class="icon"></i>
								<h3>
									<a href="/zixun<?php echo $r['id'];?>/" target="_blank" title="<?php echo $r['title'];?>"><?php echo str_cut($r[title],55,'');?></a>
								</h3>
							</div>
							<div class="pl_le2"><span><?php echo date('Y-m-d H:i',$r[inputtime]);?></span><span>来源:<?php if(empty($r[copyfrom])) { ?>网络<?php } else { ?><?php echo $r['copyfrom'];?><?php } ?></span></div>
							<p><?php echo $r['description'];?><a href="/zixun<?php echo $r['id'];?>/" target="_blank">[阅读全文]</a></p>
							
							<div class="pl">
								<div class="pl_le"><span>浏览：<?php echo $r['views'];?> 次</span><span>评论：<?php if($comment_total) { ?><?php echo $comment_total;?><?php } else { ?>0<?php } ?></span></div>
							</div>
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