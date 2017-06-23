<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title>olcms - <?php echo L('member','','member').L('manage_center');?></title>
<link href="<?php echo CSS_PATH;?>reset.css" rel="stylesheet" type="text/css" />
<link href="<?php echo CSS_PATH;?>table_form.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>cookie.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>member_common.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>dialog.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>formvalidator.js" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>formvalidatorregex.js" charset="UTF-8"></script>
<script language="JavaScript">
<!--
$(function(){
	$.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});

	$("#username").formValidator({onshow:"<?php echo L('input').L('username');?>",onfocus:"<?php echo L('between_2_to_20');?>"}).inputValidator({min:2,max:20,onerror:"<?php echo L('between_2_to_20');?>"}).regexValidator({regexp:"ps_username",datatype:"enum",onerror:"<?php echo L('username').L('format_incorrect');?>"});
	$("#password").formValidator({onshow:"<?php echo L('input').L('password');?>",onfocus:"<?php echo L('password').L('between_6_to_20');?>"}).inputValidator({min:6,max:20,onerror:"<?php echo L('password').L('between_6_to_20');?>"});

	//$("#code").formValidator({onshow:"<?php echo L('input').L('checkcode');?>",onfocus:"<?php echo L('checkcode').L('cannot_empty');?>"}).inputValidator({min:1,max:20,onerror:"<?php echo L('checkcode').L('cannot_empty');?>"});

});
//-->
</script>

