	function template($field, $value, $fieldinfo) {
		$sitelist = getcache('sitelist','commons');
		$default_style = $sitelist['default_style'];
		return form::select_template($default_style,'content',$value,'name="info['.$field.']" id="'.$field.'"','show');
	}
