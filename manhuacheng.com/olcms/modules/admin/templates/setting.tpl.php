<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<script type="text/javascript">
<!--
	$(function(){
		SwapTab('setting','on','',5,<?php echo $_GET['tab'] ? $_GET['tab'] : '1'?>);
		$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'200',height:'50'}, function(){this.close();$(obj).focus();})}});		
		$("#js_path").formValidator({onshow:"<?php echo L('setting_input').L('setting_js_path')?>",onfocus:"<?php echo L('setting_js_path').L('setting_end_with_x')?>"}).inputValidator({onerror:"<?php echo L('setting_js_path').L('setting_input_error')?>"}).regexValidator({regexp:"(.+)\/$",onerror:"<?php echo L('setting_js_path').L('setting_end_with_x')?>"});
		$("#css_path").formValidator({onshow:"<?php echo L('setting_input').L('setting_css_path')?>",onfocus:"<?php echo L('setting_css_path').L('setting_end_with_x')?>"}).inputValidator({onerror:"<?php echo L('setting_css_path').L('setting_input_error')?>"}).regexValidator({regexp:"(.+)\/$",onerror:"<?php echo L('setting_css_path').L('setting_end_with_x')?>"});
		
		$("#img_path").formValidator({onshow:"<?php echo L('setting_input').L('setting_img_path')?>",onfocus:"<?php echo L('setting_img_path').L('setting_end_with_x')?>"}).inputValidator({onerror:"<?php echo L('setting_img_path').L('setting_input_error')?>"}).regexValidator({regexp:"(.+)\/$",onerror:"<?php echo L('setting_img_path').L('setting_end_with_x')?>"});

		$("#not_allow").formValidator({onshow:"<?php echo L('setting_input').L('setting_not_allow_typeid')?>",onfocus:"<?php echo '多个分类id'.L('setting_end_with_xx')?>"}).inputValidator({onerror:"<?php echo L('setting_not_allow_typeid').L('setting_input_error')?>"}).regexValidator({regexp:"[0-9]+(,?[0-9]+)*$",onerror:"<?php echo L('setting_not_allow_typeid').L('setting_end_with_xx')?>"});

		
		$("#upload_url").formValidator({onshow:"<?php echo L('setting_input').L('setting_upload_url')?>",onfocus:"<?php echo L('setting_upload_url').L('setting_end_with_x')?>"}).inputValidator({onerror:"<?php echo L('setting_upload_url').L('setting_input_error')?>"}).regexValidator({regexp:"(.+)\/$",onerror:"<?php echo L('setting_upload_url').L('setting_end_with_x')?>"});
		
		$("#errorlog_size").formValidator({onshow:"<?php echo L('setting_errorlog_hint')?>",onfocus:"<?php echo L('setting_input').L('setting_error_log_size')?>"}).inputValidator({onerror:"<?php echo L('setting_error_log_size').L('setting_input_error')?>"}).regexValidator({regexp:"decmal",datatype:"enum",onerror:"<?php echo L('setting_errorlog_type')?>"});	
			
		$("#uc_api_url").formValidator({onshow:"<?php echo L('setting_uc_type')?>",onfocus:"<?php echo L('setting_uc_type')?>",tipcss:{width:'300px'},empty:false});
		$("#uc_appid").formValidator({onshow:"<?php echo L('input').L('setting_uc_appid')?>",onfocus:"<?php echo L('input').L('setting_uc_appid')?>"});
		$("#uc_version").formValidator({onshow:"<?php echo L('input').L('setting_uc_version')?>",onfocus:"<?php echo L('input').L('setting_uc_version')?>"});
		$("#uc_auth_key").formValidator({onshow:"<?php echo L('input').L('setting_uc_auth_key')?>",onfocus:"<?php echo L('input').L('setting_uc_auth_key')?>"});
		})
