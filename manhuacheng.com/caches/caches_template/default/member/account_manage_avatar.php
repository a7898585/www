<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php include template('member', 'header'); ?>
<div id="memberArea">
	<?php include template('member', 'account_manage_left'); ?>
	<div class="col-auto">
		<div class="col-1 ">
			<h5 class="title"><?php echo L('modify').L('avatar');?></h5>
			<div class="content">
			<?php if($this->ucopen) { ?>
				<img src='<?php echo $avatar['small'];?>' /><br /><?php echo $avatar_html;?>
				<ul class="col-right col-avatar" id="avatarlist">
				  <?php $n=1; if(is_array($avatar)) foreach($avatar AS $k => $v) { ?>
					<li>
						<img src="<?php echo $v;?>" onerror="this.src='<?php echo IMG_PATH;?>nophoto.gif'"><br />
						<?php echo L('avatar');?><?php echo $k;?> x <?php echo $k;?>
					</li>
				  <?php $n++;}unset($n); ?>
				</ul>
			<?php } else { ?>
				<?php echo getavatar_upload_html($memberinfo['userid']);?>
				<ul class="col-right col-avatar" id="avatarlist">
				  <?php $n=1; if(is_array($avatar)) foreach($avatar AS $k => $v) { ?>
					<li>
						<img src="<?php echo $v;?>" onerror="this.src='<?php echo IMG_PATH;?>nophoto.gif'"><br />
						<?php echo $k;?><?php echo L('avatar');?>
					</li>
				  <?php $n++;}unset($n); ?>
				</ul>
				<div class="col-auto">
					<div id="myContent"> 
					  <p>Alternative content</p> 
					</div>
				</div>
			<?php } ?>
			</div>
			<span class="o1"></span><span class="o2"></span><span class="o3"></span><span class="o4"></span>
		</div>
	</div>
</div>
<div class="clear"></div>
<?php include template('member', 'footer'); ?>