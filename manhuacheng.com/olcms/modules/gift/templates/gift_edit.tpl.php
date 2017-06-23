<?php
include $this->admin_tpl('header','admin');
?>
<script type="text/javascript">
<!--
	$(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'200',height:'50'}, function(){this.close();$(obj).focus();})}}); 

	$("#link_name").formValidator({onshow:"<?php echo L("input").L('link_name')?>",onfocus:"<?php echo L("input").L('link_name')?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('link_name')?>"}).ajaxValidator({type : "get",url : "",data :"m=link&c=link&a=public_name&linkid=<?php echo $linkid;?>",datatype : "html",async:'false',success : function(data){	if( data == "1" ){return true;}else{return false;}},buttons: $("#dosubmit"),onerror : "<?php echo L('link_name').L('exists')?>",onwait : "<?php echo L('connecting')?>"}).defaultPassed(); 

	$("#link_url").formValidator({onshow:"<?php echo L("input").L('url')?>",onfocus:"<?php echo L("input").L('url')?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('url')?>"}).regexValidator({regexp:"^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&]*([^<>])*$",onerror:"<?php echo L('link_onerror')?>"})
	
	})
//-->
</script>

<div class="pad_10">
<form action="?m=gift&c=admin_gift&a=edit&id=<?php echo $id; ?>" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">


	<tr>
		<th width="20%">礼物ID：</th>
		<td><input type="text" name="gift[giftid]" id="gift_id"
			size="30" class="input-text" value="<?php echo $giftid;?>"></td>
	</tr>
		<tr>
		<th width="20%">礼物数量：</th>
		<td><input type="text" name="gift[giftnum]" id="gift_num"
			size="30" class="input-text" value="<?php echo $giftnum;?>"></td>
	</tr>
    
    		<tr>
		<th width="20%">礼物人：</th>
		<td><input type="text" name="gift[name]" id="gift_name"
			size="30" class="input-text" value="<?php echo $name;?>"></td>
	</tr>
        		<tr>
		<th width="20%">联系电话：</th>
		<td><input type="text" name="gift[telphone]" id="gift_telphone"
			size="30" class="input-text" value="<?php echo $telphone;?>"></td>
	</tr>
    
    
        		<tr>
		<th width="20%">联系地址：</th>
		<td><input type="text" name="gift[address]" id="gift_address"
			size="30" class="input-text" value="<?php echo $address;?>"></td>
	</tr>
    
        		<tr>
		<th width="20%">邮编：</th>
		<td><input type="text" name="gift[post]" id="gift_post"
			size="30" class="input-text" value="<?php echo $post;?>"></td>
	</tr>
	
	        		<tr>
		<th width="20%">发送状态：</th>
		<td><input type="text" name="gift[send]" id="gift_post"
			size="30" class="input-text" value="<?php if($send==1) echo "已发送"; else echo "未发送";?>"></td>
	</tr>
	

	
	
 
	

 
	
	 


<tr>
		<th></th>
		<td><input type="hidden" name="forward" value="?m=gift&c=admin_gift&a=edit"> <input
		type="submit" name="dosubmit" id="dosubmit" class="dialog"
		value=" <?php echo L('submit')?> "></td>
	</tr>

</table>
</form>
</div>
</body>
</html>

