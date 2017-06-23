<?php

/* * **********************
 * 功能：   博客扩展功能
 * author： leicc
 * add：    2010-11-16
 * modify   2010-11-16
 * *********************** */

class Expantion extends MY_Controller {

    function Expantion() {
        parent::MY_Controller();
    }

    /**
     * @ 用户推荐文章 提交动作
     * */
    function ArticleRemmend($domainname) {
        if (!$this->_checkUserlogin()) {
            echo json_encode(array('error' => '请先登入再进行文章推荐', 'errno' => 'login'));
            exit;
        }
        $artstr = $this->input->post('articleid');

        if (strpos($artstr, '-') === false) {
            $appearTime = '2011-01-01 00:00:00';
            $articleID = $artstr;
        } else {
            $temp = explode('-', $artstr);
            $appearTime = date("Y-m-d H:i:s", $temp[0]);
            $articleID = $temp[1];
        }

        $Title = $this->input->post('title');
        $Author = trim($this->input->post('author'));
        $IsCheck = intval($this->input->post('check'));
        $bloginfo = $this->_getBlogInfoByDomain($domainname);
        $data['MemberID'] = $bloginfo['MemberID'];
        $data['ArticleID'] = $articleID;
        $data['IsCheck'] = 0;
        $data['AppearTime'] = $appearTime;
        $this->load->model('blogarticle_socket');
        $article = $this->blogarticle_socket->getBlogArticleByID($data);

        if (empty($article)) {
            echo json_encode(array('error' => '您推荐的文章信息不存在', 'errno' => 'not found'));
            exit;
        } else if (trim($article['Title']) != trim($Title)) {
            echo json_encode(array('error' => '您推荐的文章标题错误', 'errno' => 'title'));
            exit;
        } else if (trim($Author) == '') {
            echo json_encode(array('error' => '您推荐的作者信息为空', 'errno' => 'author'));
            exit;
        } else if ($IsCheck == 0) {
            echo json_encode(array('error' => '推荐文章信息验证合法', 'errno' => 'success'));
            exit;
        } else {
            $ActUrl = trim($this->input->post('arturl'));
            $param['ArticleID'] = $articleID;
            $param['Title'] = $Title;
            $param['URL'] = $ActUrl;
            $param['ArticleUserID'] = $bloginfo['UserID'];
            $param['RecommendUserID'] = $this->user['userid'];
            $param['IP'] = $this->input->ip_address();
            $param['ArticleAppearTime'] = $article['AppearTime'];

            if ($this->blogarticle_socket->RecmmondArticle($param)) {
                echo json_encode(array('error' => '推荐文章信息提交成功', 'errno' => 'success'));
                exit;
            } else {
                echo json_encode(array('error' => '推荐文章信息提交失败', 'errno' => 'failed'));
                exit;
            }
        }
    }

    /**
     * @ 获取自荐文章
     * */
    function getUserRecommendArts() {
        $this->load->model('channel_socket');
        $data['StartNo'] = -1;
        $tmpInfo = $this->channel_socket->getUserRecommendArticle($data);
        $nTolCnt = $tmpInfo['TtlRecords'];

        $pagelimit = 60; //最多翻页60
        $page = intval($this->input->get_post('page'));
        $page = ($page > $pagelimit || $page < 1) ? 1 : $page;
        $data['StartNo'] = channelarticlepagesize * ($page - 1);
        $data['QryCount'] = channelarticlepagesize;
        $data['FlagCode'] = $tmpInfo['FlagCode'];

        $extract['ArtList'] = $this->channel_socket->getUserRecommendArticle($data);
        $extract['ArtList'] = $extract['ArtList']['Record'];

        $extract['TagTitle'] = '用户推荐区';
        $extract['RssURL'] = config_item('base_url') . '/rss/recommendarticles.xml';

        //翻页信息
        $baseLink = config_item('base_url') . '/shtml/recommendarticles,';
        $this->load->helper('channal');

        $TolPage = ceil($nTolCnt / $data['QryCount']);
        $pagelimit = ($TolPage > $pagelimit) ? $pagelimit : $TolPage;

        $extract['pagebar'] = drawpagebar($data['QryCount'] * $pagelimit, $page, $data['QryCount'], $baseLink);
        $extract['channelTitle'] = $this->lang->language['title_recommendarticles'];
        $extract['channelKeywords'] = $this->lang->language['keywords_recommendarticles'];
        $extract['channelDescription'] = $this->lang->language['description_recommendarticles'];
        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['header'] = $blocks['channalhead'];
        $extract['footer'] = $blocks['channalfoot'];
        $extract['shtml'] = $this->config->item('shtml_path');
        $extract['baseurl'] = $this->config->item('base_url');
        $extract['TagID'] = 1;
        $this->load->view('channal/channal_recommendarticles.shtml', $extract);
    }

