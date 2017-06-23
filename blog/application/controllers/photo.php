<?php

/* * **********************
 * 功能：   博客相册图片管理
 * author： leicc
 * add：    2010-11-16
 * modify   2010-11-16
 * *********************** */

class Photo extends MY_Controller {

    function Photo() {
        parent::MY_Controller();
        $this->pagesize = $this->config->item("showc");
    }

    /**
     * @ 编辑图片信息
     * */
    function EditPhoto($domainname) {
        $this->_checkLogin();
        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);

        $this->_checkUser($extract['bloginfo']['UserID']);
        $param['GroupIDs'] = trim($extract['bloginfo']['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);
        $this->_checkAccess($blogaccess, 'EditPhoto');

        $data['PhotoID'] = intval($this->input->get('photoid'));
        $data['AlbumID'] = intval($this->input->get('albumid'));
        $data['UserID'] = intval($this->user['userid']);
        $this->load->model('blogalbum_socket');
        $extract['photo'] = $this->blogalbum_socket->getPhotoById($data);
        $extract['baseurl'] = config_item('base_url');
        $this->load->view('album/photo_edit.shtml', $extract);
    }

    /**
     * 通过Ajax获取信息
     * return json
     * @ 图片的关联动作 增删改
     * */
    function Action($domainname) {
        $this->_checkLogin();
        $BlogInfo = $this->_getBlogInfoByDomain($domainname);
        $this->_checkUser($BlogInfo['UserID']);
        $act = $this->input->post('act');

        switch ($act) {
            case 'uploadphoto':
                //验证权限
                //$param['GroupIDs']     = trim($BlogInfo['GroupID'],',');
                //$blogaccess  = $this->memberblog_socket->getAccessList($param);
                //$this->_checkAccess($blogaccess,'AddPhoto'); 
                $filename = strip_tags($this->input->post('filename'));
                $albumid = intval($this->input->post('albumID'));
                $remark = strip_tags($this->input->post('remark'));

                $errstr = false; //标志是否有参数传递错误
                if (strlen($filename) < 2 || strlen($filename) > 200) {
                    $errstr = '图片名应该在2-200个字节之间';
                } else if (strlen($remark) > 200) {
                    $errstr = '备注信息应该在0-200字节之间';
                } else if (!isset($_FILES['uploadimage']) || empty($_FILES['uploadimage'])) {
                    $errstr = '请选择要上传的图片信息';
                } else if (!preg_match('/(\.jpg)|(\.jpeg)|(\.png)|(\.gif)$/i', $_FILES['uploadimage']['name'])) {
                    $errstr = '图片格式错误，请选择jpeg,jpg,gif,png格式上传';
                } else if (!preg_match('/image/i', $_FILES['uploadimage']['type'])) {
                    $errstr = '图片格式错误，请选择jpeg,jpg,gif,png格式上传';
                } else if (photosizelimite < $_FILES['uploadimage']['size']) {
                    $errstr = '图片大小超出限制，服务限制最多2M';
                } else if (!is_uploaded_file($_FILES['uploadimage']['tmp_name'])) {
                    $errstr = '操作非法，拒绝执行';
                } else {
                    $this->load->model('blogalbum_socket');
                    if ($albumid < 1) {
                        unset($data);
                        $data['RelationID'] = $BlogInfo['MemberID'];
                        $data['UserID'] = $this->user['userid'];
                        $data['Name'] = '默认相册';
                        $data['Summary'] = '系统默认相册不能更改，不能删除。';
                        $data['Property'] = 2;
                        $albumid = $this->blogalbum_socket->addAlbum($data);
                        if ($albumid < 1) {
                            echo "博客默认相册创建失败";
                            echo '<script>setTimeout("parent.location.href=\'' . $returnurl . '\';",2000);</script>';
                            exit;
                        }
                    }
                    $newfilename = getImgName($_FILES['uploadimage']['name']);
                    $tmpupload = config_item('tmpupload') . $newfilename;
                    if (move_uploaded_file($_FILES['uploadimage']['tmp_name'], $tmpupload)) {
                        //给相册添加图片getUploadPath
                        unset($param);
                        $param['Name'] = $filename;
                        $param['Size'] = $_FILES['uploadimage']['size'];
                        $param['URL'] = trim(getPhotoImg($this->user['userid'])) . trim($newfilename);
                        $param['UserID'] = $this->user['userid'];
                        $param['Remark'] = htmlEncode($remark);
                        $param['AlbumID'] = $albumid;
                        $param['RelationID'] = $BlogInfo['MemberID'];

                        $flag = $this->blogalbum_socket->addPhoto($param);
                        if ($flag) {
                            $dir = getUploadPath($this->user['userid']);
                            ftpAttachment($dir . $newfilename, $tmpupload); //同步上传
                            $errstr = '图片上传成功！';
                        } else {
                            $errstr = '图片上传失败！';
                        }
                    } else {
                        $errstr = '图片上传失败！-';
                    }
                }
                echo $errstr;
                echo "<script>setTimeout(function(){ parent.location.href=parent.location.href; },2000);</script>";
                exit;
                break;

            case 'delphoto':
                //验证权限
                $param['GroupIDs'] = trim($BlogInfo['GroupID'], ',');
                $blogaccess = $this->memberblog_socket->getAccessList($param);
                $this->_checkAccess($blogaccess, 'DelPhoto');
                unset($data);
                $data['PhotoID'] = intval($this->input->post('photoids'));
                $data['AlbumID'] = intval($this->input->post('albumid'));
                $data['RelationID'] = $BlogInfo['MemberID'];
                $data['memberid'] = intval($this->input->post('memberid'));
                $flashCode = $this->input->post('vCode');
                if (getVerifyStr($data['AlbumID'] . $BlogInfo['UserID']) != $flashCode) {
                    echo '验证信息传递失败';
                    exit;
                } else if ($data['PhotoID'] < 1 || $data['AlbumID'] < 1) {
                    echo '图片信息交互传递失败';
                    exit;
                } else {
                    $this->load->model('blogalbum_socket');
                    $data['UserID'] = intval($this->user['userid']);
                    $photo = $this->blogalbum_socket->getPhotoById($data);

                    if (empty($photo)) {
                        echo '您要删除的图片信息不存在';
                        exit;
                    }
                    $url = parse_url($photo['URL']);

                    $destination_file = config_item('uploadbasepath') . $url['path']; //只需相对路劲即可

                    $flag = $this->blogalbum_socket->delPhoto($data);
                    if ($flag) {
                        echo 'succ';
                    } else {
                        echo 'error';
                    }
                }
                exit;
                break;
            case 'editphoto':
                //验证权限
                $param['GroupIDs'] = trim($BlogInfo['GroupID'], ',');
                $blogaccess = $this->memberblog_socket->getAccessList($param);
                $this->_checkAccess($blogaccess, 'EditPhoto');

                unset($data);
                $data['PhotoID'] = intval($this->input->post('photoid'));
                $data['AlbumID'] = intval($this->input->post('albumid'));
                $data['Name'] = $this->input->post('filename');
                $data['UserID'] = $this->user['userid'];
                $data['Remark'] = $this->input->post('remark');
                $data['RelationID'] = $BlogInfo['MemberID'];
                $flashCode = $this->input->post('flashCode');
                $errstr = false; //标志是否有参数传递错误
                if (strlen($data['Name']) < 2 || strlen($data['Name']) > 200) {
                    $errstr = '图片名应该在2-200个字节之间';
                } else if (strlen($data['Remark']) > 200) {
                    $errstr = '备注信息应该在0-200字节之间';
                } else if (getVerifyStr($data['AlbumID'] . $data['PhotoID']) != $flashCode) {
                    $errstr = '验证信息传递非法';
                } else {
                    $this->load->model('blogalbum_socket');
                    $flag = $this->blogalbum_socket->addPhoto($data);
                    if ($flag) {
                        $errstr = '图片编辑提交成功！';
                    } else {
                        $errstr = '图片编辑提交失败！';
                    }
                }
                echo $errstr;
                echo "<script>setTimeout(function(){ parent.location.href=parent.location.href; },2000);</script>";
                exit;
                break;
            default:
                break;
        }
    }

    //博客相册图片列表
    function getPhotolist($domainname) {
        $this->_checkUserlogin();
        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);
        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], $extract['isowner']);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        /* --------博客信息--------------- */
        $extract['userinfo'] = $this->_getUserInfoByUid($extract['bloginfo']['UserID']);
        /* --------统计各文章或各博客主页被访问次数--------------- */
        //$this->_hotBlogArticle(array('domainname' => $domainname, 'appearTime' => '', 'articleID' => '', 'guestType' => $this->user['userid']), $extract['bloginfo']['UserID']);
        /* ------------------start粉丝，我关注的，相互关注数  ----------------------------------- */
        $extract['friendsnumber'] = $this->_getFriend($extract['bloginfo']['UserID']);
        $extract['isFrends'] = $this->_isFriend($this->user['userid'], $extract['bloginfo']['UserID']);
        /* ------------------end粉丝，我关注的，相互关注数  ------------------------------------- */

        /* ----------start博主的文章数量----------------------------------- */
        $data['MemberIDs'] = $extract['bloginfo']['MemberID'];
        $stat1 = $this->memberblog_socket->getMemberBlogStat($data);
        $extract['TotalArticle'] = $stat1['TotalArticle'];
        /* ------------end博主的文章数量------------------------------------ */
        /* 实名认真 -start */
        $params['UserID'] = $extract['bloginfo']['UserID'];
        $extract['auth'] = $this->user_socket->realNameAuth($params);
        /** 实名认真 -end */
        /* -------end-博客信息--------------- */

        $albumID = intval($this->input->get('albumid'));
        $data['AlbumID'] = $albumID;
        $data['RelationID'] = $extract['bloginfo']['MemberID'];
        $data['UserID'] = $extract['bloginfo']['UserID'];
        $data['Property'] = ($extract['isowner']) ? -1 : 0;
        $data['StartNo'] = 0;
        $data['QryCount'] = 1;
        $this->load->model('blogalbum_socket');
        $extract['albuminfo'] = $this->blogalbum_socket->getAlubmInfoById($data);

        if (empty($extract['albuminfo'])) {

            $extracts['tip'] = '您还没有访问此相册的权限';
            $extracts['url'] = config_item('base_url') . '/' . $domainname . '/albumlist';

            $this->load->view('article/article_no.shtml', $extracts);
            return;
        }
        //获取相册图片信息
        unset($data);
        $data['AlbumID'] = $albumID;
        $data['StartNo'] = 0;
        $data['QryCount'] = photolistpagesize;
        $data['RelationID'] = $extract['bloginfo']['MemberID'];
        $extract['photolist'] = $this->blogalbum_socket->getAlbumPhoteList($data);


        $extract['user'] = $this->user;
        $extract['userid'] = $extract['user']['userid'];
        $blocks = &$this->config->item('block');
        $extract['blocks'] = $blocks;
        $extract['block'] = $blocks['articlelist'];
        $extract['default'] = config_item('default');
        $extract['title'] = $extract['bloginfo']['BlogName'] . '相册列表_' . $extract['bloginfo']['NickName'];
        $extract['devmyblogloginheader'] = $blocks['devmyblogloginheader'];
        $extract['devmyblogcommonright'] = $blocks['devmyblogcommonright'];
        $extract['peronalfoot'] = $blocks['devmyblogcommonfooter'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 3;
        /* --------右边公共栏加载--------------- */
        $extract['modulepath'] = &config_item('module_path');
        $extract['show_renewvisitor'] = $blocks['show_renewvisitor'];
        $extract['cuttrent_domainname'] = $domainname;
        $extract['pagesize'] = $this->pagesize;
        /* ------end--右边公共栏加载--------------- */

        $extract['tmp_jointly'] = $blocks['jointly'];
        $extract['navConfig'] = 'album';
        $extract['logintool'] = $blocks['logintool'];
        $this->load->view('album/devalubm_photolist.shtml', $extract);
    }

    //相册图片评论
    function CommentAction($domainname) {
        $this->_checkUserlogin();
        $BlogInfo = $this->_getBlogInfoByDomain($domainname);
        $IsOwner = $this->_checkOwnUser($BlogInfo['UserID']);
        $act = $this->input->post('act');

        switch ($act) {
            case 'addcomment':
                $verifycode = $this->input->post('verifycode');
                $this->load->library('SimpleCaptcha');
                if (!$this->simplecaptcha->validate($verifycode, $this->user['userid'])) {
                    echo json_encode(array('error' => '验证码信息错误', 'errno' => 'validate'));
                    exit;
                }
                //判断时间限制
                if (1 == checkPublic()) {
                    echo json_encode(array('error' => '两次评论时间间隔小于15秒，请不要灌水', 'errno' => 'limit'));
                    exit;
                }
                $flashCode = $this->input->post('vCode');
                $data['AlbumID'] = intval($this->input->post('albumid'));
                if ($flashCode != getVerifyStr($data['AlbumID'] . $BlogInfo['UserID'])) {
                    echo json_encode(array('error' => '传递参数信息非法', 'errno' => 'verify'));
                    exit;
                }
                $data['Content'] = $this->input->post('ccontent', TRUE);
                $data['Content'] = '<![CDATA[' . $data['Content'] . ']]>';
//               $data['Content'] = htmlspecialchars($data['Content']);
                if (strlen($data['Content']) < 1 || strlen($data['Content']) > 3000) {
                    echo json_encode(array('error' => '评论内容应该在1-3000字节以内', 'errno' => 'lenlimit'));
                    exit;
                }
                $data['PhotoID'] = intval($this->input->post('photoid'));
                if ($data['AlbumID'] < 1 || $data['PhotoID'] < 1) {
                    echo json_encode(array('error' => '请选择要评论的图片', 'errno' => 'lenlimit'));
                    exit;
                }
                $data['CommentID'] = intval($this->input->post('commentid'));
                $data['IP'] = $this->input->ip_address();
                $Isanoyes = intval($this->input->post('anonymous'));
                if ($Isanoyes == 0) {
                    $data['Address'] = $this->input->post('udomainname');
                    $data['Address'] = (preg_match('/[0-9a-z\_]{3,}/i', $data['Address'])) ? $data['Address'] : '';
                    $data['UserID'] = $this->user['userid'];
                    $data['NickName'] = $this->user['nickname'];
                } else {
                    $data['Address'] = '';
                    $data['UserID'] = 0;
                    $data['NickName'] = '中金在线网友';
                }
                $data['IsChecked'] = 1; //默认审核通过

                $this->load->model('blogalbum_socket');
                if ($this->blogalbum_socket->addPhotoComment($data)) {
                    setPublic(); //设置时间限制
                    echo json_encode(array('error' => '相册图片评论成功', 'errno' => 'succ'));
                    exit;
                } else {
                    echo json_encode(array('error' => '相册图片评论失败', 'errno' => 'failed'));
                    exit;
                }
                break;
            case 'delcomment':
                if (!$IsOwner) {
                    echo json_encode(array('error' => '您没有删除相册评论的权限', 'errno' => 'reload'));
                    exit;
                }
                $data['CommentIDs'] = trim($this->input->post('commid'), ',');

                if (!preg_match('/[0-9]+([0-9,]*)/', $data['CommentIDs'])) {
                    echo json_encode(array('error' => '评论信息传递不合法', 'errno' => 'cid'));
                    exit;
                }
                $tmpcids = explode(',', $data['CommentIDs']);
                $tempids = is_array($tmpcids) ? $tmpcids : array($tmpcids);
                $cids = array();
                foreach ($tempids as $cid) {
                    $cids[] = intval($cid);
                }
                if (count($cids) > 25) {
                    echo json_encode(array('error' => '评论ID信息提交操作限制', 'errno' => 'cidlimit'));
                    exit;
                }
                $data['CommentIDs'] = array_unique($cids);
                $data['CommentIDs'] = join(',', $data['CommentIDs']);
                $data['AlbumID'] = intval($this->input->post('albumid'));
                $data['PhotoID'] = intval($this->input->post('photoid'));
                $flashCode = $this->input->post('vCode');
                if ($flashCode != getVerifyStr($data['AlbumID'] . $this->user['userid'])) {
                    echo json_encode(array('error' => '验证信息交互传递丢失', 'errno' => 'verify'));
                    exit;
                } else if ($data['AlbumID'] < 1 || $data['PhotoID'] < 1) {
                    echo json_encode(array('error' => '相册信息传递丢失', 'errno' => 'info'));
                    exit;
                }
                $this->load->model('blogalbum_socket');
                if ($this->blogalbum_socket->delPhotoComment($data)) {
                    echo json_encode(array('error' => '评论删除成功', 'errno' => 'succ'));
                    exit;
                } else {
                    echo json_encode(array('error' => '评论删除失败', 'errno' => 'failed'));
                    exit;
                }
                break;
            default:
                break;
        }
    }

    //获取相册评论列表
    function CommentList($domainname) {
        $this->_checkUserlogin();
        //通过博客名获取博客信息	
        $bloginfo = $this->_getBlogInfoByDomain($domainname);
        $isowner = $this->_checkOwnUser($bloginfo['UserID']);
        $data['PhotoID'] = intval($this->input->post('photoid'));
        $data['AlbumID'] = intval($this->input->post('albumID'));
        $page = intval($this->input->post('Page'));
        $pagesize = intval($this->input->post('PageSize'));
        $flagshCode = $this->input->post('vCode');
        $page = ($page > 1) ? $page : 1;
        $return = array();
        $return['errno'] = 'succ';

        if ($flagshCode != getVerifyStr($data['AlbumID'] . $bloginfo['UserID'])) {
            echo json_encode(array('errno' => 'verify', 'Record' => '<div class="PhotoComt Cf wangyoup2"><label class="wangyoup11">数据交互验证信息错误失败</label></div>'));
            exit;
        } else if ($data['PhotoID'] < 1 || $data['AlbumID'] < 1) {
            echo json_encode(array('errno' => 'verify', 'Record' => '<div class="PhotoComt Cf wangyoup2"><label class="wangyoup11">图片信息传递丢失</label></div>'));
            exit;
        }
        $data['StartNo'] = -1;
        $this->load->model('blogalbum_socket');
        $tmpInfo = $this->blogalbum_socket->getPhotoComment($data);
        $return['TolCnt'] = $tmpInfo['TtlRecords'];

//        $page = ($page > 1) ? $page : ($return['TolCnt'] % $pagesize) ? (floor($return['TolCnt'] / $pagesize) + 1) : floor($return['TolCnt'] / $pagesize);
        $return['nowpage'] = ($return['TolCnt'] % $pagesize) ? (floor($return['TolCnt'] / $pagesize) + 1) : floor($return['TolCnt'] / $pagesize);
        $return['isaddpage'] = (($return['TolCnt'] % $pagesize) && $return['TolCnt'] > 0) ? '' : 'toadd';
        $data['StartNo'] = ($page - 1) * $pagesize;
        if ($data['StartNo'] > $return['TolCnt']) {
            $return['errno'] = 'nodata';
            $return['Record'] = print_r($data) . '<div class="PhotoComt Cf wangyoup2"><label class="wangyoup11">暂无评论记录</label></div>';
        } else {
            $data['QryCount'] = $pagesize;
            $data['FlagCode'] = $tmpInfo['FlagCode'];
            $Record = $this->blogalbum_socket->getPhotoComment($data);

            if (is_array($Record['Record']) && !empty($Record['Record'])) {
                $return['Record'] = '';
                foreach ($Record['Record'] as $key => $vc) {
                    if (isset($vc['DataTime'])) {
                        $vc['DataTime'] = date('Y-m-d H:i', strtotime($vc['DataTime']));
                    } else {
                        $vc['DataTime'] = date('Y-m-d H:i');
                    }
                    $return['Record'] .= '<div class="PhotoComt Cf wangyoup2"><label for="" class="wangyoup11">';
                    if ($isowner) {
                        $return['Record'] .= '<input type="checkbox" value="' . $vc['CommentID'] . '" id="DelId[]" name="DelId[]">';
                    }
                    $return['Record'] .= '<div class="PicBox">';
                    $return['Record'] .= '<img title="点击查看个人名片" onclick="showuserinfo(' . $vc['UserID'] . ',this,event)" onload="javascript:this.style.display=\'\';" style="cursor: pointer;width:48px;height:48px;" onerror="this.onerror=\'\';this.src=\'http://head.cnfolimg.com/man.png\';" src="' . getUserHead($vc['UserID']) . '">';
                    if ($isowner) {
                        $return['Record'] .= '<a href="javascript:;" onclick="DelSinglePhotoComent(' . $vc['CommentID'] . ')">删除</a>';
                    }
                    $return['Record'] .= '</div>';
                    $return['Record'] .= '<div class="ComtBox">
                        <p class="Nick"><a target="_blank" href="' . config_item('base_url') . '/' . $vc['Address'] . '">' . $vc['NickName'] . '</a></p>
                        <p>' . FilterJs($vc['Content']) . '</p>
                    </div>';
                    $return['Record'] .= '</label></div>';
                }
                $return['Record'] .= '<p>';
                if ($isowner) {
                    $return['Record'] .= '<div class="ComtDel"><label for=""><input onclick="javascript:SelectAll(\'formc\')" type="checkbox">全选/反选</label><a href="javascript:DelPhotoCommentlist();">删除所选</a></div>';
                }
                $return['Record'] .= '</p>';
                $this->load->library('pagebarsnew');
                $this->pagebarsnew->Page($return['TolCnt'], $page, $pagesize, '', '');
                $pagebars = $this->pagebarsnew->upDownListAjax('#PhotoCommentList', 'setCommentPage');
                $return['Record'] .= '<div class="Page">' . $pagebars . '</div>';
            } else {
                $return['errno'] = 'nodata';
                $return['Record'] = '<div class="PhotoComt Cf wangyoup2"><label class="wangyoup11">暂无评论记录</label></div>';
            }
        }
        echo json_encode($return);
    }

    function CommentList_old($domainname) {
        $this->_checkUserlogin();
        //通过博客名获取博客信息	
        $bloginfo = $this->_getBlogInfoByDomain($domainname);
        $isowner = $this->_checkOwnUser($bloginfo['UserID']);
        $data['PhotoID'] = intval($this->input->post('photoid'));
        $data['AlbumID'] = intval($this->input->post('albumID'));
        $page = intval($this->input->post('Page'));
        $pagesize = intval($this->input->post('PageSize'));
        $flagshCode = $this->input->post('vCode');

        $pagesize = ($pagesize > 50 || $pagesize < 10) ? 10 : $pagesize;
        $page = ($page > 1) ? $page : 1;
        $return = array();
        $return['errno'] = 'succ';

        if ($flagshCode != getVerifyStr($data['AlbumID'] . $bloginfo['UserID'])) {
            echo json_encode(array('errno' => 'verify', 'Record' => '数据交互验证信息错误失败'));
            exit;
        } else if ($data['PhotoID'] < 1 || $data['AlbumID'] < 1) {
            echo json_encode(array('errno' => 'verify', 'Record' => '图片信息传递丢失'));
            exit;
        }
        $data['StartNo'] = -1;
        $this->load->model('blogalbum_socket');
        $tmpInfo = $this->blogalbum_socket->getPhotoComment($data);
        $return['TolCnt'] = $tmpInfo['TtlRecords'];

        $data['StartNo'] = ($page - 1) * $pagesize;
        if ($data['StartNo'] > $return['TolCnt']) {
            $return['errno'] = 'nodata';
            $return['Record'] = "暂无评论记录";
        } else {
            $data['QryCount'] = $pagesize;
            $data['FlagCode'] = $tmpInfo['FlagCode'];
            $Record = $this->blogalbum_socket->getPhotoComment($data);

            if (is_array($Record['Record']) && !empty($Record['Record'])) {
                $return['Record'] = '';
                foreach ($Record['Record'] as $key => $vc) {
                    if (isset($vc['DataTime'])) {
                        $vc['DataTime'] = date('Y-m-d H:i', strtotime($vc['DataTime']));
                    } else {
                        $vc['DataTime'] = date('Y-m-d H:i');
                    }
                    $return['Record'] .= '<div class="wangyoup2"><div style="word-wrap: break-word;" class="wangyoup11">' . FilterJs($vc['Content']) . '</div><div class="wangyoup12"><div class="wangyoup5 wangyoup14"><img height="60" border="0" width="60" title="点击查看个人名片" onclick="showuserinfo(' . $vc['UserID'] . ',this,event)" onload="javascript:this.style.display=\'\';" style="cursor: pointer;" onerror="this.onerror=\'\';this.src=\'http://head.cnfolimg.com/man.png\';" src="' . getUserHead($vc['UserID']) . '"></div><div class="wangyoup13">发布者:<a target="_blank" href="' . config_item('base_url') . '/' . $vc['Address'] . '">' . $vc['NickName'] . '</a>(<a target="_blank" href="' . config_item('base_url') . '/' . $vc['Address'] . '">' . config_item('base_url') . '/' . $vc['Address'] . '</a>)<br>' . $vc['DataTime'];

                    if ($isowner) {
                        $return['Record'] .= '<span style="cursor: pointer;" onclick="DelSinglePhotoComent(' . $vc['CommentID'] . ')">删除</span><input type="checkbox" value="' . $vc['CommentID'] . '" id="DelId[]" name="DelId[]">';
                    }
                    $return['Record'] .= '</div></div></div><div class="wzjtym07"></div>';
                }
                $return['Record'] .= '<div class="wzlbsz11">';
                if ($isowner) {
                    $return['Record'] .= '<span onclick="javascript:SelectAll(\'formc\')" style="cursor: pointer;">全选/反选</span><input type="button" onclick="DelPhotoCommentlist();" value="删除所选" class="but_1" name="Submit">';
                }

                $this->load->library('pagebarsnew');
                $this->pagebarsnew->Page($return['TolCnt'], $page, $pagesize, '', '');
                $pagebars = $this->pagebarsnew->upDownListAjax('#PhotoCommentList', 'setCommentPage');
                $return['Record'] .= $pagebars . '</div>';
            } else {
                $return['errno'] = 'nodata';
                $return['Record'] = "暂无评论记录";
            }
        }
        echo json_encode($return);
    }

}

//end class
?>