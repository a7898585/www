	function images($field, $value, $fieldinfo) {
		extract($fieldinfo);
		$setting = string2array($setting);
		$list_str = '';
		if($value) {
			$value = string2array(html_entity_decode($value,ENT_QUOTES));
			if(is_array($value)) {
				foreach($value as $_k=>$_v) {
				$list_str .= "<li id='image{$_k}' style='padding:1px'><input type='text' name='{$field}_url[]' value='{$_v[url]}' style='width:310px;' ondblclick='image_priview(this.value);' class='input-text'> <input type='text' name='{$field}_alt[]' value='{$_v[alt]}' style='width:160px;' class='input-text'>";
				foreach($setting['more'] as $v){
					$list_str .= "{$v}:<input type='text' name='{$field}_{$v}[]' value='$_v[$v]' style='width:160px;' class='input-text'>";
					}
				$list_str .= "<a href=\"javascript:remove_div('image{$_k}')\">".L('remove_out', '', 'content')."</a></li>";
				}
			}
		} else {
			$list_str .= "<center><div class='onShow' id='nameTip'>".L('upload_pic_max', '', 'content')." <font color='red'>{$upload_number}</font> ".L('tips_pics', '', 'content')."</div></center>";
		}
		$string = '<input name="info['.$field.']" type="hidden" value="1">
		<fieldset class="blue pad-10">
        <legend>'.L('pic_list').'</legend>';
		$string .= $list_str;
		$string .= '<ul id="'.$field.'" class="picList"></ul>
		</fieldset>
		<div class="bk10"></div>
		';
		if(!defined('IMAGES_INIT')) {
			$str = '<script type="text/javascript" src="statics/js/swfupload/swf2ckeditor.js"></script>';
			define('IMAGES_INIT', 1);
		}
		$more = implode($setting['more'],'|');
		$authkey = upload_key("$upload_number,$upload_allowext,$isselectimage,,,,$more");
		$string .= $str."<div class='picBut cu'><a herf='javascript:void(0);' onclick=\"javascript:flashupload('{$field}_images', '".L('attachment_upload')."','{$field}',change_images,'{$upload_number},{$upload_allowext},{$isselectimage},,,,{$more}','content','$this->catid','{$authkey}')\"/> ".L('select_pic')." </a></div>";
		return $string;
	}