<?php 
$datas = $this->model_db->listinfo(array('type'=>0));
$rs_fields = $this->db->select(array('modelid'=>$setting['modelid'],'issystem'=>1),'field,name',100,'listorder ASC');
$parent_fields = $this->db->select(array('modelid'=>$modelid,'issystem'=>1),'field,name',100,'listorder ASC');
?>
<table cellpadding="2" cellspacing="1" width="98%">
	<tr> 
      <td>
      关联的模块:<select name="setting[modelid]" onchange="javascript:field_list(this.value);">
      <option value=''>请选择字段类型</option>
      <?php foreach ($datas as $v){?><option value='<?php echo $v['modelid'];?>' <?php if($setting['modelid']==$v['modelid']) echo 'selected';?>><?php echo $v['name'];?></option><?php }?>
      </select> 
	  用来关联的字段:<select name="setting[relation_field]" id="rl_field"><?php foreach ($rs_fields  as $v){?><option value='<?php echo $v['field'];?>' <?php if($setting['relation_field']==$v['field']) echo 'selected';?>><?php echo $v['name'];?></option><?php }?></select> 
	  关联需得到的值字段:<select name="setting[relation_fieldid]" id="rl_fieldid"><option value='id' <?php if($setting['relation_fieldid']=='id') echo 'selected';?>>ID值</option><?php foreach ($rs_fields  as $v){?><option value='<?php echo $v['field'];?>' <?php if($setting['relation_fieldid']==$v['field']) echo 'selected';?>><?php echo $v['name'];?></option><?php }?></select>
	  </td>
    </tr>
     <tr> 
      <td>
      对应本模型的字段值:<select name="setting[relation_field_value]" id="rl_field_value">
      <option value='' selected>请选择对应字段值</option>
      <?php foreach ($parent_fields  as $v){?><option value='<?php echo $v['field'];?>' <?php if($setting['relation_field_value']==$v['field']) echo 'selected';?>><?php echo $v['name'];?></option><?php }?>
      </select> 
	  </tr>
	<tr>
	<td>显示方式<input name="setting[showtype]" value="0" type="radio" <?php if($setting['showtype']==0) echo 'checked';?>>
        下拉列表方式
        <input name="setting[showtype]" value="1" type="radio" <?php if($setting['showtype']==1) echo 'checked';?>>
        自动感知方式  	
	</td></tr>
</table>

