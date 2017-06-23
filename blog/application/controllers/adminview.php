<?php
/************************
* 功能：   后台访问文章
* author： lifeng
* add：    2011-6-28
*************************/

class Adminview extends MY_Controller
{
    function Adminview()
    {
        parent::MY_Controller();
    }
	
	function postinfo(){
		$flashCode			= $this->input->get_post('flashCode');
        $url				= base64_decode($this->input->get_post('url'));
		
		if(isset($flashCode) && $flashCode == getVerifyStr('ViewAdmin')) 
		{
			session_start();
			$_SESSION['ViewAdmin'] = 'admin';
			/* 
			$data = array('ViewAdmin'=>'admin');
			$this->load->library('session');
			$this->session->set_userdata($data);   */
			echo '<script>';
			echo 'window.location.href=\''.$url.'\'';
			echo '</script>';
		}else{
			echo '没有权限';
			exit;
		}
	}
	
}//end class
?>