//-->
</script>
<div class="pad-10">
<div class="col-tab">
<ul class="tabBut cu-li">
<li id="tab_setting_1" class="on" onclick="SwapTab('setting','on','',10,1);"><?php echo L('setting_basic_cfg')?></li>
<li id="tab_setting_2" onclick="SwapTab('setting','on','',10,2);"><?php echo L('setting_safe_cfg')?></li>
<li id="tab_setting_3" onclick="SwapTab('setting','on','',10,3);"><?php echo L('setting_member_cfg')?></li>
<li id="tab_setting_4" onclick="SwapTab('setting','on','',10,4);"><?php echo L('setting_mail_cfg')?></li>
<li id="tab_setting_5" onclick="SwapTab('setting','on','',10,5);"><?php echo L('setting_manage_cfg')?></li>
<li id="tab_setting_7" onclick="SwapTab('setting','on','',10,7);"><?php echo L('setting_attach_cfg')?></li>
<li id="tab_setting_6" onclick="SwapTab('setting','on','',10,6);"><?php echo L('setting_others_cfg')?></li>
<li id="tab_setting_9" onclick="SwapTab('setting','on','',10,9);"><?php echo L('setting_add')?></li>
</ul>
<div id="div_setting_9" class="contentList pad-10" style="display:none">
  <form name="fadd" action="?m=admin&c=setting&a=add" method="post">
		<table width="100%"  class="table_form">
            <tr>
              <td  width="200"><?php echo L('setting_value_name')?>：</td>
              <td  class="y-bg"><input name="add[varname]" type="text" id="nvarname" class="npvar"  /></td>
              </tr>
              <tr>
              <td><?php echo L('setting_value_value')?>：</td>
              <td class="y-bg"><textarea name="add[varvalue]" rows="8" cols="60"><?php echo $varvalue;?></textarea></td>
            </tr>
            <tr>
              <td><?php echo L('setting_value_type')?>：</td>
              <td  class="y-bg">
              	<input name="add[vartype]" type="radio"  value="string" class='np' checked='checked' />
                文本
                <input name="add[vartype]" type="radio"  value="number" class='np' />
                数字
                <input type="radio" name="add[vartype]" value="bool" class='np' />
                布尔(1/0)
                <input type="radio" name="add[vartype]" value="bstring" class='np' />
                多行文本
              </td>
            </tr>
            <tr>
              <td><?php echo L('setting_value_description')?>：</td>
              <td  class="y-bg"><textarea name="add[varmsg]" rows="8" cols="60"><?php echo $varmsg;?></textarea></td>
              </tr>
            <tr>
              <td><?php echo L('setting_value_final')?>：</td>
              <td  class="y-bg">
              	<input name="add[tofinal]" type="radio"  value="1" class='np' checked='checked' />
                是
                <input name="add[tofinal]" type="radio"  value="0" class='np' />
                否
              </td>
            </tr>
              <tr><td colspan="2"> <input type="submit" name="Submit" value="保存变量" class="button" />（保存完点下面提交）</td></tr>
          </table>
	 </form>
</div>
<form action="?m=admin&c=setting&a=save" method="post" id="myform">
<div id="div_setting_1" class="contentList pad-10">
<table width="100%"  class="table_form">
  <tr>
    <th width="120"><?php echo L('setting_admin_email')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[admin_email]" id="admin_email" size="30" value="<?php echo $admin_email?>"/></td>
  </tr>
