	function box($field, $value, $fieldinfo) {
		if(!$value) $value = $this->fields[$field]['defaultvalue'];
		$options = explode("\n",$this->fields[$field]['options']);
		foreach($options as $_k) {
			$v = explode("|",$_k);
			$option[$v[1]] = $v[0];
		}
		switch($this->fields[$field]['boxtype']) {
			case 'radio':
				$string = form::radio($option,$value,"name='info[$field]' $this->no_allowed id='$field'");
			break;

			case 'checkbox':
				$string = form::checkbox($option,$value,"name='info[$field][]' $this->no_allowed id='$field'",1);
			break;

			case 'select':
				$string = form::select($option,$value,"name='info[$field]' $this->no_allowed id='$field'");
			break;

			case 'multiple':
				$string = form::select($option,$value,"name='info[$field][]' id='$field' size=2 multiple='multiple' style='height:60px;' $this->no_allowed");
			break;
		}
		return $string;
	}
