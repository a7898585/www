<?php
defined('IN_ADMIN') or exit('No permission resources.');
include admin::admin_tpl('header','admin');?>
<style type="text/css">
.sbs{
	line-height:30px;
	padding-left:20px;
	font-size:14px;
}
	.sbs{}
	.sbul{margin:10px;}
	.sbul li{line-height:30px;}
	.button{margin-top:20px;}
	.subnav,.ifm{display:none;}
</style>
<div class="pad-10">
<div class="col-2">
<h6>OLcms在线更新系统</h6>
<div class="sbs" id="update_tips" style="height:360px; overflow:auto;">
<?php echo $message;echo $message2;?>
</div>
</div>
<!-- <input name="dosubmit" type="submit" class="dialog" id="dosubmit" value="<?php echo L('start_update')?>" onclick="$('#file').html('');return true;" class="button"> -->
<iframe id="cache_if" name="cache_if" class="ifm"></iframe>
<iframe id="hidden" name="hidden"  width="0" height="0" frameborder=0></iframe>
</div>
<script type="text/javascript">
function addtext(data) {
	$('#file').append(data);
	document.getElementById('update_tips').scrollTop = document.getElementById('update_tips').scrollHeight;
}
</script>
</body>
</html>