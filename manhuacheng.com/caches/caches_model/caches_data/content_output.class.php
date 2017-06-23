<?php
class content_output {
	var $fields;
	var $data;

	function __construct($modelid,$catid = 0,$categorys = array()) {
		$this->modelid = $modelid;
		$this->catid = $catid;
		$this->categorys = $categorys;
		$this->fields = getcache('model_field_'.$modelid,'model');
    }
	function get($data) {
		$this->data = $data;
		$this->id = $data['id'];
		$info = array();
		foreach($this->fields as $field=>$v) {
			if(!isset($data[$field])) continue;
			$func = $v['formtype'];
			$value = $data[$field];
			$result = method_exists($this, $func) ? $this->$func($field, $data[$field]) : $data[$field];
			if($result !== false) $info[$field] = $result;
		}
		return $info;
	}
function texts($field, $value) {
	return string2array($value);
}
	function editor($field, $value) {
		$setting = string2array($this->fields[$field]['setting']);
		if($setting['enablekeylink']) {
			$value = $this->_keylinks($value, $setting['replacenum'],$setting['link_mode']);
		}
		return $value;
	}
	function _base64_encode($t,$str) {
		return $t."\"".base64_encode($str)."\"";
	}
	function _base64_decode($t,$str) {
		return $t."\"".base64_decode($str)."\"";
	}
	function _keylinks($txt, $replacenum = '',$link_mode = 1) {
		$search = "/(alt\s*=\s*|title\s*=\s*)[\"|\'](.*?)[\"|\']/ise";
		$replace = "\$this->_base64_encode('\\1','\\2')";
		$replace1 = "\$this->_base64_decode('\\1','\\2')";
		$txt = preg_replace($search, $replace, $txt);
		$keywords = $this->data['keywords'];
		if($keywords) $keywords = strpos($keywords,',') === false ? explode(' ',$keywords) : explode(',',$keywords);
		if($link_mode && !empty($keywords)) {
			foreach($keywords as $keyword) {
				$linkdatas[] = $keyword;
			}
		} else {
			$linkdatas = getcache('keylink','commons');
		}
		if($linkdatas) {
			$word = $replacement = array();
			foreach($linkdatas as $v) {
				if($link_mode && $keywords) {
					$word1[] = '/'.preg_quote($v, '/').'/';
					$word2[] = $v;
					$replacement[] = '<a href="javascript:;" onclick="show_ajax(this)" class="keylink">'.$v.'</a>';
				} else {
					$word1[] = '/'.preg_quote($v[0], '/').'/';
					$word2[] = $v[0];
					
					$replacement[] = '<a href="'.$v[1].'" target="_blank" class="keylink">'.$v[0].'</a>';
				}
			}
			if($replacenum != '') {
				$txt = preg_replace($word1, $replacement, $txt, $replacenum);
			} else {
				$txt = str_replace($word2, $replacement, $txt);
			}
		}
		$txt = preg_replace($search, $replace1, $txt);
		return $txt;
	}
	function title($field, $value) {
		$value = htmlspecialchars($value);
		return $value;
	}
	function box($field, $value) {
		extract(string2array($this->fields[$field]['setting']));
		if($outputtype==1) {
			return $value;
		} else {
			$options = explode("\n",$this->fields[$field]['options']);
			foreach($options as $_k) {
				$v = explode("|",$_k);
				$k = trim($v[1]);
				if($outputtype==2) {
				$option[$k] = $v;
				}else{
				$option[$k] = $v[0];
				}
			}
			$string = '';
			switch($this->fields[$field]['boxtype']) {
				case 'radio':
					$string = $option[$value];
				break;

				case 'checkbox':
					$value_arr = explode(',',$value);
					foreach($value_arr as $_v) {
						if($_v) $string .= $option[$_v].' 、';
					}
				break;

				case 'select':
					$string = $option[$value];
				break;

				case 'multiple':
					$value_arr = explode(',',$value);
					foreach($value_arr as $_v) {
						if($_v) $string .= $option[$_v].' 、';
					}
				break;
			}
			return $string;
		}
	}
	function images($field, $value) {
		return string2array($value);
	}
	function datetime($field, $value) {
		$setting = string2array($this->fields[$field]['setting']);
		extract($setting);
		if($fieldtype=='date') {
			$format_txt = 'Y-m-d';
		} elseif($fieldtype=='datetime') {
			$format_txt = 'Y-m-d H:i';
		} else {
			$format_txt = $format;
		}
		if(strlen($format_txt)<6) {
			$isdatetime = 0;
		} else {
			$isdatetime = 1;
		}
		if(!$value) $value = SYS_TIME;
		$value = date($format_txt,$value);
		return $value;
	}
	function keyword($field, $value) {
	    if($value == '') return '';
		$v = '';
		if(strpos($value, ',')===false) {
			$tags = explode(' ', $value);
		} else {
			$tags = explode(',', $value);
		}
		return $tags;
	}	function copyfrom($field, $value) {
		static $copyfrom_array;
		if(!$copyform_array) $copyfrom_array = getcache('copyfrom','admin');
		if($value && strpos($value,'|')!==false) {
			$arr = explode('|',$value);
			$value = $arr[0];
			$value_data = $arr[1];
		}
		if($value_data) {
			$copyfrom_link = $copyfrom_array[$value_data];
			if(!empty($copyfrom_array)) {
				$imgstr = '';
				if($copyfrom_link['thumb']) $imgstr = "<a href='{$copyfrom_link[siteurl]}' target='_blank'><img src='{$copyfrom_link[thumb]}' height='15'></a> ";
				return $imgstr."<a href='$value' target='_blank' style='color:#AAA'>{$copyfrom_link[sitename]}</a>";
			}
		} else {
			return $value;
		}
	}
	function groupid($field, $value) {
		if($value) $value = explode(',',$value);
		return $value;
	}
	function linkage($field, $value) {
		$setting = string2array($this->fields[$field]['setting']);
		$datas = getcache($setting['linkageid'],'linkage');
		$infos = $datas['data'];
		if($setting['showtype']==1) {
			$result = $this->_get_parent($value, $setting['linkageid'], $setting['space']);
		} elseif($setting['showtype']==2) {
			$result = $value;
		} else {
			$result['name'] = $infos[$value]['name'];
			$result['id'] = $infos[$value]['linkageid'];

		}
		return $result;
	}
	function _get_parent($linkageid, $keyid, $space = '>', $result = array(), $infos = array()) {
		if($space=='' || !isset($space))$space = '>';
		if(!$infos) {
			$datas = getcache($keyid,'linkage');
			$infos = $datas['data'];
		}
		if(array_key_exists($linkageid,$infos)) {
			$result[]=$infos[$linkageid]['name'];
			return $this->_get_parent($infos[$linkageid]['parentid'], $keyid, $space, $result, $infos);
		} else {
			if(count($result)>0) {
				krsort($result);
				$result = implode($space,$result);
				return $result;
			}
			else {
				return $result;
			}
		}			
	}
	function downfile($field, $value) {
		extract(string2array($this->fields[$field]['setting']));
		$list_str = array();
		if($value){
			$value_arr = explode('|',$value);
			$fileurl = $value_arr['0'];
			$sel_server = $value_arr['1'] ? explode(',',$value_arr['1']) : '';
		} 
		$server_list = getcache('downservers','commons');
		if(is_array($server_list)) {
			foreach($server_list as $_k=>$_v) {
				if($value && is_array($sel_server) && in_array($_k,$sel_server)) {
					$downloadurl = $_v[siteurl].$fileurl;
					if($downloadlink) {
						$a_k = urlencode(sys_auth("i=$this->id&s=$_v[siteurl]&m=1&f=$fileurl&d=$downloadtype&modelid=$this->modelid&catid=$this->catid", 'ENCODE', pc_base::load_config('system','auth_key')));
						$list_str[] = "<a href='".APP_PATH."index.php?m=content&c=down&a_k={$a_k}' target='_blank'>{$_v[sitename]}</a>";
					} else {
						$list_str[] = "<a href='{$downloadurl}' target='_blank'>{$_v[sitename]}</a>";
					}
				}
			}
		}	
		return $list_str;
	}
	function downfiles($field, $value) {
		extract(string2array($this->fields[$field]['setting']));
		$list_str = array();
		$file_list = string2array($value);
		if(is_array($file_list)) {
			foreach($file_list as $_k=>$_v) {	
				if($_v[fileurl]){
					$filename = $_v[filename] ? $_v[filename] : L('click_to_down');
					if($downloadlink) {
						$a_k = urlencode(sys_auth("i=$this->id&s=&m=1&f=$_v[fileurl]&d=$downloadtype&modelid=$this->modelid&catid=$this->catid", 'ENCODE', pc_base::load_config('system','auth_key')));
						$list_str[] = "<a href='".APP_PATH."index.php?m=content&c=down&a_k={$a_k}' target='_blank'>{$filename}</a>";
					} else {
						$list_str[] = "<a href='{$_v[fileurl]}' target='_blank'>{$filename}</a>";
					}
				}
			}
		}
		return $list_str;		
	}

 } 
?>