<?php
defined ( 'IN_OLCMS' ) or exit ( 'No permission resources.' );
class index {
	function __construct() {
		pc_base::load_app_func ( 'global' );
	
	}
	public function init() {
		
		if (isset ( $_POST['submit'] )) {
			$data=array();
			$data ['title'] = $_POST ['fb_class'];
			$data ['content'] = $_POST ['erroContent'];
			$data ['customer'] = $_POST ['email'];	
			$data ['updatetime'] = SYS_TIME;
			$data ['ip']=ip();
			$feedback_db = pc_base::load_model ( 'feedback_model' );
			$feedback_db->insert ( $data );
			showmessage ( L('feedback_success'), '/index.php?m=feedback&c=index' );
		}
		//SEO设置 
		$SEO = seo ('', L ( 'feedback' ), '', '' );
		include template ( 'feedback', 'index' );
	}
}
?>