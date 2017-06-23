<?php
defined('IN_ADMIN') or exit('No permission resources.');
include PC_PATH.'modules'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'header.tpl.php';
?>
<div id="main_frameid" class="pad-10 display" style="_margin-right:-12px;_width:98.9%;">
<script type="text/javascript">
$(function(){if ($.browser.msie && parseInt($.browser.version) < 7) $('#browserVersionAlert').show();}); 
function update_online(){
	var data ='';
	$.ajax({
		type: "GET",
		url: "/api.php?op=version&step=1",
		data: data,
		dataType: "json",
		success: returndata
	});	
}
function returndata(result){
	if(result.code=='none'){
		$("#up1").html(result.message);
	}else if(result.code=='update') {
		$("#up").html(result.message);
	}
}
function skipreload(num,ver)
{
	if( window.confirm("忽略后以后都不会再提示这个日期前的升级信息，你确定要忽略这些更新吗?") )
	{
		var data ='lasttime='+num+'&version='+encodeURIComponent(ver);
		$('#updateinfos').innerHTML = "<img src='img/loadinglit.gif' /> 正在处理中...";
		$.ajax({
			type: "GET",
			url: "/api.php?op=version&step=skip",
			data: data,
			dataType: "json",
			success: change
		});
	}
}
function change(result){
	if(result.code=='yes'){
		location.reload();
	}
}
</script>
<div class="explain-col mb10" style="display:none" id="browserVersionAlert">
<?php echo L('ie8_tip')?></div>
<div class="col-2 lf mr10" style="width:48%">
	<h6><?php echo L('personal_information')?></h6>
	<div class="content">
	<?php echo L('main_hello')?><?php echo $admin_username?><br />
	<?php echo L('main_role')?><?php echo $rolename?> <br />
	<div class="bk20 hr"><hr /></div>
	<?php echo L('main_last_logintime')?><?php echo date('Y-m-d H:i:s',$logintime)?><br />
	<?php echo L('main_last_loginip')?><?php echo $loginip?> <br />
	</div>
</div>
<div class="col-2 col-auto"  style="height:137px">
	<h6><?php echo L('main_safety_tips')?></h6>
	<div class="content">
<?php if($pc_writeable) {?>	
<?php echo L('main_safety_permissions')?><br />
<?php } ?>
<?php if(pc_base::load_config('system','debug')) {?>
<?php echo L('main_safety_debug')?><br />
<?php } ?>
<?php if(!pc_base::load_config('system','errorlog')) {?>
<?php echo L('main_safety_errlog')?><br />
<?php } ?>
	<div class="bk20 hr"><hr /></div>	
<?php if(pc_base::load_config('system','execution_sql')) {?>	
<?php echo L('main_safety_sql')?> <br />
<?php } ?>
<?php if($logsize_warning) {?>	
<?php echo L('main_safety_log',array('size'=>$common_cache['errorlog_size'].'MB'))?>
 <br />
<?php } ?>
	</div>
</div>
<div class="bk10"></div>
<div class="col-2 lf mr10" style="width:48%">
	<h6><?php echo L('main_shortcut')?></h6>
	<div class="content" id="admin_panel">
	<?php foreach($adminpanel as $v) {?>
		<span>
			[<a target="right" href="<?php echo $v['url'].'&menuid='.$v['menuid'];?>"><?php echo L($v['name'])?></a>]   
		</span>
	<?php }?>
	</div>
</div>
<div class="col-2 col-auto">
	<h6><?php echo L('main_sysinfo')?></h6>
	<div class="content">
	<?php echo L('main_version')?><?php echo PC_VERSION?> <br />
	<?php echo L('main_os')?><?php echo $sysinfo['os']?> <br />
	<?php echo L('main_web_server')?><?php echo $sysinfo['web_server']?> <br />
	<?php echo L('main_sql_version')?><?php echo $sysinfo['mysqlv']?><br />
	<?php echo L('main_upload_limit')?><?php echo $sysinfo['fileupload']?><br />	
	</span>
	</div>
</div>
    <div class="bk10"></div>
<div class="col-2 col-auto">
	<h6><?php echo L('olcms_news')?></h6>
	<div class="content" id="up">
	<span id='up1'>[<a href='javascript:update_online();'><?php echo L('update_online')?></a>]&nbsp;&nbsp;[<a href="http://doc.258.com/olcms/olcms.rar" target="_blank"><?php echo L('main_latest_version')?></a>]</span>
<!-- <iframe src='http://up.olcms.com/index.html' frameborder='1' width="98%" height='50' frameborder='no' border='0' scrolling='no' allowtransparency='yes'></iframe> -->
	</div>
</div>
    <div class="bk10"></div>
</div>
</body></html>