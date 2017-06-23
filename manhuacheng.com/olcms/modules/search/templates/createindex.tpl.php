<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="pad-10">
<div class="explain-col">
<form action="" method="get">
<input type="hidden" name="m" value="search">
<input type="hidden" name="c" value="search_admin">
<input type="hidden" name="a" value="createindex">
<input type="hidden" name="menuid" value="909">
<table>
	<tr> 
      <td align="center">
	<?php
			$model_datas = array();
			foreach($types as $_k=>$_v) {
				$model_datas[$_v['modelid']] = $_v['name'];
			}
			echo form::select($model_datas,$modelid,'name="modelid" size="2" style="height:200px;width:130px;"',L('no_limit_model'));
		?>
	</td>
      <td align="center">
<?php echo L('re_index_note');?> <input type="text" name="pagesize" value="100" size="5"> <?php echo L('tiao');?> <input type="submit" name="dosubmit" class="button" value="<?php echo L('confirm_reindex');?>"></form>
 </td>
    </tr>   </table>
</div>

<script language="JavaScript">
<!--
	window.top.$('#display_center_id').css('display','none');
//-->
</script>