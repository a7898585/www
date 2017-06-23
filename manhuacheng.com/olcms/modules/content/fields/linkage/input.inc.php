	function linkage($field, $value) {
		$setting = string2array($this->fields[$field]['setting']);
		if($setting['selected']==1) {
			$v = implode(',', $value);
		}else{
			$v=$value;
		}
		return $v;
	}