    //close popup
    function closepop($userid) {
        echo '<script>setTimeout("top.g_pop.close();",1000);</script>';
        exit;
    }

    /* function EditUpload()
      {
      $FileName = base64_decode($this->input->get_post('filename'));
      $t = $this->input->get_post('t');
      if(empty($t))
      {
      $FileName = '<img src="' . $FileName . '" border="0">';
      $str = '<script language="javascript">
      var oEditor = window.parent.InnerDialogLoaded() ;
      oEditor.FCK.InsertHtml(\''.$FileName.'\');';
      $str .= 'window.parent.Cancel();
      </script>';
      }
      else
      {

      $str = '<script language="javascript">
      window.parent.document.getElementById(\'logo\').value="'.$FileName.'";';

      $str .= 'var obj=window.parent.document.getElementById(\'imgLogo\');
      obj.style.display=\'\';
      obj.src="'.$FileName.'";';

      $str .= 'window.parent.g_pop.close() ;
      </script>';

      }
      echo $str;
      } */

    //编辑器附件上传
    function AttUpload($usrid) {

        if (isset($_FILES['imgFile'])) {
            $attachments = $_FILES['imgFile'];
            $data['filename'] = $attachments['name'];
        } else {
            echo json_encode(array('error' => 1, 'message' => '请选择要上传的文件！'));
            exit;
        }

        $data['remark'] = $this->input->post('remark');
        if (is_uploaded_file($attachments['tmp_name'])) {
            $this->load->model('user_socket');
            $this->load->model('attachment_socket');

            $params['UserID'] = $usrid;
            //获取文件名、后缀、大小
            $getFilename = getFilename($attachments['name']);
            $attachment_ext = strtolower($getFilename['extdname']);
            $fileSize = filesize($attachments['tmp_name']);

            //获取用户的附件配置
            $userInfo = $this->user_socket->getUserBaseInfo($params);
            $groupids = $userInfo['GroupIDs'];
            $arrGroupid = explode(',', $groupids);

            if (count($arrGroupid) >= 2) {
                $params['Type'] = 0;
                $params['QryData'] = $usrid;
                $config = $this->attachment_socket->getConfigList($params);
            } else {
                $params['Type'] = 1;
                $params['QryData'] = $groupids;
                $config = $this->attachment_socket->getConfigList($params);
            }

            if ($config === false) {
                echo json_encode(array('error' => 1, 'message' => '文件传输失败，请重新上传。'));
                exit;
            }

            $spaceSize = $config['SpaceSize'];
            $fileMaxSize = $config['AttachSize'];
            $config['AttachType'] = 'jpg,gif,jpeg';  //控制博客文章上传的类型
            $fileTypes = explode(',', strtolower($config['AttachType']));

            if (!in_array($attachment_ext, $fileTypes)) {
                echo json_encode(array('error' => 1, 'message' => '上传文件类型不属于：' . $config['AttachType']));
                exit;
            }

            if ($fileMaxSize < $fileSize) {
                //echo json_encode(array('error' => 1, 'message' => '上传文件大小超出：'.$fileMaxSize.','.$fileSize)); exit;
                echo json_encode(array('error' => 1, 'message' => '上传文件大小超出，请转换为小于' . ($fileMaxSize / 1024) . 'KB的图片，谢谢。'));
                exit;
            }

            $employSpace = $this->attachment_socket->getEmploySpace($params);

            if ($employSpace === false) {
                echo json_encode(array('error' => 1, 'message' => '所需验证信息不足，请联系管理员'));
                exit;
            }

            if ($employSpace + $fileSize > $spaceSize) {
                echo json_encode(array('error' => 1, 'message' => '您当前可以使用的空间已满：' . $spaceSize));
                exit;
            }

            if (!$data['filename']) {
                if (strlen($getFilename['filename']) > 100) {
                    $getFilename['filename'] = substr($getFilename['filename'], 0, 90);
                }
                $data['filename'] = $getFilename['filename'];
            }

            //获取上传路径
            $folder = '/images';

            //获取服务目录
            $path = getUploadPath($params['UserID'], $folder);
            $dir = config_item('attached');


            //获取唯一文件名
            //$newfilename    = getUploadFileName($attachment_ext);
            $newfilename = getImgName($attachments['name']);


            //上传到本地

            if (move_uploaded_file($attachments['tmp_name'], $dir . $newfilename)) {

                $imgtype = getimagesize($dir . $newfilename);
                if ($imgtype[0] > 600) {
                    $attcig['source_image'] = $dir . $newfilename;
                    $attcig['maintain_ratio'] = TRUE;
                    $attcig['width'] = 600;
                    $attcig['height'] = $imgtype[1] / ($imgtype[0] / 600);
                    $this->load->library('image_lib', $attcig);
                    $this->image_lib->resize();
                }


                //发送成功调用接口保存图片信息
                $params['SaveType'] = 1;
                $params['AttachmentSortID'] = 3;
                $params['Name'] = $data['filename'];
                $params['Size'] = $fileSize;
                $params['URL'] = $path . $newfilename;
                $params['Remark'] = $data['remark'];

                $result = $this->attachment_socket->addAttachment($params);

                if ($result) {
                    //同步到服务器

                    ftpArticleImg($params['URL'], $dir . $newfilename);
                    sleep(3);
                } else {
                    unlink($dir . $newfilename);
                }

                if ($result) {
                    echo json_encode(array('error' => 0, 'url' => config_item('img_base_url') . $params['URL']));
                    exit;
                    //echo json_encode(array('error'=>0, 'url'=>config_item('base_url').'/attached/'.$newfilename)); exit;
                } else {
                    echo json_encode(array('error' => 1, 'message' => '附件上传失败01'));
                    exit;
                }
            } else {
                error_log(print_r($attachments['tmp_name'], true) . '||' . $dir . $newfilename . '||', 3, '/home/www/html/logs/upload_tmp_name.log');
                echo json_encode(array('error' => 1, 'message' => '附件上传失败02'));
                exit;
            }
        }


        echo json_encode(array('error' => 1, 'message' => '您没有提交要上传的图片!'));
        exit;
    }

