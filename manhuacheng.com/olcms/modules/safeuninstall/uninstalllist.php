<?php

/**
 * 卸载表单原因
 */

defined('IN_OLCMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);

class uninstalllist extends admin {
	private $db;
	function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('safeuninstall_model');
		
	}

	/**
	 * 后台卸载表单首页
	 */
	function init() {
		$msg_arr=array(
		 '12070270'=>'我的漫画城不能用了，我想重装',
             '12070271'=>'程序偶尔会出现无响应崩溃的情况',
              '12070272'=>'漫画资源太少',
             '12070273'=>  '玩游戏，看在线视频时会很卡',
              '12070274' =>'运行过程中，提示文件被破坏',
             '12070276'  =>'漫画城有病毒，杀毒软件提示',
               '12070277' => '安装了漫画城软件后，电脑变慢了',
              '12070278' => '发现没有我想要的漫画',
              '12070279' => '使用其他软件时，提示与漫画城不兼容',
              '12070280' => '我已安装其他漫画软件，不需要漫画城',
               '12070281'=>  '习惯在线观看漫画');
		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$uninstalllist=$this->db->listinfo('','',$page);
		$pages = $this->db->pages;
		include $this->admin_tpl('uninstall_init');
	}
	public function uninstallcount(){
		$msg_arr=array(
		 '12070270'=>'我的漫画城不能用了，我想重装',
             '12070271'=>'程序偶尔会出现无响应崩溃的情况',
              '12070272'=>'漫画资源太少',
             '12070273'=>  '玩游戏，看在线视频时会很卡',
              '12070274' =>'运行过程中，提示文件被破坏',
             '12070276'  =>'漫画城有病毒，杀毒软件提示',
               '12070277' => '安装了漫画城软件后，电脑变慢了',
              '12070278' => '发现没有我想要的漫画',
              '12070279' => '使用其他软件时，提示与漫画城不兼容',
              '12070280' => '我已安装其他漫画软件，不需要漫画城',
               '12070281'=>  '习惯在线观看漫画');
		$msg_arr_count=array(
		 '12070270'=>0,
             '12070271'=>0,
              '12070272'=>0,
             '12070273'=> 0,
              '12070274' =>0,
             '12070276'  =>0,
               '12070277' => 0,
              '12070278' => 0,
              '12070279' => 0,
              '12070280' => 0,
               '12070281'=> 0);
		$num=$this->db->count();
		$data=$this->db->select('','unreason',$num);
		foreach($data as $v){
			if($v['unreason']){
				foreach (unserialize($v['unreason']) as $v1){
					if($v1)$msg_arr_count[$v1]+=1;
				}
			}
		}
		
		include $this->admin_tpl('uninstallcount');
	}
	
			


	


	



}

?>