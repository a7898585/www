<?php

class Userblogapi_socket extends MY_Model {

    function Userblogapi_socket() {
        parent::MY_Model();
    }

    //获取博客访客信息
    function get_blogvisitor($memberid, $_debug = 0) {
        $data = array();
        $keyStr = str_replace('memberid', $memberid, 'visitor_blog_memberid');
        $data = $this->cache->get($keyStr);

        if (empty($data) || $_debug) {
            $data = $this->getBlogVisitor($memberid);
            $flag = $this->cache->set($keyStr, $data, 86400);
        }
        return $data;
    }

    //获取博客访客记录
    function getBlogVisitor($memberid) {
        $type = 'B072';
        $data['MemberID'] = $memberid;
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs['type'] = $type;
        $rs['args'] = $data;
        $rs = $this->_checkrs($rs);
        $rs = (isset($rs['Record']) && $rs['Record'] != false) ? $rs['Record'] : array();
        return $rs;
    }

    /*
      --|增加个人博客文章
     */

    function addBlogArticleApi($data) {
        $type = 'B508';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);
//        print_r($data);
        if ($this->_checkrs($rs, $type) && $rs['TtlRecords'] > 0) {
            return $rs;
        } else {
            return false;
        }
    }

    /**
     * 删 除 個人动态
     * @param type $data
     */
    function deleteArticleApi($param) {
        $type = 'B509';
        $data = array(
            'UserID' => $param['userid'],
            'AppID' => 1,
            'ParentPostID' => $param['ArticleIDs']
        );
//        print_r($data);
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);
//        print_r($rs);
        if ($this->_checkrs($rs, $type) && $rs['TtlRecords'] > 0) {
            return $rs;
        } else {
            return false;
        }
    }

}