</table>
<table width="100%"  class="table_form">
  <tr>
    <th width="120"><?php echo L('setting_delayviewcount')?></th>
    <td class="y-bg">
    <input name="setconfig[delayviewcount]" value="1"  type="radio"  <?php echo ($delayviewcount=='1') ? ' checked' : ''?>> <?php echo L('setting_yes')?>&nbsp;&nbsp;&nbsp;&nbsp;
	<input  name="setconfig[delayviewcount]" value="0" type="radio"  <?php echo ($delayviewcount=='0') ? ' checked' : ''?>> <?php echo L('setting_no')?> </td>
  </tr> 
   <tr>
    <th width="120"><?php echo L('setting_gzip')?></th>
    <td class="y-bg">
    <input name="setconfig[gzip]" value="1"  type="radio"  <?php echo ($gzip=='1') ? ' checked' : ''?>> <?php echo L('setting_yes')?>&nbsp;&nbsp;&nbsp;&nbsp;
	<input  name="setconfig[gzip]" value="0" type="radio"  <?php echo ($gzip=='0') ? ' checked' : ''?>> <?php echo L('setting_no')?></td>
  </tr> 
  <tr>
    <th width="120"><?php echo L('setting_attachment_stat')?></th>
    <td class="y-bg">
    <input name="setconfig[attachment_stat]" value="1"  type="radio"  <?php echo ($attachment_stat=='1') ? ' checked' : ''?>> <?php echo L('setting_yes')?>&nbsp;&nbsp;&nbsp;&nbsp;
	<input  name="setconfig[attachment_stat]" value="0" type="radio"  <?php echo ($attachment_stat=='0') ? ' checked' : ''?>> <?php echo L('setting_no')?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo L('setting_attachment_stat_desc')?></td>
  </tr> 	
  <tr>
    <th width="120"><?php echo L('setting_js_path')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[js_path]" id="js_path" size="50" value="<?php echo JS_PATH?>" /></td>
  </tr>
  <tr>
    <th width="120"><?php echo L('setting_css_path')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[css_path]" id="css_path" size="50" value="<?php echo CSS_PATH?>"/></td>
  </tr> 
  <tr>
    <th width="120"><?php echo L('setting_img_path')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[img_path]" id="img_path" size="50" value="<?php echo IMG_PATH?>" /></td>
  </tr>
  <tr>
    <th width="120"><?php echo L('setting_upload_url')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[upload_url]" id="upload_url" size="50" value="<?php echo $upload_url?>" /></td>
  </tr>  
  <tr>
    <th width="120"><?php echo L('setting_not_allow_typeid')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[not_allow]" id="not_allow" size="50" value="<?php echo NOT_ALLOW?>" /></td>
  </tr>

</table>
</div>
<div id="div_setting_2" class="contentList pad-10 hidden">
	<table width="100%"  class="table_form">
  <tr>
    <th width="120"><?php echo L('setting_admin_log')?></th>
    <td class="y-bg">
	  <input name="setconfig[admin_log]" value="1" type="radio" <?php echo ($admin_log=='1') ? ' checked' : ''?>> <?php echo L('setting_yes')?>&nbsp;&nbsp;&nbsp;&nbsp;
	  <input name="setconfig[admin_log]" value="0" type="radio" <?php echo ($admin_log=='0') ? ' checked' : ''?>> <?php echo L('setting_no')?>
     </td>
  </tr>
  <tr>
    <th width="120"><?php echo L('setting_error_log')?></th>
    <td class="y-bg">
	  <input name="setconfig[errorlog]" value="1" type="radio" <?php echo ($errorlog=='1') ? ' checked' : ''?>> <?php echo L('setting_yes')?>&nbsp;&nbsp;&nbsp;&nbsp;
	  <input name="setconfig[errorlog]" value="0" type="radio" <?php echo ($errorlog=='0') ? ' checked' : ''?>> <?php echo L('setting_no')?>
     </td>
  </tr> 
  <tr>
    <th><?php echo L('setting_error_log_size')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[errorlog_size]" id="errorlog_size" size="5" value="<?php echo $errorlog_size?>"/> MB</td>
  </tr>     

  <tr>
    <th><?php echo L('setting_maxloginfailedtimes')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[maxloginfailedtimes]" id="maxloginfailedtimes" size="10" value="<?php echo $maxloginfailedtimes?>"/></td>
  </tr>

  <tr>
    <th><?php echo L('setting_minrefreshtime')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[minrefreshtime]" id="minrefreshtime" size="10" value="<?php echo $minrefreshtime?>"/> <?php echo L('miao')?></td>
  </tr> 
</table>
</div>
<div id="div_setting_3" class="contentList pad-10 hidden">
<table width="100%"  class="table_form">
  <tr>
    <th width="120"><?php echo L('setting_uc')?></th>
    <td class="y-bg">
    <input name="setconfig[uc]" value="1" type="radio" onclick="showsmtp(this,'ucfg')"  <?php echo ($uc=='1') ? ' checked' : ''?>> <?php echo L('setting_yes')?>&nbsp;&nbsp;&nbsp;&nbsp;
	<input name="setconfig[uc]" value="0" type="radio" onclick="showsmtp(this,'ucfg')" <?php echo ($uc=='0') ? ' checked' : ''?>> <?php echo L('setting_no')?></td>
  </tr> 
