	function relation($field, $value, $fieldinfo) {
		$setting = string2array($fieldinfo['setting']);
		$linkageid = $setting['relation'];
		$str= '<input type="text" name="info['.$field.']" value="'.$value.'" id="input_relation" size="20">
		<div id="divSmartList"></div>
		<script language="javascript" type="text/javascript">
		modelid='.$setting['modelid'].';
		field="'.$setting['relation_field'].'";
		fieldid="'.$setting['relation_field_value'].'";
		</script>
		<script language="javascript" type="text/javascript" src="'.JS_PATH.'input.js"></script>';
		return $str;
	}
