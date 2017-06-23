<?php
 $this->type = pc_base::load_model('type_model');
 $datas = $this->type->select(array('module'=>'content','modelid'=>$modelid,'parentid'=>0));
?>
<table cellpadding="2" cellspacing="1" width="96%">
	<tr> 
      <td>默认值</td>
      <td>
	  <input type="hidden" name="setting[minnumber]" value="5">
	  <input type="text" name="setting[defaultvalue]" value="0" size="10" class="input-text"> 正整数 最大长度 5 </td>
    </tr>
	<tr> 
      <td>选择该字段的类别</td>
      <td><select name="setting[sortid]" onchange="javascript:field_list(this.value);">
      <option value='' selected>请选择类别</option>
      <?php foreach ($datas as $k=>$v){?><option value='<?php echo $v['typeid'];?>'><?php echo $v['name'];?></option><?php }?>
      </select></td>
    </tr>
</table>