<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="pad-10">
<div class="explain-col">
<?php echo L('updatehits_tips');?>
</div>
<div class="bk10"></div>

<div class="table-list">
<table width="100%" cellspacing="0">

<form action="?m=content&c=create_html&a=batch_views" method="post" name="myform">
  <input type="hidden" name="dosubmit" value="1"> 
<thead>
<tr>
<th align="center" width="200"><?php echo L('according_model');?></th>
<th align="center" width="200"><?php echo L('select_operate_content');?></th>
</tr>
</thead>
<tbody  height="200" class="nHover td-line">
	<tr> 
      <td align="center" rowspan="6">
	<?php
			$models = getcache('model','commons');
			$model_datas = array();
			foreach($models as $_k=>$_v) {
				$model_datas[$_v['modelid']] = $_v['name'];
			}
			echo form::select($model_datas,$modelid,'name="modelid" size="2" style="height:200px;width:130px;" onclick="change_model(this.value)"',L('no_limit_model'));
		?>
	</td>
    </tr>
	<tr> 
      <td><?php echo L('last_information');?> <input type="text" name="number" value="100" size="5"> <?php echo L('information_items');?><input type="button" class="button" name="dosubmit2" value=" <?php echo L('submit_start_update');?> " onclick="myform.submit();"></td>
    </tr>
	</tbody>
	</form>
</table>

</div>
</div>
<script language="JavaScript">
<!--
	window.top.$('#display_center_id').css('display','none');
	function change_model(modelid) {
	
		window.location.href='?m=content&c=create_html&a=batch_views&modelid='+modelid;
	}
//-->
</script>