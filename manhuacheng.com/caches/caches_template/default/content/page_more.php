<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php include template("content","header"); ?>
   	<div class="middle_div1">
    	<div class="middle_div1_le" style="float:right">
        	<div class="mhc">
            	<div class="mhc_tit"><h3><?php echo $title;?></h3></div>
                <div class="mhc_txt">
				<?php echo $content;?>
                </div>
            </div>
        </div>
        <div class="middle_div1_ri" style="float:left">
		    <div class="xl_rj">
				<div class="rs_dm_tit">
					<h3><?php echo $CATEGORYS[$CATEGORYS[$catid]['parentid']]['catname'];?></h3>
				</div>
                <div class="xl_rj_txt">
                	<div class="content_box box2">
						<ul>
						<?php $n=1;if(is_array($arrchild_arr)) foreach($arrchild_arr AS $cid) { ?>
							<li<?php if($catid==$cid) { ?> style="background-color: #CCC;margin: 5px 0;opacity: 0.7;"<?php } ?>><a href="<?php echo $CATEGORYS[$cid]['url'];?>"><?php echo $CATEGORYS[$cid]['catname'];?></a></li>
						<?php $n++;}unset($n); ?>
						</ul>
   				    </div>
                </div>
            </div>
		</div>
    </div>
<?php include template("content","footer"); ?>