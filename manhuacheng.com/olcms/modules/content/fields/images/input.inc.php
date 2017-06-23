	function images($field, $value) {
		//取得图片列表
		$pictures = $_POST[$field.'_url'];
		$type = $_POST['type']=='cx_picture' ? 1 : 0;
		//取得图片说明
		$pictures_alt = isset($_POST[$field.'_alt']) ? $_POST[$field.'_alt'] : array();
		$setting = string2array($this->fields[$field]['setting']);
		$array = $temp = array();
		if($type){
			$site_setting = string2array($this->site_config['setting']);
			$watermark_enable = intval($site_setting['watermark_enable']);
			$pictures = $this->attachment->download('pictureurls', $pictures,$watermark_enable);
		}
		if(!empty($pictures)) {
			foreach($pictures as $key=>$pic) {
				$temp['url'] = $pic;
				$temp['alt'] = $pictures_alt[$key];
				foreach ($setting['more'] as $v){
				$temp[$v] = $_POST[$field.'_'.$v][$key];
				}
				$array[$key] = $temp;
			}
		}
		$array = array2string($array);
		return $array;
	}
