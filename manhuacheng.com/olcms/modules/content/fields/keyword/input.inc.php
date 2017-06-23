	function keyword($field, $value) {
		$value=_trim($value);
		if(!$value) return false;
		$value=str_replace(' ', ',', $value);
		$value=str_replace('，', ',', $value);
		return $value;
	}