<tbody id="ucfg" style="<?php if($uc == 0) echo 'display:none'?>">
  <tr>
    <th><?php echo L('setting_uc_appid')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[uc_appid]" id="uc_appid" size="30" value="<?php echo $uc_appid ?>"/></td>
  </tr> 
  <tr>
    <th><?php echo L('setting_uc_uc_api_url')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[uc_api_url]" id="uc_api_url" size="50" value="<?php echo $uc_api_url ?>"/></td>
  </tr>  
   <tr>
    <th><?php echo L('setting_uc_auth_key')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[uc_auth_key]" id="uc_auth_key" size="50" value="<?php echo $uc_auth_key ?>"/></td>
  </tr>
   <tr>
    <th><?php echo L('setting_uc_version')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[uc_version]" id="uc_version" size="2" value="<?php echo $uc_version ?>"/></td>
  </tr>  
  </tbody>    
  </table>
</div>
<div id="div_setting_4" class="contentList pad-10 hidden">
<table width="100%"  class="table_form">
  <tr>
    <th width="120"><?php echo L('mail_type')?></th>
    <td class="y-bg">
     <input name="setting[mail_type]" checkbox="mail_type" value="1" onclick="showsmtp(this)" type="radio" <?php echo $mail_type==1 ? ' checked' : ''?>> <?php echo L('mail_type_smtp')?>
    <input name="setting[mail_type]" checkbox="mail_type" value="0" onclick="showsmtp(this)" type="radio" <?php echo $mail_type==0 ? ' checked' : ''?> /> <?php echo L('mail_type_mail')?> 
	</td>
  </tr>
  <tbody id="smtpcfg" style="<?php if($mail_type == 0) echo 'display:none'?>">
  <tr>
    <th><?php echo L('mail_server')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[mail_server]" id="mail_server" size="30" value="<?php echo $mail_server?>"/></td>
  </tr>  
  <tr>
    <th><?php echo L('mail_port')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[mail_port]" id="mail_port" size="30" value="<?php echo $mail_port?>"/></td>
  </tr> 
  <tr>
    <th><?php echo L('mail_from')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[mail_from]" id="mail_from" size="30" value="<?php echo $mail_from?>"/></td>
  </tr>   
  <tr>
    <th><?php echo L('mail_auth')?></th>
    <td class="y-bg">
    <input name="setting[mail_auth]" id="mail_auth" value="1" type="radio" <?php echo $mail_auth==1 ? ' checked' : ''?>> <?php echo L('mail_auth_open')?>
	<input name="setting[mail_auth]" id="mail_auth" value="0" type="radio" <?php echo $mail_auth==0 ? ' checked' : ''?>> <?php echo L('mail_auth_close')?></td>
  </tr> 

	  <tr>
	    <th><?php echo L('mail_user')?></th>
	    <td class="y-bg"><input type="text" class="input-text" name="setting[mail_user]" id="mail_user" size="30" value="<?php echo $mail_user?>"/></td>
	  </tr> 
	  <tr>
	    <th><?php echo L('mail_password')?></th>
	    <td class="y-bg"><input type="password" class="input-text" name="setting[mail_password]" id="mail_password" size="30" value="<?php echo $mail_password?>"/></td>
	  </tr>

 </tbody>
  <tr>
    <th><?php echo L('mail_test')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="mail_to" id="mail_to" size="30" value=""/> <input type="button" class="button" onClick="javascript:test_mail();" value="<?php echo L('mail_test_send')?>"></td>
  </tr>           
  </table>
</div>
<div id="div_setting_5" class="contentList pad-10 hidden">
<table width="100%"  class="table_form">
  <tr>
    <th width="120"><?php echo L('content_manage_model')?></th>
    <td class="y-bg">
     <input name="setconfig[content_manage]"  value="1"  type="radio" <?php echo $content_manage==1 ? ' checked' : ''?> /> <?php echo L('content_manage1')?>&nbsp;&nbsp;&nbsp;&nbsp;
    <input name="setconfig[content_manage]"  value="2"  type="radio" <?php echo $content_manage==2 ? ' checked' : ''?> /> <?php echo L('content_manage2')?> 
	</td>
  </tr>
  <tr>
    <th width="120"><?php echo L('updatehost')?></th>
    <td class="y-bg">
    <input type="text" class="input-text" name="setconfig[updatehost]" size="30" value="<?php echo $updatehost;?>"/> 
	</td>
  </tr>   
  </table>
