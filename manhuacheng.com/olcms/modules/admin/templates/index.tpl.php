<?php defined('IN_ADMIN') or exit('No permission resources.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="off">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8 echo CHARSET?>" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<title><?php echo L('admin_site_title')?></title>
<link href="<?php echo CSS_PATH?>reset.css" rel="stylesheet" type="text/css" />
<link href="<?php echo CSS_PATH?>system.css" rel="stylesheet" type="text/css" />
<link href="<?php echo CSS_PATH?>dialog.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH?>style/styles1.css" title="styles1" media="screen" />
<link rel="alternate stylesheet" type="text/css" href="<?php echo CSS_PATH?>style/styles2.css" title="styles2" media="screen" />
<link rel="alternate stylesheet" type="text/css" href="<?php echo CSS_PATH?>style/styles3.css" title="styles3" media="screen" />
<link rel="alternate stylesheet" type="text/css" href="<?php echo CSS_PATH?>style/styles4.css" title="styles4" media="screen" />
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>styleswitch.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>dialog.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>hotkeys.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery.sGallery.js"></script>
</head>
<body scroll="no">
<div id="dvLockScreen" class="ScreenLock" style="display:<?php if(isset($_SESSION['lock_screen']) && $_SESSION['lock_screen']==0) echo 'none';?>">
    <div id="dvLockScreenWin" class="inputpwd">
    <h5><b class="ico ico-info"></b><span id="lock_tips"><?php echo L('lockscreen_status');?></span></h5>
    <div class="input">
    	<label class="lb"><?php echo L('password')?>：</label><input type="password" id="lock_password" class="input-text" size="24">
        <input type="submit" class="submit" value="&nbsp;" name="dosubmit" onclick="check_screenlock();return false;">
    </div></div>
</div>
<div class="header">
	<div class="logo lf"><a href="<?php echo $currentsite['domain']?>" target="_blank"><span class="invisible"><?php echo L('olcms_title')?></span></a></div>
    <div class="rt">
    	<div class="tab_style white cut_line text-r"><a href="javascript:;" onclick="lock_screen()"><img src="<?php echo IMG_PATH.'icon/lockscreen.png'?>"> <?php echo L('lockscreen')?></a><span>|</span><a href="http://v9.help.phpcms.cn" target="_blank"><?php echo L('help')?></a>
    <ul id="Skin">
		<li class="s1 styleswitch" rel="styles1"></li>
		<li class="s2 styleswitch" rel="styles2"></li>
		<li class="s3 styleswitch" rel="styles3"></li>
        <li class="s4 styleswitch" rel="styles4"></li>
	</ul>
        </div>
        <div class="style_but"></div>
    </div>
    <div class="col-auto" style="overflow: visible">
    	<div class="log white cut_line"><?php echo L('hello'),$admin_username?>  [<?php echo $rolename?>]<span>|</span><a href="?m=admin&c=index&a=public_logout">[<?php echo L('exit')?>]</a><span>|</span>
    		<a href="<?php echo $currentsite['domain']?>" target="_blank" id="site_homepage"><?php echo L('site_homepage')?></a><span>|</span>
    		<a href="?m=member" target="_blank"><?php echo L('member_center')?></a><span>|</span>
    		<a href="?m=search" target="_blank" id="site_search"><?php echo L('search')?></a>
    	</div>
        <ul class="nav white" id="top_menu">
        <?php
        $array = admin::admin_menu(0);
        foreach($array as $_value) {
        	if($_value['id']==4) {
        		echo '<li id="_M'.$_value['id'].'" class="on top_menu"><a href="javascript:_M('.$_value['id'].',\'?m='.$_value['m'].'&c='.$_value['c'].'&a='.$_value['a'].'\')" hidefocus="true" style="outline:none;">'.L($_value['name']).'</a></li>';
        		
        	} else {
        		echo '<li id="_M'.$_value['id'].'" class="top_menu"><a href="javascript:_M('.$_value['id'].',\'?m='.$_value['m'].'&c='.$_value['c'].'&a='.$_value['a'].'\')"  hidefocus="true" style="outline:none;">'.L($_value['name']).'</a></li>';
        	}      	
        }
        ?>

        </ul>
    </div>
</div>
<div id="content">
	<div class="col-left left_menu">
    	<div id="leftMain"></div>
        <a href="javascript:;" id="openClose" style="outline-style: none; outline-color: invert; outline-width: medium;" hideFocus="hidefocus" class="open" title="<?php echo L('spread_or_closed')?>"><span class="hidden"><?php echo L('expand')?></span></a>
    </div>
	<div class="col-1 lf cat-menu" id="display_center_id" style="display:none" height="100%">
	<div class="content">
        	<iframe name="center_frame" id="center_frame" src="" frameborder="false" scrolling="auto" style="border:none" width="100%" height="auto" allowtransparency="true"></iframe>
            </div>
        </div>
    <div class="col-auto mr8">
    <div class="crumbs">
    <div class="shortcut cu-span"><a href="?m=admin&c=cache_all&a=init" target="right"><span><?php echo L('update_backup')?></span></a><a href="javascript:art.dialog({id:'map',iframe:'?m=admin&c=index&a=public_map', title:'<?php echo L('background_map')?>', width:'700', height:'500', lock:true});void(0);"><span><?php echo L('background_map')?></span></a><a href="?m=content&c=create_html&a=public_index" target="right"><span><?php echo L('create_index')?></span></a></div>
    <?php echo L('current_position')?><span id="current_pos"></span></div>
    	<div class="col-1">
        	<div class="content" style="position:relative; overflow:hidden">
                <iframe name="right" id="rightMain" src="?m=admin&c=index&a=public_main" frameborder="false" scrolling="auto" style="border:none; margin-bottom:30px" width="100%" height="auto" allowtransparency="true"></iframe>
                <div class="fav-nav">
					<div id="panellist">
						<?php foreach($adminpanel as $v) {?>
								<span>
								<a onclick="paneladdclass(this);" target="right" href="<?php echo $v['url'].'menuid='.$v['menuid'];?>"><?php echo L($v['name'])?></a>
								<a class="panel-delete" href="javascript:delete_panel(<?php echo $v['menuid']?>, this);"></a></span>
						<?php }?>
					</div>
					<div id="paneladd"></div>
					<input type="hidden" id="menuid" value="">
					<input type="hidden" id="bigid" value="" />
				</div>
        	</div>
        </div>
    </div>
</div>
<script type="text/javascript"> 
//clientHeight-0; 空白值 iframe自适应高度
function windowW(){
	if($(window).width()<980){
			$('.header').css('width',980+'px');
			$('#content').css('width',980+'px');
			$('body').attr('scroll','');
			$('body').css('overflow','');
	}
}
windowW();
$(window).resize(function(){
	if($(window).width()<980){
		windowW();
	}else{
		$('.header').css('width','auto');
		$('#content').css('width','auto');
		$('body').attr('scroll','no');
		$('body').css('overflow','hidden');
		
	}
});
window.onresize = function(){
	var heights = document.documentElement.clientHeight-150;document.getElementById('rightMain').height = heights;
	var openClose = $("#rightMain").height()+39;
	$('#center_frame').height(openClose+9);
	$("#openClose").height(openClose+30);	
}
window.onresize();
//站点下拉菜单
$(function(){
	var offset = $(".tab_web").offset();
	$(".tab_web").mouseover(function(){
			$(".tab-web-panel").css({ "left": +offset.left+4, "top": +offset.top+$('.tab_web').height()+2});
			$(".tab-web-panel").show();
		});
	$(".tab_web span").mouseout(function(){hidden_site_list_1()});
	$(".tab-web-panel").mouseover(function(){clearh();$('.tab_web a').addClass('on')}).mouseout(function(){hidden_site_list_1();$('.tab_web a').removeClass('on')});
	//默认载入左侧菜单
	$("#leftMain").load("?m=admin&c=index&a=public_menu_left&menuid=4");
})
//隐藏站点下拉。
var s = 0;
var h;
function hidden_site_list() {
	s++;
	if(s>=3) {
		$('.tab-web-panel').hide();
		clearInterval(h);
		s = 0;
	}
}
function clearh(){
	if(h)clearInterval(h);
}
function hidden_site_list_1() {
	h = setInterval("hidden_site_list()", 1);
}

//左侧开关
$("#openClose").click(function(){
	if($(this).data('clicknum')==1) {
		$("html").removeClass("on");
		$(".left_menu").removeClass("left_menu_on");
		$(this).removeClass("close");
		$(this).data('clicknum', 0);
	} else {
		$(".left_menu").addClass("left_menu_on");
		$(this).addClass("close");
		$("html").addClass("on");
		$(this).data('clicknum', 1);
	}
	return false;
});

function _M(menuid,targetUrl) {
	$("#menuid").val(menuid);
	$("#bigid").val(menuid);
	$("#paneladd").html('<a class="panel-add" href="javascript:add_panel();"><em><?php echo L('add')?></em></a>');
	if(menuid!=8) {
		$("#leftMain").load("?m=admin&c=index&a=public_menu_left&menuid="+menuid);
	} else {
		$("#leftMain").load("?m=admin&c=uc&a=public_menu_left&menuid="+menuid);
	}
	
	//$("#rightMain").attr('src', targetUrl);
	$('.top_menu').removeClass("on");
	$('#_M'+menuid).addClass("on");
	$.get("?m=admin&c=index&a=public_current_pos&menuid="+menuid, function(data){
		$("#current_pos").html(data);
	});
	//当点击顶部菜单后，隐藏中间的框架
	$('#display_center_id').css('display','none');
	//显示左侧菜单，当点击顶部时，展开左侧
	$(".left_menu").removeClass("left_menu_on");
	$("#openClose").removeClass("close");
	$("html").removeClass("on");
	$("#openClose").data('clicknum', 0);
	$("#current_pos").data('clicknum', 1);
}
function _MP(menuid,targetUrl) {
	$("#menuid").val(menuid);
	$("#paneladd").html('<a class="panel-add" href="javascript:add_panel();"><em><?php echo L('add')?></em></a>');

	$("#rightMain").attr('src', targetUrl+'&menuid='+menuid);
	$('.sub_menu').removeClass("on fb blue");
	$('#_MP'+menuid).addClass("on fb blue");
	$.get("?m=admin&c=index&a=public_current_pos&menuid="+menuid, function(data){
		$("#current_pos").html(data+'<span id="current_pos_attr"></span>');
	});
	$("#current_pos").data('clicknum', 1);
}

function add_panel() {
	var menuid = $("#menuid").val();
	$.ajax({
		type: "POST",
		url: "?m=admin&c=index&a=public_ajax_add_panel",
		data: "menuid=" + menuid,
		success: function(data){
			if(data) {
				$("#panellist").html(data);
			}
		}
	});
}
function delete_panel(menuid, id) {
	$.ajax({
		type: "POST",
		url: "?m=admin&c=index&a=public_ajax_delete_panel",
		data: "menuid=" + menuid,
		success: function(data){
			$("#panellist").html(data);
		}
	});
}

function paneladdclass(id) {
	$("#panellist span a[class='on']").removeClass();
	$(id).addClass('on')
}
setInterval("session_life()", 160000);
function session_life() {
	$.get("?m=admin&c=index&a=public_session_life");
}
function lock_screen() {
	$.get("?m=admin&c=index&a=public_lock_screen");
	$('#dvLockScreen').css('display','');
}
function check_screenlock() {
	var lock_password = $('#lock_password').val();
	if(lock_password=='') {
		$('#lock_tips').html('<font color="red"><?php echo L('password_can_not_be_empty');?></font>');
		return false;
	}
	$.get("?m=admin&c=index&a=public_login_screenlock", { lock_password: lock_password},function(data){
		if(data==1) {
			$('#dvLockScreen').css('display','none');
			$('#lock_password').val('');
			$('#lock_tips').html('<?php echo L('lockscreen_status');?>');
		} else if(data==3) {
			$('#lock_tips').html('<font color="red"><?php echo L('wait_1_hour_lock');?></font>');
		} else {
			strings = data.split('|');
			$('#lock_tips').html('<font color="red"><?php echo L('password_error_lock');?>'+strings[1]+'<?php echo L('password_error_lock2');?></font>');
		}
	});
}
$(document).bind('keydown', 'return', function(evt){check_screenlock();return false;});
</script>
</body>
</html>