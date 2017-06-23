	function copyfrom($field, $value, $fieldinfo) {
		$value_data = '';
		if($value && strpos($value,'|')!==false) {
			$arr = explode('|',$value);
			$value = $arr[0];
			$value_data = $arr[1];
		}
		$copyfrom_array = getcache('copyfrom','admin');
		$copyfrom_datas = array(L('copyfrom_tips'));
		if(!empty($copyfrom_array)) {
			foreach($copyfrom_array as $_k=>$_v) {
				 $copyfrom_datas[$_k] = $_v['sitename'];
			}
		}
		$strinput = "<input type='text' name='info[$field]' id='copyfromname' value='$value' style='width: 400px;' class='input-text'><input type='hidden' name='copyfromid' id='copyfrom' /><input type='button' class='button' onclick='show_comefrom(1239)' value= ".L('add_copyfrom')." />";
		return $strinput;
	}
