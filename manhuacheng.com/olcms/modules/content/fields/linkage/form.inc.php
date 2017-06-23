	function linkage($field, $value, $fieldinfo) {
		$setting = string2array($fieldinfo['setting']);
		$linkageid = $setting['linkageid'];
		if($setting['selected']==1){
			return select_linkages($linkageid,$field,$value,$field);
		}else{
			return menu_linkage($linkageid,$field,$value);		
		}
	}