<link href="<?php echo CSS_PATH;?>dialog_simp.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.submit,.pass-logo a,.form-login .input label,.item span{display:inline-block;display:-moz-inline-stack;zoom:1;*display:inline;}
.blue,.blue a{color:#377abe}
.log{line-height:24px; height:24px;float:right; font-size:12px}
.log span{color:#ced9e7}
.log a{color:#049;text-decoration: none;}
.log a:hover{text-decoration: underline;}
#header{ height:94px; background:url(<?php echo IMG_PATH;?>member/h.png) repeat-x}
#header .logo{ padding-right:100px;float:left;background:url(<?php echo IMG_PATH;?>member/login-logo.png) no-repeat right 2px;}
#header .content{width:920px; margin:auto; height:60px;padding:10px 0 0 0}
#content{width:920px; margin:auto; padding:36px 0 0 0}
.form-login{ width:440px; padding-left:40px}
.form-login h2{font-size:25px;color:#494949;border-bottom: 1px dashed #CCC;padding-bottom:3px; margin-bottom:10px}
.form-login .input{ padding:7px 0}
.form-login .input label{ width:84px;font-size:14px; color:#888; text-align:right}
.take,.reg{padding:0 0 0 84px}
.take .submit{ margin-top:10px}
.form-login .hr{background: url(<?php echo IMG_PATH;?>member/line.png) no-repeat left center; height:50px;}
.form-login .hr hr{ display:none}

.submit{padding-left:3px}
.submit,.submit input{ background: url(<?php echo IMG_PATH;?>member/but.png) no-repeat; height:29px;cursor:hand;}
.submit input{background-position: right top; border:none; padding:0 10px 0 7px; font-size:14px}
.reg{ color:#666; line-height:24px}
.reg .submit{background-position: left -35px; height:35px}
.reg .submit input{background-position: right -35px; font-weight:700; color:#fff; height:35px}

.col-1{position:relative; float:right; border:1px solid #c4d5df; zoom:1;background: url(<?php echo IMG_PATH;?>member/member_title.png) repeat-x; width:310px; margin: auto; height:304px}
.col-1 span.o1,
	.col-1 span.o2,
	.col-1 span.o3,
	.col-1 span.o4{position:absolute;width:3px;height:3px; overflow:hidden;background: url(<?php echo IMG_PATH;?>fillet.png) no-repeat}
	.col-1 span.o1{background-position: left -6px; top:-1px; left:-1px}
	.col-1 span.o2{background-position: right -6px; top:-1px; right:-1px}
	.col-1 span.o3{background-position: left -9px; bottom:-1px; left:-1px}
	.col-1 span.o4{background-position: right -9px; bottom:-1px; right:-1px;}
.col-1 .title{color:#386ea8; padding:5px 10px 3px}
.col-1 div.content{padding:0px 10px 10px}
.col-1 div.content h5{background: url(<?php echo IMG_PATH;?>member/ext-title.png) no-repeat 2px 10px; height:34px}
.col-1 div.content h5 strong{ visibility: hidden}
.pass-logo{ margin:auto; width:261px; padding-top:15px}
.pass-logo a img{ border:1px solid #ddd}
.pass-logo a{border:3px solid #fff}
.pass-logo a.logo,
.pass-logo a:hover{border:3px solid #e8f1f1;}
.pass-logo p{border-top: 1px solid #e1e4e8; padding-top:15px}
.item{padding:10px 0; vertical-align:middle; margin-bottom:10px}
.item span{ color:#8c8686}

#footer{color:#666; line-height:24px;width:920px; margin:auto; text-align:center; padding:12px 0; margin-top:52px; border-top:1px solid #e5e5e5}
#footer a{color:#666;}

</style>
</head>
<body>
<div id="header">
	<div class="content">
	<div class="logo"><img src="<?php echo IMG_PATH;?>beta/logo.jpg"/></div>
    <span class="rt log"></span>
    </div>
</div>
<div id="content">
<div class="col-left form-login">
<form method="post" action="" onsubmit="save_username();" id="myform" name="myform">
<input type="hidden" name="forward" id="forward" value="<?php echo $forward;?>">
    	<h2><?php echo L('member').L('login');?></h2>
    	<div class="input">
			<label><?php echo L('username');?>：</label><input type="text" id="username" name="username" size="22" class="input-text">
		</div>
        <div class="input">
			<label><?php echo L('password');?>：</label><input type="password" id="password" name="password" size="22" class="input-text">
		</div>
        <div class="input">
			<label><?php echo L('checkcode');?>：</label><input type="text" id="code" name="code" size="8" class="input-text"><?php echo form::checkcode('code_img', '4', '14', 84, 24);?>
		</div>
        <div class="take">
		<input type="checkbox" name="cookietime" value="2592000" id="cookietime"> <?php echo L('remember');?><?php echo L('username');?>
		<a href="index.php?m=member&c=index&a=public_forget_password" class="blue"><?php echo L('forgetpassword');?></a><br />
		<div class="submit"><input type="submit" name="dosubmit" value="<?php echo L('login');?>"></div></div>
        <div class="hr"><hr /></div>
        <div class="reg"><?php echo L('no_olcms_account');?><br />
        	<div class="submit"><input type="button" name="register" value="<?php echo L('immediately').L('register');?>" onclick="redirect('<?php echo APP_PATH;?>index.php?m=member&c=index&a=register')"></div></div>
</form>
</div>
    <div class="col-auto">
			<?php if($setting['connect_enable']) { ?>
    		<div class="col-1">
            	<div class="content">
                	<h5><strong><?php echo L('use_passport');?></strong></h5>
                    <div class="pass-logo">
						<?php if($setting['snda_enable']) { ?>
                    	<a href="javascript:;" class="logo"><img src="<?php echo IMG_PATH;?>member/logo/1.gif" width="255" height="60" id="snda" /></a>
						<?php } ?>
                        <div class="item"><span><?php echo L('other_passport');?></span>
						<?php if($setting['sina_akey'] || $setting['sina_skey']) { ?>
						<a href="javascript:;" onclick="show_login('sina');return false;"><img src="<?php echo IMG_PATH;?>member/logo/sina.png" /></a>
						<?php } ?>
						</div>
                        <p>
                        	<span class="blue"><?php echo L('other_passport_introduce');?></span>
                            <br />
							<?php echo L('other_passport_info');?>
                        </p>
                    </div>
       	        </div>
            	<span class="o1"></span><span class="o2"></span><span class="o3"></span><span class="o4"></span>
            </div>
			<?php } else { ?>
			<div class="col-1">
			<script language="javascript" src="<?php echo APP_PATH;?>index.php?m=poster&c=index&a=show_poster&id=2"></script>
			</div>
			<?php } ?>
    </div>
</div>

<script language="JavaScript">
<!--

	$(function(){
		$('#username').focus();
	})

	function save_username() {
		if($('#cookietime').attr('checked')==true) {
			var username = $('#username').val();
			setcookie('username', username, 3);
		} else {
			delcookie('username');
		}
	}
	var username = getcookie('username');
	if(username != '' && username != null) {
		$('#username').val(username);
		$('#cookietime').attr('checked',true);
	}

	function show_login(site) {
		if(site == 'sina') {
			art.dialog({lock:false,title:'<?php echo L('sina_login');?>',id:'protocoliframe', iframe:'index.php?m=member&c=index&a=public_sina_login',width:'500',height:'310',yesText:'<?php echo L('close');?>'}, function(){
			});
		}
	}
//-->
</script>

<?php if($setting['connect_enable'] && $setting['snda_enable']) { ?>
<script type="text/javascript">
function loginStatus(s){
	var el=document.getElementById('snda');
	if(s) {
		var snda_enable = getcookie('snda_enable');
		if(snda_enable != '' && snda_enable != null) {
			delcookie('snda_enable');
			window.location = '<?php echo $sndaurl;?>';
		} else {
			el.title = '<?php echo L('click_login');?>';
			el.onclick=function(){
				setcookie('snda_enable', '1', 3);
				ibw_public.openLoginWindow();
			}
		}
	} else{
		el.title = '<?php echo L('click_login');?>';
		el.onclick=function(){
			setcookie('snda_enable', '1', 3);
			ibw_public.openLoginWindow();
		}
	}
}
function ibwSpInit() {
	ibw.appid = <?php echo $setting['snda_enable'];?>;
	ibw.autoInit = false;
	ibw.loginStatusNotifyFn = loginStatus;
	ibw.loginCompleteCallFn = function(){ibw_public.updateLoginStatus()};
}
</script>
<script type="text/javascript" src="<?php echo JS_PATH;?>webwidget.js" charset="utf-8"></script>
<?php } ?>

<?php include template('member', 'footer'); ?>