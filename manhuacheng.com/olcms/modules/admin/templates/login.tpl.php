<?php defined('IN_ADMIN') or exit('No permission resources.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET?>" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<title><?php echo L('olcms_logon')?></title>
<style type="text/css">
@charset "utf-8";
/* LOGIN Document */
body{ 
 margin:0px;
 padding:0px;
 font-size:12px;
 color:#1a77b0;
 font-family:Arial, Helvetica, sans-serif,"宋体";
 background:#1573b6 url(<?php echo IMG_PATH?>admin_img/bg_y.gif) repeat-x;
 overflow-x:hidden;
 overflow-y:hidden}
*{
 margin:0px;
 padding:0px;}
* ul,* ol,* li {list-style:none}
h1,h2,h3,h4,h5,h6{
   font-size:12px;
   font-weight:normal;}
.clear{ 
  clear:both;
  line-height:0px;
  height:0px;
  overflow:hidden;
  font-size:0px;}
.left{
  float:left;}
.right{
  float:right;}
.wrap{
 width:990px;
 margin:0px auto;
 padding-top:5%;
 background:url(<?php echo IMG_PATH?>admin_img/topbg.jpg) no-repeat}
.loginBox{
 width:795px;
 margin:0px auto;}
.logo{
 width:679px;
 height:148px;
 margin-left:116px;
 background-image:url(<?php echo IMG_PATH?>admin_img/logo.png)!important;
 background:no-repeat;}
.logo {
background-image:none;
filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='logo.png' ,sizingMethod='crop');
}
.loginBox_in{
 width:795px;
 height:370px;
 background-image:url(<?php echo IMG_PATH?>admin_img/login_box.png)!important;
 background:no-repeat;}
*html .loginBox_in {
background-image:none;
filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='login_box.png' ,sizingMethod='crop');
}
.loginBox_content{
 width:243px;
 margin:0px auto;
 padding-top:10%;
 position:relative;z-index:9;}
.loginBox_content table td{
 height:45px;}
.userName{
 width:186px;
 height:32px;
 background:url(<?php echo IMG_PATH?>admin_img/bgs.png) 0px 0px no-repeat;}
.userName input,.password input{
 width:146px;
 height:22px;
 margin-top:5px;
 margin-bottom:5px;
 border:0px;
 margin-left:30px;
 margin-right:10px;
 background:none;
 font-size:12px;
 line-height:20px;
 }
.password{
 width:186px;
 height:32px;
 background:url(<?php echo IMG_PATH?>admin_img/bgs.png) 0px -45px no-repeat;}
.login_btn{
 width:70px;
 height:29px;
 border:none;
 cursor:pointer;
 background:url(<?php echo IMG_PATH?>admin_img/bgs.png) -1px -112px no-repeat;}
.checkbox{
 width:14px;
 height:14px;
 float:left;
 list-style-type:none;
 margin-top:15px;
 }
.login_btnContent{
 margin-top:20px;}

</style>
<script language="JavaScript">
<!--YUMN
	if(top!=self)
	if(self!=top) top.location=self.location;
//-->
</script>
<script type="text/javascript">    
    if (document.addEventListener)   
    {//如果是Firefox    
        document.addEventListener("keypress", fireFoxHandler, true);    
    } else  
    {    
        document.attachEvent("onkeypress", ieHandler);    
    }    
    function fireFoxHandler(evt)   
    {    
        //alert("firefox");    
        if (evt.keyCode == 13)   
        {    
            document.forms[0].submit();    
        }    
    }    
    function ieHandler(evt)   
    {    
        //alert("IE");    
        if (evt.keyCode == 13)   
        {    
            document.forms[0].submit();    
        }    
    }   
</script>   

</head>

<body  onload="javascript:document.myform.username.focus();">
<div class="wrap">
   <div class="loginBox">
     <div class="logo"></div>
     <div class="loginBox_in">
       <div class="loginBox_content">
       <form action="index.php?m=admin&c=index&a=login&dosubmit=1" name="myform" method="post">
         <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="24%"><label><?php echo L('username')?>：</label></td>
    <td width="76%"><div class="userName"><input name="username" type="text" class="ipt" value="" /></div></td>
  </tr>
  <tr>
    <td><?php echo L('password')?>：</td>
    <td><div class="password"><input name="password" type="password" class="ipt" value="" /></div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0" class="login_btnContent">
      <tr>
        <td width="54%"><label>
         <input name="button" type="button" class="login_btn" id="button" onclick="document.myform.submit()"/>
        </label></td>
        <td width="46%"><label>
          <input type="checkbox" name="cookietime" id="checkbox" class="checkbox" value="2592000" checked/>
        </label><span style="float:left;margin-top:16px; padding-left:3px;">记住密码？</span></td>
      </tr>
    </table></td>
  </tr>
</table>
</form>
       </div>
     </div>
   </div>
</div>
</body>
</html>