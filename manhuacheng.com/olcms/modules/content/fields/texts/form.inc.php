	function texts($field, $value, $fieldinfo) {
		extract(string2array($fieldinfo['setting']));
		$list_str = '';
		if($value) {
			$value = string2array(html_entity_decode($value,ENT_QUOTES));
			if(is_array($value)) {
				foreach($value as $_k=>$_v) {
				$list_str .= "<div id='textsfile{$_k}'><input type='text' name='{$field}_fileurl[]' value='{$_v[fileurl]}' style='width:160px;' class='input-text'> <input type='text' name='{$field}_filename[]' value='{$_v[filename]}' style='width:160px;' class='input-text'> <input type='text' name='{$field}_filename2[]' value='{$_v[filename2]}' style='width:160px;' class='input-text'> <a href=\"javascript:remove_div('textsfile{$_k}')\">".L('remove_out')."</a></div>";
				}
			}
		}
        
        $string ='<script type=text/javascript>
		function add_textsfile(returnid) {
		var ids = parseInt(Math.random() * 10000); 
		var str = "<li id=\'textsfile"+ids+"\'><input type=\'text\' name=\'"+returnid+"_fileurl[]\' value=\'\' style=\'width:160px;\' class=\'input-text\'> <input type=\'text\' name=\'"+returnid+"_filename[]\' value=\'附件说明\' style=\'width:160px;\' class=\'input-text\'> <input type=\'text\' name=\'"+returnid+"_filename2[]\' value=\'附件说明\' style=\'width:160px;\' class=\'input-text\'> <a href=\"javascript:remove_div(\'textsfile"+ids+"\')\">移除</a> </li>";
		$(\'#\'+returnid).append(str);
		 }</script>';
        
		$string .= '<input name="info['.$field.']" type="hidden" value="1">
		<fieldset class="blue pad-10">
        <legend>'.L('file_list').'</legend>';
		$string .= $list_str;
		$string .= '<ul id="'.$field.'" class="picList"></ul>
		</fieldset>
		<div class="bk10"></div>
		';
		$string .= $str."<input type=\"button\" class=\"button\" value=\"".L('add_remote_url')."\" onclick=\"add_textsfile('{$field}')\">";
		return $string;
	}
