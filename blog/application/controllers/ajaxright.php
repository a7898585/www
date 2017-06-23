<?php

class AjaxRight extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("devfriend_socket");
        $this->load->model("friendmodel");
        $this->load->model("memberblog_socket");
        $this->load->model("blogarticle_socket");
    }

    function rightFans($domainname) {
        $bloginfo = $this->_getBlogInfoByDomain($domainname);
        $param['UserID'] = $bloginfo['UserID'];
        $param['FType'] = 0;
        $param['StartNo'] = 0;
        $param['QryCount'] = 6;
        $focuse = $this->friendmodel->getFriendList($param);
        $extract['focuse'] = $focuse ? $focuse['Record'] : '';
        $extract['focuseCount'] = $focuse['TtlRecords'];

        unset($param);
        $param['UserID'] = $bloginfo['UserID'];
        $param['FType'] = 2;
        $param['StartNo'] = 0;
        $param['QryCount'] = 6;
        $focused = $this->friendmodel->getFriendList($param);
        $extract['focused'] = $focused ? $focused['Record'] : '';
        $extract['focusedCount'] = $focuse['TtlRecords'];


        $extract['bloginfo'] = $bloginfo;
        $this->load->view("ajax/rightfans.shtml", $extract);
    }

}

?>