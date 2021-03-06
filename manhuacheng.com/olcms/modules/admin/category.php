<?php
defined('IN_OLCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class category extends admin {
	private $db;
	function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('category_model');
	}
	/**
	 * 管理栏目
	 */
	public function init () {
		$tree = pc_base::load_sys_class('tree');
		$models = getcache('model','commons');
		$sitelist = getcache('sitelist','commons');
		$category_items = array();
		foreach ($models as $modelid=>$model) {
			$category_items[$modelid] = getcache('category_items_'.$modelid,'commons');
		}
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		$categorys = array();
		//读取缓存
		$result = getcache('category_content','commons');
		$show_detail = count($result) < 500 ? 1 : 0;
		$parentid = $_GET['parentid'] ? $_GET['parentid'] : 0;
		$html_root = pc_base::load_config('system','html_root');
		
		//数据库查询
		$types = array(0 => L('category_type_system'),1 => L('category_type_page'),2 => L('category_type_link'));
		foreach($result as $r) {
			if($r['type']==1) {
				$r['listurl']='?m=content&c=content&a=add&catid='.$r['catid'];
				$r['modelname'] = '<a href="?m=content&c=content&a=add&catid='.$r['catid'].'" onclick="open_list(this)"><font color="red">编辑内容</font></a>';
			}else{
				$r['listurl']='?m=content&c=content&a=init&catid='.$r['catid'];
				$r['modelname'] = $models[$r['modelid']]['name'];
			}
				$r['str_manage'] = '';
				if(!$show_detail) {
					if($r['parentid']!=$parentid) continue;
					$r['parentid'] = 0;
					$r['str_manage'] .= '<a href="?m=admin&c=category&a=init&parentid='.$r['catid'].'&menuid='.$_GET['menuid'].'&s='.$r['type'].'">'.L('manage_sub_category').'</a> | ';
			}
			$r['str_manage'] = '<a href="?m=admin&c=category&a=add&parentid='.$r['catid'].'&menuid='.$_GET['menuid'].'&s='.$r['type'].'">'.L('add_sub_category').'</a> | ';
			
			$r['str_manage'] .= '<a href="?m=admin&c=category&a=edit&catid='.$r['catid'].'&menuid='.$_GET['menuid'].'&type='.$r['type'].'">'.L('edit').'</a> | <a href="javascript:confirmurl(\'?m=admin&c=category&a=delete&catid='.$r['catid'].'&menuid='.$_GET['menuid'].'\',\''.L('confirm',array('message'=>addslashes($r['catname']))).'\')">'.L('delete').'</a> ';
			$r['typename'] = $types[$r['type']];
			$r['display_icon'] = $r['ismenu'] ? '' : ' <img src ="'.IMG_PATH.'icon/gear_disable.png" title="'.L('not_display_in_menu').'">';
			if($r['type'] || $r['child']) {
				$r['items'] = ''; 
			} else {
				$r['items'] = $category_items[$r['modelid']][$r['catid']];
			}
			$r['help'] = '';
			$modelid = $r['modelid'];
			$catid = $r['catid'];
			if($r['url']) {
				$r['u'] = "?m=content&c=content&modelid=$modelid&catid=$catid&type=index";
				$r['url'] = "<a href='$r[url]' target='_blank'>".L('vistor')."</a>";
			} else {
				$r['url'] = "<a href='?m=admin&c=category&a=public_cache&menuid=43&module=admin'><font color='red'>".L('update_backup')."</font></a>";
				$r['u'] = "?m=content&c=content&modelid=$modelid&catid=$catid&type=index";
			}		
		$categorys[$r['catid']] = $r;
		}
		$str  = "<tr>
					<td><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input-text-c'></td>
					<td>\$id</td>
					<td>\$spacer<a href='\$u'>\$catname</a>\$display_icon</td>
					<td>\$typename</td>
					<td>\$modelname</td>
					<td align='center'>\$items</td>
					<td align='center'>\$url</td>
					<td align='center' >\$str_manage</td>
				</tr>";
		$tree->init($categorys);
		$categorys = $tree->get_tree(0, $str);
		include $this->admin_tpl('category_manage');
	}
	/**
	 * 添加栏目
	 */
	public function add() {
		if(isset($_POST['dosubmit'])) {
			pc_base::load_sys_func('iconv');
			$_POST['info']['type'] = intval($_POST['type']);
			
			if(isset($_POST['batch_add']) && empty($_POST['batch_add'])) {
				if($_POST['info']['catname']=='') showmessage(L('input_catname'));
				if($_POST['info']['type']!=2) {
					if($_POST['info']['catdir']=='') showmessage(L('input_dirname'));
					if(!$this->public_check_catdir(0,$_POST['info']['catdir'])) showmessage(L('catname_have_exists'));
				}
			}
			$_POST['info']['module'] = 'content';
			$setting = $_POST['setting'];
			if($_POST['info']['type']!=2) {
				//栏目生成静态配置
				if($setting['ishtml']) {
					$setting['category_ruleid'] = $_POST['category_html_ruleid'];
				} else {
					$setting['category_ruleid'] = $_POST['category_php_ruleid'];
					$_POST['info']['url'] = '';
				}
			}
			
			//内容生成静态配置
			if($setting['content_ishtml']) {
				$setting['show_ruleid'] = $_POST['show_html_ruleid'];
			} else {
				$setting['show_ruleid'] = $_POST['show_php_ruleid'];
			}
			if($setting['repeatchargedays']<1) $setting['repeatchargedays'] = 1;
			$_POST['info']['sethtml'] = $setting['create_to_html_root'];
			$_POST['info']['setting'] = array2string($setting);
			
			$end_str = $old_end =  '<script type="text/javascript">window.top.art.dialog({id:"test"}).close();window.top.art.dialog({id:"test",content:\'<h2>'.L("add_success").'</h2><span style="fotn-size:16px;">'.L("following_operation").'</span><br /><ul style="fotn-size:14px;"><li><a href="?m=admin&c=category&a=public_cache&menuid=43&module=admin" target="right"  onclick="window.top.art.dialog({id:\\\'test\\\'}).close()">'.L("following_operation_1").'</a></li><li><a href="'.HTTP_REFERER.'" target="right" onclick="window.top.art.dialog({id:\\\'test\\\'}).close()">'.L("following_operation_2").'</a></li></ul>\',width:"400",height:"200"});</script>';
			if(!isset($_POST['batch_add']) || empty($_POST['batch_add'])) {
				$catname = CHARSET == 'utf-8' ? $_POST['info']['catname'] : iconv('utf-8','gbk',$_POST['info']['catname']);
				$letters = gbk_to_pinyin($catname);
				$_POST['info']['letter'] = strtolower(implode('', $letters));
				if(empty($_POST['info']['catdir'])) $_POST['info']['catdir'] = $_POST['info']['letter'];
				$catid = $this->db->insert($_POST['info'], true);
				$this->update_priv($catid, $_POST['priv_roleid']);
				$this->update_priv($catid, $_POST['priv_groupid'],0);
			} else {
				$end_str = '';
				$batch_adds = explode("\n", $_POST['batch_add']);
				foreach ($batch_adds as $_v) {
					if(trim($_v)=='') continue;
					$names = explode('|', $_v);
					$catname = $names[0];
					$_POST['info']['catname'] = $names[0];
					$letters = gbk_to_pinyin($catname);
					$_POST['info']['letter'] = strtolower(implode('', $letters));
					$_POST['info']['catdir'] = trim($names[1]) ? trim($names[1]) : trim($_POST['info']['letter']);
					if(!$this->public_check_catdir(0,$_POST['info']['catdir'])) {
						$end_str .= $end_str ? ','.$_POST['info']['catname'].'('.$_POST['info']['catdir'].')' : $_POST['info']['catname'].'('.$_POST['info']['catdir'].')';
						continue;
					}
					$catid = $this->db->insert($_POST['info'], true);
					$this->update_priv($catid, $_POST['priv_roleid']);
					$this->update_priv($catid, $_POST['priv_groupid'],0);
				}
				$end_str = $end_str ? L('follow_catname_have_exists').$end_str : $old_end;
			}
			$this->cache();
			showmessage(L('add_success').$end_str);
		} else {
			//获取站点模板信息
			pc_base::load_app_func('global');

			$template_list = template_list(0);
			foreach ($template_list as $k=>$v) {
				$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($template_list[$k]);
			}
			$show_validator = '';
			if(isset($_GET['parentid'])) {
				$parentid = $_GET['parentid'];
				$r = $this->db->get_one(array('catid'=>$parentid));
				if($r) extract($r,EXTR_SKIP);
				$setting = string2array($setting);
			}
			
			pc_base::load_sys_class('form','',0);
			$type = $_GET['s'];
			if($type==0) {
				$exists_model = false;
				$models = getcache('model','commons');	
				foreach($models as $_m) {
					$exists_model = true;
					break;
				}
				if(!$exists_model) showmessage(L('please_add_model'),'?m=content&c=sitemodel&a=init&menuid=59',5000);
				include $this->admin_tpl('category_add');
			} elseif ($type==1) {
				include $this->admin_tpl('category_page_add');
			} else {
				include $this->admin_tpl('category_link');
			}
		}
	}
	/**
	 * 修改栏目
	 */
	public function edit() {
		
		if(isset($_POST['dosubmit'])) {
			pc_base::load_sys_func('iconv');	
			$catid = intval($_POST['catid']);
			$setting = $_POST['setting'];
			//栏目生成静态配置
			if($_POST['type'] != 2) {
				if($setting['ishtml']) {
					$setting['category_ruleid'] = $_POST['category_html_ruleid'];
				} else {
					$setting['category_ruleid'] = $_POST['category_php_ruleid'];
					$_POST['info']['url'] = '';
				}
			}
			//内容生成静态配置
			if($setting['content_ishtml']) {
				$setting['show_ruleid'] = $_POST['show_html_ruleid'];
			} else {
				$setting['show_ruleid'] = $_POST['show_php_ruleid'];
			}
			if($setting['repeatchargedays']<1) $setting['repeatchargedays'] = 1;
			$_POST['info']['sethtml'] = $setting['create_to_html_root'];
			$_POST['info']['setting'] = array2string($setting);
			$_POST['info']['module'] = ROUTE_M;
			$catname = CHARSET == 'utf-8' ? $_POST['info']['catname'] : iconv('utf-8','gbk',$_POST['info']['catname']);
			$letters = gbk_to_pinyin($catname);
			$_POST['info']['letter'] = strtolower(implode('', $letters));
			if(empty($_POST['info']['catdir'])) $_POST['info']['catdir'] = $_POST['info']['letter'];
			$this->db->update($_POST['info'],array('catid'=>$catid));
			$this->update_priv($catid, $_POST['priv_roleid']);
			$this->update_priv($catid, $_POST['priv_groupid'],0);
			$this->cache($_POST['info']['app_all'],$_POST['info']['modelid']);
			//**start - 栏目和内容页URL规则继承设置 11-16**//
			if($setting['app_son'] && $_POST['child']){
				$this->change_son($catid,$setting['category_ruleid'],$setting['show_ruleid']);
			}
			//**end**//
			//更新附件状态
			if($_POST['info']['image'] && pc_base::load_config('system','attachment_stat')) {
				$this->attachment_db = pc_base::load_model('attachment_model');
				$this->attachment_db->api_update($_POST['info']['image'],'catid-'.$catid,1);
			}
			showmessage(L('operation_success').'<script type="text/javascript">window.top.art.dialog({id:"test"}).close();window.top.art.dialog({id:"test",content:\'<h2>'.L("operation_success").'</h2><span style="fotn-size:16px;">'.L("edit_following_operation").'</span><br /><ul style="fotn-size:14px;"><li><a href="?m=admin&c=category&a=public_cache&menuid=43&module=admin" target="right"  onclick="window.top.art.dialog({id:\\\'test\\\'}).close()">'.L("following_operation_1").'</a></li></ul>\',width:"400",height:"200"});</script>','?m=admin&c=category&a=init&module=admin&menuid=43');
		} else {
			//获取站点模板信息
			pc_base::load_app_func('global');
			$template_list = template_list(0);
			foreach ($template_list as $k=>$v) {
				$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($template_list[$k]);
			}
			
			
			$show_validator = '';
			$catid = intval($_GET['catid']);
			pc_base::load_sys_class('form','',0);
			$r = $this->db->get_one(array('catid'=>$catid));
			if($r) extract($r);
			$setting = string2array($setting);
			
			$this->priv_db = pc_base::load_model('category_priv_model');
			$this->privs = $this->priv_db->select(array('catid'=>$catid));
			
			$type = $_GET['type'];
			if($type==0) {
				$fielddb = pc_base::load_model('sitemodel_field_model');
				$f_datas = $fielddb->select(array('modelid'=>$modelid,'isseo'=>1),'field,name',20,'listorder ASC');
				include $this->admin_tpl('category_edit');
			} elseif ($type==1) {
				include $this->admin_tpl('category_page_edit');
			} else {
				include $this->admin_tpl('category_link');
			}
		}	
	}
	/**
	 * 排序
	 */
	public function listorder() {
		if(isset($_POST['dosubmit'])) {
			foreach($_POST['listorders'] as $id => $listorder) {
				$this->db->update(array('listorder'=>$listorder),array('catid'=>$id));
			}
			$this->cache();
			showmessage(L('operation_success'),HTTP_REFERER);
		} else {
			showmessage(L('operation_failure'));
		}
	}
	/**
	 * 删除栏目
	 */
	public function delete() {
		$catid = intval($_GET['catid']);
		$categorys = getcache('category_content','commons');
		$modelid = $categorys[$catid]['modelid'];
		$items = getcache('category_items_'.$modelid,'commons');
		if($items[$catid]) showmessage(L('category_does_not_allow_delete'));
		$this->delete_child($catid);
		$this->db->delete(array('catid'=>$catid));
		$this->cache();
		showmessage(L('operation_success'),HTTP_REFERER);
	}
	/**
	 * 递归删除栏目
	 * @param $catid 要删除的栏目id
	 */
	private function delete_child($catid) {
		$r = $this->db->get_one(array('parentid'=>$catid));
		if($r) {
			$this->delete_child($r['catid']);
			$this->db->delete(array('catid'=>$r['catid']));
		}
		return true;
	}
	/**
	 * 更新缓存
	 */
	public function cache() {
		$categorys = array();
		$models = getcache('model','commons');
		foreach ($models as $modelid=>$model) {
			$datas = $this->db->select(array('modelid'=>$modelid),'catid,type,items',10000);
			$array = array();
			foreach ($datas as $r) {
				if($r['type']==0) $array[$r['catid']] = $r['items'];
			}
			setcache('category_items_'.$modelid, $array,'commons');
		}
		$this->categorys = $this->db->select('','*',120000,'listorder ASC');
		foreach($this->categorys as $r) {
			$setting = string2array($r['setting']);
			$r['create_to_html_root'] = $setting['create_to_html_root'];
			$r['ishtml'] = $setting['ishtml'];
			$r['content_ishtml'] = $setting['content_ishtml'];
			$r['category_ruleid'] = $setting['category_ruleid'];
			$r['show_ruleid'] = $setting['show_ruleid'];
			$r['workflowid'] = $setting['workflowid'];
			$categorys[$r['catid']] = $r;
		}
		if($app_all && $modelid){
			$m = explode(',',$categorys[$app_all]['arrchildid']) ;
			$count = count($m);
			if($count>1){
			$setting = addslashes($categorys[$app_all]['setting']);
			foreach ($m as $k=>$v){
			if($v!=$app_all)$this->db->update(array('modelid'=>$mid,'setting'=>$setting),array('parentid'=>$app_all));
			}
		}
		}
		setcache('category_content',$categorys,'commons');
		return true;
	}
	/**
	 * 更新缓存并修复栏目
	 */
	public function public_cache() {
		$this->repair();
		$this->cache();
		showmessage(L('operation_success'),'?m=admin&c=category&a=init&module=admin&menuid=43');
	}
	/**
	* 修复栏目数据
	*/
	private function repair() {
		pc_base::load_sys_func('iconv');
		@set_time_limit(600);
		$html_root = pc_base::load_config('system','html_root');
		$this->categorys = $categorys = array();
		$this->categorys = $categorys = $this->db->select('', '*', '', 'listorder ASC, catid ASC', '', 'catid');
		
		$this->get_categorys($categorys);
		if(is_array($this->categorys)) {
			foreach($this->categorys as $catid => $cat) {
				if($cat['type'] == 2) continue;
				$arrparentid = $this->get_arrparentid($catid);
				$setting = string2array($cat['setting']);
				$arrchildid = $this->get_arrchildid($catid);
				$child = is_numeric($arrchildid) ? 0 : 1;
				if($categorys[$catid]['arrparentid']!=$arrparentid || $categorys[$catid]['arrchildid']!=$arrchildid || $categorys[$catid]['child']!=$child) $this->db->update(array('arrparentid'=>$arrparentid,'arrchildid'=>$arrchildid,'child'=>$child),array('catid'=>$catid));

				$parentdir = $this->get_parentdir($catid);
				$catname = $cat['catname'];
				$letters = gbk_to_pinyin($catname);
				$letter = strtolower(implode('', $letters));
				$listorder = $cat['listorder'] ? $cat['listorder'] : $catid;
				
				$this->sethtml = $setting['create_to_html_root'];
				//检查是否生成到根目录
				$this->get_sethtml($catid);
				$sethtml = $this->sethtml ? 1 : 0;
				
				if($setting['ishtml']) {
				//生成静态时
					$url = $this->update_url($catid);
					if(!preg_match('/^http:\/\//i', $url)) {
						$url = $sethtml ? '/'.$url : $html_root.'/'.$url;
					}
				} else {
				//不生成静态时
					$url = $this->update_url($catid);
				}
				if($cat['url']!=$url) $this->db->update(array('url'=>$url), array('catid'=>$catid));
				
				
				
				if($categorys[$catid]['parentdir']!=$parentdir || $categorys[$catid]['sethtml']!=$sethtml || $categorys[$catid]['letter']!=$letter || $categorys[$catid]['listorder']!=$listorder) $this->db->update(array('parentdir'=>$parentdir,'sethtml'=>$sethtml,'letter'=>$letter,'listorder'=>$listorder), array('catid'=>$catid));
			}
		}
		
		//删除在非正常显示的栏目
		foreach($this->categorys as $catid => $cat) {
			if($cat['parentid'] != 0 && !isset($this->categorys[$cat['parentid']])) {
				$this->db->delete(array('catid'=>$catid));
			}
		}
		return true;
	}
	/**
	 * 获取父栏目是否生成到根目录
	 */
	private function get_sethtml($catid) {
		foreach($this->categorys as $id => $cat) {
			if($catid==$id) {
				$parentid = $cat['parentid'];
				if($this->categorys[$parentid]['sethtml']) {
					$this->sethtml = 1;
				}
				if($parentid) {
					$this->get_sethtml($parentid);
				}
			}
		}
	}
	
	/**
	 * 找出子目录列表
	 * @param array $categorys
	 */
	private function get_categorys($categorys = array()) {
		if (is_array($categorys) && !empty($categorys)) {
			foreach ($categorys as $catid => $c) {
				$this->categorys[$catid] = $c;
				$result = array();
				foreach ($this->categorys as $_k=>$_v) {
					if($_v['parentid']) $result[] = $_v;
				}
				$this->get_categorys($r);
			}
		} 
		return true;
	}
	/**
	* 更新栏目链接地址
	*/
	private function update_url($catid) {
		$catid = intval($catid);
		if (!$catid) return false;
		$url = pc_base::load_app_class('url', 'content'); //调用URL实例
		return $url->category_url($catid);
	}

	/**
	 * 
	 * 获取父栏目ID列表
	 * @param integer $catid              栏目ID
	 * @param array $arrparentid          父目录ID
	 * @param integer $n                  查找的层次
	 */
	private function get_arrparentid($catid, $arrparentid = '', $n = 1) {
		if($n > 5 || !is_array($this->categorys) || !isset($this->categorys[$catid])) return false;
		$parentid = $this->categorys[$catid]['parentid'];
		$arrparentid = $arrparentid ? $parentid.','.$arrparentid : $parentid;
		if($parentid) {
			$arrparentid = $this->get_arrparentid($parentid, $arrparentid, ++$n);
		} else {
			$this->categorys[$catid]['arrparentid'] = $arrparentid;
		}
		$parentid = $this->categorys[$catid]['parentid'];
		return $arrparentid;
	}

	/**
	 * 
	 * 获取子栏目ID列表
	 * @param $catid 栏目ID
	 */
	private function get_arrchildid($catid) {
		$arrchildid = $catid;
		if(is_array($this->categorys)) {
			foreach($this->categorys as $id => $cat) {
				if($cat['parentid'] && $id != $catid && $cat['parentid']==$catid) {
					$arrchildid .= ','.$this->get_arrchildid($id);
				}
			}
		}
		return $arrchildid;
	}
	/**
	 * 获取父栏目路径
	 * @param  $catid
	 */
	function get_parentdir($catid) {
		if($this->categorys[$catid]['parentid']==0) return '';
		$r = $this->categorys[$catid];
		$setting = string2array($r['setting']);
		$url = $r['url'];
		$arrparentid = $r['arrparentid'];
		unset($r);
		if (strpos($url, '://')===false) {
			if ($setting['creat_to_html_root']) {
				return '';
			} else {
				$arrparentid = explode(',', $arrparentid);
				$arrcatdir = array();
				foreach($arrparentid as $id) {
					if($id==0) continue;
					$arrcatdir[] = $this->categorys[$id]['catdir'];
				}
				return implode('/', $arrcatdir).'/';
			}
		} else {
			if ($setting['create_to_html_root']) {
				if (preg_match('/^((http|https):\/\/)?([^\/]+)/i', $url, $matches)) {
					$url = $matches[0].'/';
					$rs = $this->db->get_one(array('url'=>$url), '`parentdir`,`catid`');
					if ($catid == $rs['catid']) return '';
					else return $rs['parentdir'];
				} else {
					return '';
				}
			} else {
				$arrparentid = explode(',', $arrparentid);
				$arrcatdir = array();
				krsort($arrparentid);
				foreach ($arrparentid as $id) {
					if ($id==0) continue;
					$arrcatdir[] = $this->categorys[$id]['catdir'];
					if ($this->categorys[$id]['parentdir'] == '') break;
				}
				krsort($arrcatdir);
				return implode('/', $arrcatdir).'/';
			}
		}
	}
	/**
	 * 检查目录是否存在
	 * @param  $return_method 返回方法
	 * @param  $catdir 目录
	 */
	public function public_check_catdir($return_method = 1,$catdir = '') {
		$catdir = $catdir ? $catdir : $_GET['catdir'];
		$old_dir = $_GET['old_dir'];
		$r = $this->db->get_one(array('module'=>'content','catdir'=>$catdir));
		if($r && $old_dir != $r['catdir']) {
			//目录存在
			if($return_method) {
				exit('0');
			} else {
				return false;
			}
		} else {
			if($return_method) {
				exit('1');
			} else {
				return true;
			}
		}
	}
	
	/**
	 * 更新权限
	 * @param  $catid
	 * @param  $priv_datas
	 * @param  $is_admin
	 */
	private function update_priv($catid,$priv_datas,$is_admin = 1) {
		$this->priv_db = pc_base::load_model('category_priv_model');
		$this->priv_db->delete(array('catid'=>$catid,'is_admin'=>$is_admin));
		$is_admin ? delcache('priv_'.$catid,'admin') : delcache('priv_'.$catid,'member');
		if(is_array($priv_datas) && !empty($priv_datas)) {
			foreach ($priv_datas as $r) {
				$r = explode(',', $r);
				$action = $r[0];
				$roleid = $r[1];
				$this->priv_db->insert(array('catid'=>$catid,'roleid'=>$roleid,'is_admin'=>$is_admin,'action'=>$action));
			}
			$m = $this->priv_db->select(array('catid'=>$catid,'is_admin'=>$is_admin),'roleid,action');
			foreach ($m as $v){
					$a['action'][] = $v['action'];
					$a[$v['roleid']][] = $v['action'];
			}
			$a['action'] = array_unique($a['action']);
			$is_admin ? setcache('priv_'.$catid, $a,'admin') : setcache('priv_'.$catid, $a,'member');
		}
	}

	/**
	 * 检查栏目权限
	 * @param $action 动作
	 * @param $roleid 角色
	 * @param $is_admin 是否为管理组
	 */
	private function check_category_priv($action,$roleid,$is_admin = 1) {
		$checked = '';
		foreach ($this->privs as $priv) {
			if($priv['is_admin']==$is_admin && $priv['roleid']==$roleid && $priv['action']==$action) $checked = 'checked';
		}
		return $checked;
	}
	/**
	 * 重新统计栏目信息数量
	 */
	public function count_items() {
		$this->content_db = pc_base::load_model('content_model');
		$result = getcache('category_content','commons');
		foreach($result as $r) {
			if($r['type'] == 0) {
				$modelid = $r['modelid'];
				$this->content_db->set_model($modelid);
				$number = $this->content_db->count(array('catid'=>$r['catid']));
				$this->db->update(array('items'=>$number),array('catid'=>$r['catid']));
			}
		}
		showmessage(L('operation_success'),HTTP_REFERER);
	}
	/**
	 * json方式加载模板
	 */
	public function public_tpl_file_list() {
		$style = isset($_GET['style']) && trim($_GET['style']) ? trim($_GET['style']) : exit(0);
		$catid = isset($_GET['catid']) && intval($_GET['catid']) ? intval($_GET['catid']) : 0;
		$batch_str = isset($_GET['batch_str']) ? '['.$catid.']' : '';
		if ($catid) {
			$cat = getcache('category_content','commons');
			$cat = $cat[$catid];
			$cat['setting'] = string2array($cat['setting']);
		}
		pc_base::load_sys_class('form','',0);
		if($_GET['type']==1) {
			$html = array('page_template'=>form::select_template($style, 'content',(isset($cat['setting']['page_template']) && !empty($cat['setting']['page_template']) ? $cat['setting']['page_template'] : 'category'),'name="setting'.$batch_str.'[page_template]"','page'));
		} else {
			$html = array('category_template'=> form::select_template($style, 'content',(isset($cat['setting']['category_template']) && !empty($cat['setting']['category_template']) ? $cat['setting']['category_template'] : 'category'),'name="setting'.$batch_str.'[category_template]"','category'), 
				'list_template'=>form::select_template($style, 'content',(isset($cat['setting']['list_template']) && !empty($cat['setting']['list_template']) ? $cat['setting']['list_template'] : 'list'),'name="setting'.$batch_str.'[list_template]"','list'),
				'show_template'=>form::select_template($style, 'content',(isset($cat['setting']['show_template']) && !empty($cat['setting']['show_template']) ? $cat['setting']['show_template'] : 'show'),'name="setting'.$batch_str.'[show_template]"','show')
			);
		}
		if ($_GET['module']) {
			unset($html);
			if ($_GET['templates']) {
				$templates = explode('|', $_GET['templates']);
				if ($_GET['id']) $id = explode('|', $_GET['id']);
				if (is_array($templates)) {
					foreach ($templates as $k => $tem) {
						$t = $tem.'_template';
						if ($id[$k]=='') $id[$k] = $tem;
						$html[$t] = form::select_template($style, $_GET['module'], $id[$k], 'name="'.$_GET['name'].'['.$t.']" id="'.$t.'"', $tem);
					}
				}
			}
			
		}
		if (CHARSET == 'gbk') {
			$html = array_iconv($html, 'gbk', 'utf-8');
		}
		echo json_encode($html);
	}

	/**
	 * 快速进入搜索
	 */
	public function public_ajax_search() {
		if($_GET['catname']) {
			if(preg_match('/([a-z]+)/i',$_GET['catname'])) {
				$field = 'letter';
				$catname = strtolower(trim($_GET['catname']));
			} else {
				$field = 'catname';
				$catname = trim($_GET['catname']);
				if (CHARSET == 'gbk') $catname = iconv('utf-8','gbk',$catname);
			}
			$result = $this->db->select("$field LIKE('$catname%') AND child=0",'catid,type,catname,letter',10);
			if (CHARSET == 'gbk') {
				$result = array_iconv($result, 'gbk', 'utf-8');
			}
			echo json_encode($result);
		}
	}
	/**
	 * json方式读取风格列表，推送部分调用
	 */
	public function public_change_tpl() {
		pc_base::load_sys_class('form','',0);
		$models = getcache('model','commons');
		$modelid = intval($_GET['modelid']);
		if($_GET['modelid']) {
			$style = $models[$modelid]['default_style'];
			$category_template = $models[$modelid]['category_template'];
			$list_template = $models[$modelid]['list_template'];
			$show_template = $models[$modelid]['show_template'];
			$html = array(
				'template_list'=> $style, 
				'category_template'=> form::select_template($style, 'content',$category_template,'name="setting[category_template]"','category'), 
				'list_template'=>form::select_template($style, 'content',$list_template,'name="setting[list_template]"','list'),
				'show_template'=>form::select_template($style, 'content',$show_template,'name="setting[show_template]"','show')
			);
			if (CHARSET == 'gbk') {
			$html = array_iconv($html, 'gbk', 'utf-8');
			}
			echo json_encode($html);
		}
	}
	/**
	 * 批量修改
	 */
	public function batch_edit() {
		$categorys = getcache('category_content','commons');
		if(isset($_POST['dosubmit'])) {
			
			pc_base::load_sys_func('iconv');	
			$catid = intval($_POST['catid']);
			$post_setting = $_POST['setting'];
			//栏目生成静态配置
			$infos = $_POST['info'];
			if(empty($infos)) showmessage(L('operation_success'));
			$this->attachment_db = pc_base::load_model('attachment_model');
			foreach ($infos as $catid=>$info) {
				$setting = string2array($categorys[$catid]['setting']);
				if($_POST['type'] != 2) {
					if($post_setting[$catid]['ishtml']) {
						$setting['category_ruleid'] = $_POST['category_html_ruleid'][$catid];
					} else {
						$setting['category_ruleid'] = $_POST['category_php_ruleid'][$catid];
						$info['url'] = '';
					}
				}
				foreach($post_setting[$catid] as $_k=>$_setting) {
					$setting[$_k] = $_setting;
				}
				//内容生成静态配置
				if($post_setting[$catid]['content_ishtml']) {
					$setting['show_ruleid'] = $_POST['show_html_ruleid'][$catid];
				} else {
					$setting['show_ruleid'] = $_POST['show_php_ruleid'][$catid];
				}
				if($setting['repeatchargedays']<1) $setting['repeatchargedays'] = 1;
				$info['sethtml'] = $post_setting[$catid]['create_to_html_root'];
				$info['setting'] = array2string($setting);
				
				$info['module'] = 'content';
				$catname = CHARSET == 'gbk' ? $info['catname'] : iconv('utf-8','gbk',$info['catname']);
				$letters = gbk_to_pinyin($catname);
				$info['letter'] = strtolower(implode('', $letters));
				$this->db->update($info,array('catid'=>$catid));

				//更新附件状态
				if($info['image'] && pc_base::load_config('system','attachment_stat')) {
					$this->attachment_db->api_update($info['image'],'catid-'.$catid,1);
				}
			}
			$this->public_cache();
			showmessage(L('operation_success'),'?m=admin&c=category&a=init&module=admin&menuid=43');
		} else {
			if(isset($_POST['catids'])) {
				//获取站点模板信息
				pc_base::load_app_func('global');
				$template_list = template_list(0);
				foreach ($template_list as $k=>$v) {
					$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
					unset($template_list[$k]);
				}
				
				$show_validator = $show_header = '';
				$catid = intval($_GET['catid']);
				$type = $_POST['type'] ? $_POST['type'] : 0;
				pc_base::load_sys_class('form','',0);
				
				if(empty($_POST['catids'])) showmessage(L('illegal_parameters'));
				foreach ($categorys as $catid=>$cat) {
					if($cat['type']==$type && in_array($catid, $_POST['catids'])) {
						$batch_array[$catid] = $cat;
					}
				}
				if(empty($batch_array)) showmessage(L('please_select_category')); 
				$workflows = getcache('workflow','commons');
				if($workflows) {
					$workflows_datas = array();
					foreach($workflows as $_k=>$_v) {
						$workflows_datas[$_v['workflowid']] = $_v['workname'];
					}
				}
				
				if($type==1) {
					include $this->admin_tpl('category_batch_edit_page');
				} else {
					include $this->admin_tpl('category_batch_edit');
				}
			} else {
				$type = isset($_GET['select_type']) ? intval($_GET['select_type']) : 0;
				
				$tree = pc_base::load_sys_class('tree');
				$tree->icon = array('&nbsp;&nbsp;│ ','&nbsp;&nbsp;├─ ','&nbsp;&nbsp;└─ ');
				$tree->nbsp = '&nbsp;&nbsp;';
				$category = array();
				foreach($categorys as $catid=>$r) {
					if(($r['type']==2 && $r['child']==0)) continue;
					$category[$catid] = $r;
				}
				$str  = "<option value='\$catid' \$selected>\$spacer \$catname</option>";
	
				$tree->init($category);
				$string .= $tree->get_tree(0, $str);
				include $this->admin_tpl('category_batch_select');
			}
		}	
	}

	//**start 修改category表中setting字段中数组中的值  11-16**//
	private function change_son($catid,$categoryid,$showid){
		$result = $this->db->select("parentid={$catid}",'setting,parentid',1000);
		foreach($result as $k=>$v){
			$m = string2array($v['setting']);
			$m['category_ruleid'] = $categoryid;
			$m['show_ruleid'] = $showid;
			$data = array2string($m);
			$this->db->update(array('setting'=>$data),array('parentid'=>$v['parentid']));
		}
		return true;
	}
	//**end**//
}
?>