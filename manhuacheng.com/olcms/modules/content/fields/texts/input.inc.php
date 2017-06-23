	function texts($field, $value) {
		$files = $_POST[$field.'_fileurl'];
		$files_alt = $_POST[$field.'_filename'];
        $files_alt2 = $_POST[$field.'_filename2'];
		$array = $temp = array();
		if(!empty($files)) {
			foreach($files as $key=>$file) {
					$temp['fileurl'] = $file;
					$temp['filename'] = $files_alt[$key];
                    $temp['filename2'] = $files_alt2[$key];
					$array[$key] = $temp;
			}
		}
		$array = array2string($array);
		return $array;
	}
