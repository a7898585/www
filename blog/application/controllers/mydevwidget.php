<?php

/* * **********************
 * 功能：   小窗口
 * author ：wuyq
 * add：    2010-01-22
 * modify   2010-04-20
 * *********************** */

class Widget extends MY_Controller {

    var $userID;
    var $accesskey;

    function Widget() {
        parent::MY_Controller();
        header("Content-Type: text/html; charset=utf-8");
        $this->widget_url = array(
            'jsurl' => 'http://images.cnfol.com/uploads/v5.0/passportweb/script/validator.js',
            'jqueryurl' => 'http://img.cnfol.com/core/js/jquery-1.4.4.min.js'
        );
    }

    /**
     *
     * 功能：发送私信
     *
     * */
    function sendMessage($uid, $domain) {
        $this->_checkUserlogin();
        $uid = $uid ? $uid : $this->input->get_post('friendUserID');
        $friendNickname = $this->input->get_post('friendNickname');

        if (!$uid) {
            die('非法操作');
        }

        $data['widget_url'] = $this->widget_url;
        $data['friendUserID'] = $uid; //接收用户的ID，可多个，用英文的逗号隔开。例如:11234,45678,911123
        $data['fromUserID'] = $this->user['userid']; //发送用户的ID
        $data['friendNickname'] = $friendNickname; //接收私信用户的昵称

        $this->load->view('/module/sendmessage.shtml', $data);
    }

}

//end class