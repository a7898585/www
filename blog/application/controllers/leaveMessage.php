<?php

/* * **********************
 * 功能：   博客个人文章
 * 
 * *********************** */

class leavemessage extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    /*
     * 留言
     */

    function message() {
        //	$this->_checkUserlogin();    	
        $extract['shtml'] = "@@@@@";
        $this->load->view("manage/articlelist.shtml", $extract);
    }

}

?>