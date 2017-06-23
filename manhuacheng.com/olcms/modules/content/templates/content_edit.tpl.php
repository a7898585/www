<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<style type="text/css">
html,body{ background:#e2e9ea}
</style>
<script type="text/javascript">
<!--
	var charset = '<?php echo CHARSET;?>';
	var uploadurl = '<?php echo pc_base::load_config('system','upload_url')?>';
//-->
</script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>content_addtop.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>colorpicker.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>hotkeys.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>cookie.js"></script>
<script type="text/javascript">var modelid=<?php echo $modelid;?></script>
<form name="myform" id="myform" action="?m=content&c=content&a=edit" method="post" enctype="multipart/form-data">
<div class="addContent">
<div class="crumbs"><?php echo L('edit_content_position');?></div>
<div class="col-right">
    	<div class="col-1">
        	<div class="content pad-6">
<?php
if(is_array($forminfos['senior'])) {
 foreach($forminfos['senior'] as $field=>$info) {
	if($info['isomnipotent']) continue;
	if($info['formtype']=='omnipotent') {
		foreach($forminfos['base'] as $_fm=>$_fm_value) {
			if($_fm_value['isomnipotent']) {
				$info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
			}
		}
		foreach($forminfos['senior'] as $_fm=>$_fm_value) {
			if($_fm_value['isomnipotent']) {
				$info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
			}
		}
	}
 ?>
	<h6><?php if($info['star']){ ?> <font color="red">*</font><?php } ?> <?php echo $info['name']?></h6>
	 <?php echo $info['form']?><?php echo $info['tips']?> 
<?php
} }
?>
          </div>
        </div>
    </div>
    <div class="col-auto">
    	<div class="col-1">
        	<div class="content pad-6">
<table width="100%" cellspacing="0" class="table_form">
	<tbody>	
<?php
if(is_array($forminfos['base'])) {
 foreach($forminfos['base'] as $field=>$info) {
	if($info['isomnipotent']) continue;
	if($info['formtype']=='omnipotent') {
		foreach($forminfos['base'] as $_fm=>$_fm_value) {
			if($_fm_value['isomnipotent']) {
				$info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
			}
		}
		foreach($forminfos['senior'] as $_fm=>$_fm_value) {
			if($_fm_value['isomnipotent']) {
				$info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
			}
		}
	}
 ?>
	<tr>
      <th width="80"><?php if($info['star']){ ?> <font color="red">*</font><?php } ?> <?php echo $info['name']?>
	  </th>
      <td><?php echo $info['form']?>  <?php echo $info['tips']?></td>
    </tr>
<?php
} }
?>

    </tbody></table>
                </div>
        	</div>
        </div>
        
    </div>
</div>
<div class="fixed-bottom">
	<div class="fixed-but text-c">
    <div class="button">
	<input value="<?php if($r['upgrade']) echo $r['url'];?>" type="hidden" name="upgrade">
	<input value="<?php echo $id;?>" type="hidden" name="id"><input value="<?php echo L('save_close');?>" type="submit" name="dosubmit" class="cu" onclick="refersh_window()"></div>
    <div class="button"><input value="<?php echo L('save_continue');?>" type="submit" name="dosubmit_continue" class="cu" onclick="refersh_window()"></div>
    <div class="button"><input value="<?php echo L('c_close');?>" type="button" name="close" onclick="refersh_window();close_window();" class="cu" title="Alt+X"></div>
      </div>
</div>
</form>

</body>
</html>
<script type="text/javascript"> 
<!--
//只能放到最下面
$(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'200',height:'50'}, 	function(){$(obj).focus();
	boxid = $(obj).attr('id');
	if($('#'+boxid).attr('boxid')!=undefined) {
		check_content(boxid);
	}
	})}});
	<?php echo $formValidator;?>
	
/*
 * 加载禁用外边链接
 */
	jQuery(document).bind('keydown', 'Alt+x', function (){close_window();});
})
document.title='<?php echo L('edit_content').addslashes($data['title']);?>';
self.moveTo(0, 0);
function refersh_window() {
	setcookie('refersh_time', 1);
}
function show_comefrom(id) {
	window.top.art.dialog({id:'show_comefrom'}).close();
	window.top.art.dialog({yesText:'<?php echo L('ok');?>',title:'<?php echo L('show_comefrom');?>：'+name,id:'show_comefrom',iframe:'index.php?m=admin&c=copyfrom&a=init&menuid='+id,width:'800',height:'500'}, function(){window.top.art.dialog({id:'edit'}).close()});
}
//-->
</script>