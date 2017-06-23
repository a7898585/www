<?php
class formguide_input {
	var $formid;
	var $fields;
	var $data;

    function __construct($formid) {
		$this->formid = $formid;
		$this->fields = getcache('formguide_field_'.$formid, 'model');
		//初始化附件类
		pc_base::load_sys_class('attachment','',0);
		$this->attachment = new attachment('formguide','0');
		$this->site_config = getcache('sitelist','commons');
    }

	function get($data,$isimport = 0) {
		$this->data = $data;
		$info = array();
		foreach($data as $field=>$value) {
			//if(!isset($this->fields[$field]) || check_in($_roleid, $this->fields[$field]['unsetroleids']) || check_in($_groupid, $this->fields[$field]['unsetgroupids'])) continue;
			$name = $this->fields[$field]['name'];
			$minlength = $this->fields[$field]['minlength'];
			$maxlength = $this->fields[$field]['maxlength'];
			$pattern = $this->fields[$field]['pattern'];
			$errortips = $this->fields[$field]['errortips'];
			if(empty($errortips)) $errortips = $name.' '.L('not_meet_the_conditions');
			$length = strlen($value);

			if($minlength && $length < $minlength) {
				if($isimport) {
					return false;
				} else {
					showmessage($name.' '.L('not_less_than').' '.$minlength.L('characters'));
				}
			}
			if($maxlength && $length > $maxlength) {
				if($isimport) {
					$value = str_cut($value,$maxlength,'');
				} else {
					showmessage($name.' '.L('not_more_than').' '.$maxlength.L('characters'));
				}
			} elseif($maxlength) {
				$value = str_cut($value,$maxlength,'');
			}
			if($pattern && $length && !preg_match($pattern, $value) && !$isimport) showmessage($errortips);
			$func = $this->fields[$field]['formtype'];
			if(method_exists($this, $func)) $value = $this->$func($field, $value);
			$info[$field] = $value;
			//颜色选择为隐藏域 在这里进行取值
			if ($_POST['style_color']) $info['style'] = $_POST['style_color'];
			if($_POST['style_font_weight']) $info['style'] = $info['style'].';'.strip_tags($_POST['style_font_weight']);
		}
		return $info;
	}
}?>