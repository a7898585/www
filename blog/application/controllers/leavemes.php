<?php

/* * **********************
 * 功能：   博客个人文章
 * 
 * *********************** */

class Leavemes extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("blogmessage_socket");
        $this->load->model("user_socket");
    }

    /*
     * 留言
     */

    function message($domainname) {
        $this->_checkUserlogin();
        $IN = parse_incoming();
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);

        //用户所在组是否需要验证验证码
        $extract['checkvalidate'] = $this->_checkValidate($this->user['userid']);


        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], $extract['isowner']);
        $page = is_numeric($IN['p']) ? $IN['p'] : 1; //页码    	
        $param['UserID'] = $extract['bloginfo']['UserID'];
        $param['StartNo'] = -1;
        $messageList = $this->blogmessage_socket->leaveMessageList($param);

        $tmpCnt = $messageList['TtlRecords'];
        if ($page > ceil($tmpCnt / LEAVE_MESSAGE_PAGESIZE)) {
            $page = 1;
        }
        if ($tmpCnt) {
            $param['StartNo'] = ($page - 1) * LEAVE_MESSAGE_PAGESIZE; //代表到第几页
            $param['QryCount'] = LEAVE_MESSAGE_PAGESIZE;
            $param['flagCode'] = $messageList['flagCode'];

            $list = $this->blogmessage_socket->leaveMessageList($param);

            $baseLink = $this->config->item('base_url') . "/{$domainname}/leaveme/message";
            $this->load->library('pagebarsnew');
            $this->pagebarsnew->Page($tmpCnt, $page, LEAVE_MESSAGE_PAGESIZE, $baseLink, '/');
            $extract['pagebar'] = $this->pagebarsnew->upDownList();
        }
        $extract['domainname'] = $domainname;
        $extract['tmpCnt'] = $tmpCnt;
        $extract['StartNo'] = $param['StartNo'];
        $this->load->model('blogmessage_socket');
        $extract['list'] = $list['Record'];
        $userid = $this->user['userid'];
        $extract['userid'] = $extract['bloginfo']['UserID'];
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $extract['baseurl'] = &config_item('base_url');
        $blocks = &$this->config->item('block');
        $extract['peronalhead'] = $blocks['devpersonalhead'];
        $extract['page'] = $page;
        $this->load->view("manage/leaveComment.shtml", $extract);
    }

    /**
     * @ messageAction 消息操作 http://passport.cnfol.com/v7/assistant/messageaction
     *
     * @access public
     * @return void
     */
    function messageAction() {
        //	$this->userinteract->passportCheck();
        $this->_checkUserlogin();
        $this->load->model('blogmessage_socket');
        $IN = parse_incoming();
        $html = "";
        $tuserid = intval($IN['t_id']);
        $content = trim($IN['content']);
        $contenttext = trim($IN['contenttext']);

        $userid = $this->user['userid'];

        //判断是否在黑名单中
        if ($this->blackCheck($tuserid, $userid)) {
            echo json_encode(array('erron' => '01', 'error' => '博主已禁止你留言'));
            return;
        }
        //判断是否在黑名单中

        switch ($IN['act']) {
            case 'newsendmsg'://发送
                if ($userid == $tuserid) {
                    $code = 102;
                } else {
                    if (empty($content)) {
                        $code = 103;
                    } elseif (utf8_strlen($contenttext) > 600) {
                        //error_log('length'.utf8_strlen($content).'||'.$userid."||".date("Y-m-d H:i:s")."\r\n", 3, '/home/httpd/logs/content.log');
                        $code = 104;
                    } else {


                        if ($IN['checkvalidate'] == '0') {
                            $verifycode = $this->input->get_post('validate');
                            $this->load->library('SimpleCaptcha');

                            if (!$this->simplecaptcha->validate($verifycode, $this->user['userid'])) {
                                $code = '108';
                                break;
                            }
                        }

                        $param['FromUserID'] = $userid;
                        $param['ToUserID'] = $tuserid;

                        $param['Content'] = remove_invisible_code($content);


                        $param['Content'] = affichePicture(array('Content' => $param['Content']), 460, 400);
                        //error_log(print_r($content,true), 3, '/home/www/html/logs/a1.log');
                        //error_log(print_r($param['Content'],true), 3, '/home/httpd/logs/a1.log');


                        preg_match_all('/(<img\s*([^>]*)?src\s*\=\s*[\"|\']?\s*([^>\"\'\s]*)[^>]*>)/i', $param['Content'], $matchesEmbed);

                        if (count($matchesEmbed['0']) > 0) {
                            $y = count($matchesEmbed['0']);
                            for ($i = 0; $i < $y; $i++) {
                                preg_match('/(<img\s*([^>]*)?src\s*\=\s*[\"|\']?\s*([^>\"\'\s]*)[^>]*>)/i', $param['Content'], $matches);
                                $matches = htmlEncode($matches['1']);

                                $param['Content'] = preg_replace('/(<img\s*([^>]*)?src\s*\=\s*[\"|\']?\s*([^>\"\'\s]*)[^>]*>)/i', $matches, $param['Content'], 1);
                            }
                        }


                        preg_match_all('/(<blockquote\s*class\=[\"|\']kquote[\"|\']>)/i', $param['Content'], $matchesEmbed);

                        if (count($matchesEmbed['0']) > 0) {
                            $y = count($matchesEmbed['0']);
                            for ($i = 0; $i < $y; $i++) {
                                preg_match('/(<blockquote\s*class\=[\"|\']kquote[\"|\']>)/i', $param['Content'], $matches);
                                $matches = htmlEncode($matches['1']);
                                $param['Content'] = preg_replace('/(<blockquote\s*class\=[\"|\']kquote[\"|\']>)/i', $matches, $param['Content'], 1);
                            }
                        }


                        preg_match_all('/(<\/blockquote>)/i', $param['Content'], $matchesEmbed);

                        if (count($matchesEmbed['0']) > 0) {
                            $y = count($matchesEmbed['0']);
                            for ($i = 0; $i < $y; $i++) {
                                preg_match('/(<\/blockquote>)/i', $param['Content'], $matches);
                                $matches = htmlEncode($matches['1']);
                                $param['Content'] = preg_replace('/(<\/blockquote>)/i', $matches, $param['Content'], 1);
                            }
                        }


                        //error_log(print_r($param['Content'],true), 3, '/home/www/html/logs/a1.log');

                        $param['FromUserIP'] = $IN['ip'];
                        $rs = $this->blogmessage_socket->sendMessage($param);

                        if ($rs) {
                            $code = 100;
                        } else {
                            $code = 101;
                        }
                    }
                }
                break;
            case 'sendmsg'://回复
                if (empty($code)) {
                    $messageid = trim($IN['messageid']);
                    if ($tuserid) {
                        if ($tuserid != $userid) {
                            if (empty($content)) {
                                $code = 103;
                            } elseif (utf8_strlen($content) > 600) {
                                $code = 104;
                            } else {
                                $param = array(
                                    'FromUserID' => $userid,
                                    'ToUserID' => $tuserid,
                                    'Content' => $content,
                                    'ParentID' => $messageid,
                                    'FromUserIP' => $IN['ip']
                                );
                                $rs = $this->blogmessage_socket->sendMessage($param);
                                $html = <<<END
	          <div class="ReplyBox message_{$rs['MsgID']}" >
	            <div class="ReplyMesgBox">                  
	              <span class="Arrow"></span>
	              <div class="ReplyMesgInfo">
	                <!--操作-->
	                <div class="HandleBox"><a href="javascript:delMsg('{$rs['MsgID']}');" >删除</a></div>
	                <!--end 操作-->
	                <p class="ReplyTime"><em class="Name">我</em>回复时间<em class="Time">{$rs['AddTime']}</em></p>
	              </div>  
	              <div class="ReplyMesg">
	                <p>{$rs['Content']}</p>
	              </div>
	            </div>    
	          </div>                                 
END;
                                $code = 100;
                            }
                        } else {
                            $code = 102;
                        }
                    }
                }
                break;
            case 'delmsg':
                $id = intval($IN['msg_id']);

                if ($id) {
                    $param = array(
                        'MsgIDs' => $id,
                        'UserID' => $userid
                    );

                    $code = $this->blogmessage_socket->delMsg($param);
                }

                break;
        }

        switch ($code) {
            case 100:
                $info = '操作成功';
                break;
            case 101:
                $info = '登录后才能给对方留言';
                break;
            case 102:
                $info = '不能给自己发留言';
                break;
            case 103:
                $info = '发送内容不能为空';
                break;
            case 104:
                $info = '发送内容不能超过300个字';
                break;
            case 105:
            case 106:
                $info = '昵称为空或格式有问题';
                break;
            case 107:
                $info = '昵称不存在';
                break;
            case 108:
                $info = '验证码错误';
                break;
            default:
                $info = '参数传递错误';
                break;
        }

        echo json_encode(array('erron' => '01', 'error' => $info, 'html' => $html));
        exit;
    }

    /*
     * 留言列表
     */

    function messageList() {
        $IN = parse_incoming();
        $param['UserID'] = intval($IN['uid']);
        $param['StartNo'] = -1;
        $messageList = $this->blogmessage_socket->leaveMessageList($param);
        $tmpCnt = $messageList['TtlRecords'];
        $extract['tmpCnt'] = $tmpCnt;
        $page = intval($IN['page']);

        $param['StartNo'] = ($page - 1) * LEAVE_MESSAGE_PAGESIZE; //代表到第几页
        $param['QryCount'] = LEAVE_MESSAGE_PAGESIZE;
        $list = $this->blogmessage_socket->leaveMessageList($param);
        $extract['list'] = $list['Record'];
        $extract['baseurl'] = &config_item('base_url');
        if ($list) {
            $html = $this->load->view("ajax/messageList.shtml", $extract, true);
            echo json_encode(array('errno' => '01', 'error' => $html, 'count' => $tmpCnt));
        } else {
            echo json_encode(array('errno' => '02', 'error' => '数据请求失败'));
        }
    }

    //判断是否在黑名单中
    function blackCheck($tuserid, $userid) {
        $this->load->model('blackuser_socket');
        $data['UserID'] = $tuserid;
        $data['FType'] = 1;
        $data['StartNo'] = -1;
        $tempInfo = $this->blackuser_socket->getBlackUserListOther($data);

        //error_log(print_r($tempInfo,true), 3, '/home/www/html/logs/a12313.log');
        if ($tempInfo['Code'] == '00' && $tempInfo['TtlRecords'] > 0) {
            $data['FType'] = 1;
            $data['StartNo'] = 0;
            $data['QryCount'] = $tempInfo['TtlRecords'];
            $tempInfo = $this->blackuser_socket->getBlackUserListOther($data);
            //error_log(print_r($tempInfo,true), 3, '/home/www/html/logs/a12312.log');
            if ($tempInfo['Code'] == '00' && $tempInfo['TtlRecords'] > 0) {

                foreach ($tempInfo['Record'] as $key => $value) {
                    if (in_array($userid, $value)) {
                        return true;
                    }
                }
            }
            return false;
        }
        return false;
    }

}

?>