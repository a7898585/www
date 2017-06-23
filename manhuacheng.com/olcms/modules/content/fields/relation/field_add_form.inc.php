<?php 
$datas = $this->model_db->listinfo(array('type'=>0));
?>
<table cellpadding="2" cellspacing="1" width="98%">
	<tr> 
      <td>
      关联的模块:<select name="setting[modelid]" onchange="javascript:field_list(this.value);">
      <option value='' selected>请选择字段类型</option>
      <?php foreach ($datas as $v){?><option value='<?php echo $v['modelid'];?>'><?php echo $v['name'];?></option><?php }?>
      </select> 
	  用来关联的字段:<select name="setting[relation_field]" id="rl_field"><option>选择关联字段</option></select>
	   关联需得到的值字段:<select name="setting[relation_fieldid]" id="rl_fieldid"><option>选择关联对应值</option></select></td>
    </tr>
    <tr> 
      <td>
      对应本模型的字段值:<select name="setting[relation_field_value]" id="rl_field_value">
      <option value='' selected>请选择对应字段值</option></select> 
	  </tr>
	<tr>
	<td>显示方式：  <input name="setting[showtype]" value="0" type="radio">
        下拉列表方式
        <input name="setting[showtype]" value="1" type="radio" checked>
        自动感知方式  	
	</td></tr>
</table>