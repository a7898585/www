	function number($field, $value) {
		$setting = string2array($this->fields[$field]['setting']);
		$func = $setting['func'];
		if($setting['isfunc']==1) {
			$value = $func($value);
		}
		return $value;
	}
