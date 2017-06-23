<?php
/**
 * 卸载表单模块
 */
defined ( 'IN_OLCMS' ) or exit ( 'No permission resources.' );

class index{
	function __construct() {
		$this->db = pc_base::load_model('safeuninstall_model');
	}
	public function init() {
		
		include template('safeuninstall','index');
	}
	
	public function safeform() {
		if($_POST['ver']){
			$reason=serialize($_POST['q_4421626']);
			if($POST['im_detail'])
			$im=$_POST['im_type'].':'.$POST['im_detail'];
			$date=time();
		}
		$this->db->insert(array('ver'=>$_POST['ver'],'unreason'=>$reason,'otherreason'=>$_POST['12070275_reason'],'email'=>$_POST['email'],'phonecode'=>$_POST['mobile'],'im'=>$im,'date'=>$date),true);
		if($this->db->insert_id()){
			showmessage ( '填写成功！！感谢您的配合，我们一定努力做到最好', '/index.php' );		
		}
	}

}
?>