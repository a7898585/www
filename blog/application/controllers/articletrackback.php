<?php

/* * **********************
 * 功能：   博客引用通告
 * author： leicc
 * add：    2010-11-16
 * modify   2010-11-16
 * *********************** */

class ArticleTrackback extends MY_Controller {

    function ArticleTrackback() {
        parent::MY_Controller();
    }

    /**
     * @ 接收引用通告
     * */
    function TrackBackReceive() {
        $this->load->library('trackback');

        $articleID = intval($this->input->get_post('articleid'));
        $trackback = $this->input->get_post('trackback');
        if ($trackback !== Md5trackback($articleID)) {
            $this->trackback->send_error('文章ID信息不存在');
        }
        if (TRUE === $this->trackback->receive()) {
            $param['BlogType'] = 1;
            $param['TrackID'] = 0;
            $param['ArticleID'] = $articleID;
            $param['Title'] = $this->trackback->data('title');
            $param['Excerpt'] = $this->trackback->data('excerpt');
            $param['URL'] = $this->trackback->data('url');
            $param['BlogName'] = $this->trackback->data('blog_name');
            $param['IP'] = $this->input->ip_address();

            $this->load->model('articletractback_socket');
            if ($this->articletractback_socket->addTractBack($param)) {
                $this->trackback->send_success('文章应用通告信息添加成功');
            } else {
                $this->trackback->send_error('文章引用通告信息传递失败');
            }
        } else {
            $errstr = $this->trackback->display_errors();
            $this->trackback->send_error($errstr);
        }
    }

    /**
     * @ 获取文章引用公告的列表
     * */
    function TrackBackList() {
        $articleID = $this->input->get_post('artid');
        $flashCode = $this->input->get_post('co');

        //获取标志是否有权限修改
        $isowner = false;
        if (false !== $this->_checkUserlogin()) {
            if ($flashCode === getVerifyStr($this->user['userid'] . $articleID))
                $isowner = true;
        }

        $param['BlogType'] = 1;
        $param['ArticleID'] = $articleID;
        $param['StartNo'] = -1;
        $this->load->model('articletractback_socket');
        $tempInfo = $this->articletractback_socket->getTrackBackList($param);
        $tmpCnt = $tempInfo['TtlRecords'];

        if ($tmpCnt < 1) {
            echo '此文章还没有相关引用!';
            exit;
        }

        $pagesize = 2;
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        $page = ($page <= ceil($tmpCnt / $pagesize)) ? $page : 1;

        $param['StartNo'] = ($page - 1) * $pagesize;
        $param['QryCount'] = $pagesize;
        $param['FlagCode'] = $tempInfo['FlagCode'];
        $data = $this->articletractback_socket->getTrackBackList($param);
        $str = '';
        if (isset($data['RetRecords']) && $data['RetRecords'] > 0) {
            $data['Record'] = ($data['RetRecords'] == 1) ? array('0' => $data['Record']) : $data['Record'];
            //翻页函数
            $this->load->library('pagebarsnew');

            $this->pagebarsnew->Page($tmpCnt, $page, $pagesize, '', '');
            $pagebars = $this->pagebarsnew->upDownListAjax($articleID, 'UpdateTrackbackPage');
            foreach ($data['Record'] as $val) {
                $str .= "·<a href='" . $val['URL'] . "' target='_blank'>" . $val['Title'] . "</a>　(<a href='" . $val['URL'] . "' target='_blank'>" . $val['BlogName'] . "</a>)";
                if ($isowner != false) {  //博客文章属于当前登入用户
                    $str .= '  <a href=\'javascript:void(0)\' onclick="javascript:deltrack(\'' . $articleID . '\',\'' . $val['TrackID'] . '\')">删除</a>';
                }
                $str .= "<br>";
            }
            $str .= $pagebars;
        } else {
            $str = '已经翻到该引用文章的最后一页!';
        }
        echo $str;
    }

    //删除博客文章引用通告
    function DelTractBack() {
        $this->_checkLogin();
        $articleID = $this->input->get_post('artid');
        $flashCode = $this->input->get_post('co');
        //获取标志是否有权限修改
        if ($flashCode != getVerifyStr($this->user['userid'] . $articleID)) {
            $data['error'] = '您没有管理他人博客文章引用通告权限';
            $data['errno'] = '00001';
            echo json_encode($data);
            exit;
        }

        $trackbackid = $this->input->get_post('traid');
        $data['BlogType'] = 1;
        $data['TrackIDs'] = $trackbackid;
        $this->load->model('articletractback_socket');
        $data = $this->articletractback_socket->delTractBack($data);
        $error = ($data == true) ? array('error' => '引用公告删除成功', 'errno' => 'succ') : array('error' => '引用通告删除失败', 'errno' => 'failed');
        echo json_encode($error);
    }

}

//end class
?>