    function InsertImg($returnurl) {
        $FileName = base64_decode($returnurl);
        //echo json_encode(array('error' => 0, 'url' => $FileName)); exit;
        //self.exec('insertimage', url, title, width, height, 0, align).hideDialog().focus();
        $str = '<script language="javascript">';
        $str .= 'window.parent.editor.plugin[\'image\'].insertimage("' . $FileName . '")';
        $str .= 'window.parent.editor.hideDialog();';
        $str .= '</script>';
        echo $str;
    }

    function EditUpload() {
        $FileName = base64_decode($this->input->get_post('filename'));
        //$FileName2 = base64_decode($this->input->get_post('filename'));
        $t = $this->input->get_post('t');
        if (empty($t)) {
            //$FileName = '<img src="' . $FileName . '" border="0">';
            $str = '<script language="javascript">';
            //$str .= 'window.parent.KE.insertHtml(\'content\',\''.$FileName.'\');';
            $str .= 'window.parent.KE.plugin[\'image\'].insert(\'content\',\'' . $FileName . '\');';
            $str .= '</script>';
        } else {

            $str = '<script language="javascript">
            window.parent.document.getElementById(\'logo\').value="' . $FileName . '";';

            $str .= 'var obj=window.parent.document.getElementById(\'imgLogo\');
            obj.style.display=\'\';
            obj.src="' . $FileName . '";';

            $str .= 'window.parent.g_pop.close() ;
            </script>';
        }
        echo $str;
    }

}

//end class
?>