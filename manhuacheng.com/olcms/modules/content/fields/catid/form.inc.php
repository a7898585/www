	function catid($field, $value, $fieldinfo) {
		if(!$value) $value = $this->catid;
		if((!$value))$value = param::get_cookie('catid');
			$modelid = $this->modelid;
			$tree = pc_base::load_sys_class('tree');
			$tree->icon = array('&nbsp;&nbsp;│ ','&nbsp;&nbsp;├─ ','&nbsp;&nbsp;└─ ');
			$tree->nbsp = '&nbsp;&nbsp;';
			$categorys = array();
			$CATEGORYS = getcache('category_content','commons');
			foreach($CATEGORYS as $cid=>$r) {
				if($modelid && $modelid != $r['modelid']) continue;
				$r['disabled'] = $r['child'] ? 'disabled' : '';
				if($value==$cid && $r['child']){
					$value=0;
				}
				if(!$value && !$r['child']){
					$value=$cid;
				} 
				$r['selected'] = $cid == $value ? 'selected' : '';
				
				$categorys[$cid] = $r;
			}
			foreach($categorys as $cid=>$r) {
				if(!$categorys[$r['parentid']])
					$categorys[$cid]['parentid'] = 0;
			}
			$str  = "<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";

			$tree->init($categorys);
			$string .= $tree->get_tree(0, $str);
			$publish_str = '';
			if(defined('IN_ADMIN') && ROUTE_A=='add') $publish_str = "&nbsp; <a href='javascript:;' onclick=\"omnipotent('selectid','?m=content&c=content&a=add_othors&modelid=".$this->modelid."','".L('publish_to_othor_category')."',1);return false;\" style='color:#B5BFBB'>[".L('publish_to_othor_category')."]</a><ul class='list-dot-othors' id='add_othors_text'></ul>";
			if(ROUTE_A=='add')
			return '<select name="info['.$field.']" id="'.$field.'" style="width:300px;"  onchange="var a = this.value;location.href=\'?m=content&c=content&a=add&menuid=&catid=\'+a">'.$string.'</select>'.$publish_str;
			else
			return '<select name="info['.$field.']" id="'.$field.'" style="width:300px;" >'.$string.'</select>'.$publish_str;
	}