</div>
<div id="div_setting_7" class="contentList pad-10 hidden">
<table width="100%"  class="table_form">
  <tr>
    <th width="120"><?php echo L('setting_gather_img')?></th>
    <td class="y-bg">
    <input name="setconfig[gather_img]" value="1"  type="radio"  <?php echo ($gather_img=='1') ? ' checked' : ''?>> <?php echo L('setting_yes')?>&nbsp;&nbsp;&nbsp;&nbsp;
	<input  name="setconfig[gather_img]" value="0" type="radio"  <?php echo ($gather_img=='0') ? ' checked' : ''?>> <?php echo L('setting_no')?> </td>
  </tr>  
  </table>
</div>


<div id="div_setting_6" class="contentList pad-10 hidden">
<table width="100%"  class="table_form">
<?php 
foreach($group as $k => $v){
?>
  <tr>
    <th width="150"><?php echo $v['info']?></th>
    <td width="60%">
<?php
if($v['type']=='bool')
{
	$c1='';
	$c2 = '';
	$v['value']=='1' ? $c1=" checked" : $c2=" checked";
	echo "<input type='radio' class='np' name='setconfig[{$v['varname']}]' value='1'$c1>是 ";
	echo "<input type='radio' class='np' name='setconfig[{$v['varname']}]' value='0'$c2>否 ";
}else if($v['type']=='bstring')
{
	echo "<textarea name='setconfig[{$v['varname']}]' row='4' id='{$v['varname']}' style='width:70%;height:30px'>".htmlspecialchars($v['value'])."</textarea>";
}else if($v['type']=='number')
{
	echo "<input type='text' class='input-text' name='setconfig[{$v['varname']}]' id='{$v['varname']}' value='{$v['value']}' style='width:30%'>";
}else
{
	echo "<input type='text' class='input-text' name='setconfig[{$v['varname']}]' id='{$v['varname']}' value=\"".htmlspecialchars($v['value'])."\" style='width:80%'>";
}
?>
</td>
	<td><?php if($v['tofinal']){?><span style="color:red">调用方式：{<?php echo strtoupper($v['varname'])?>}</span><?php }else{?><?php echo $v['varname']?><?php }?></td>
	<td><?php if($v['tofinal']){?><a  href="javascript:confirmurl('?m=admin&c=setting&a=vardelete&varname=<?php echo $v[varname];?>','确定要删除？')">X</a><?php }?></td>
</tr> 
<?php 
}
?>
</table>
</div>

<div class="bk15"></div>
<input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="button">
</form>
</body>
<script type="text/javascript">

function SwapTab(name,cls_show,cls_hide,cnt,cur){
    for(i=1;i<=cnt;i++){
		if(i==cur){
			 $('#div_'+name+'_'+i).show();
			 $('#tab_'+name+'_'+i).attr('class',cls_show);
		}else{
			 $('#div_'+name+'_'+i).hide();
			 $('#tab_'+name+'_'+i).attr('class',cls_hide);
		}
	}
}

function showsmtp(obj,hiddenid){
	hiddenid = hiddenid ? hiddenid : 'smtpcfg';
	var status = $(obj).val();
	if(status == 1) $("#"+hiddenid).show();
	else  $("#"+hiddenid).hide();
}
function test_mail() {
	var mail_type = $('input[checkbox=mail_type][checked]').val();
    $.post('?m=admin&c=setting&a=public_test_mail&mail_to='+$('#mail_to').val(),{mail_type:mail_type,mail_server:$('#mail_server').val(),mail_port:$('#mail_port').val(),mail_user:$('#mail_user').val(),mail_password:$('#mail_password').val(),mail_auth:$('#mail_auth').val(),mail_auth:$('#mail_auth').val(),mail_from:$('#mail_from').val()}, function(data){
	alert(data);
	});
}

</script>
</html>