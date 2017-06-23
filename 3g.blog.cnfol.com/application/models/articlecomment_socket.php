<?php

/*
  --|博客评论的相关动作
  --|modify  2011-8-23  lifeng
 */

class Articlecomment_socket extends MY_Model {

    function Articlecomment_socket() {
        parent::MY_Model();
    }

    /*
      --|获取文章评论列表
     */

    function getArtCommentList($data) {
        $type = 'B301';
        $data['BlogType'] = isset($data['BlogType']) ? $data['BlogType'] : 1;
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;
        $data['OrderBy'] = 0;
        $data['Status'] = 1;
        $data['Function'] = 'getArtCommentList';

        if (ISCACHE) {
            if ($data['StartNo'] == -1) {
                $ckey = config_item('K1027');
                $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
                $rs = $this->cache->get($ckey);

                if (!$rs) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type, $data)) {
                        $rs['FlagCode'] = md5($rs['TtlRecords'] . time());
                        $this->cache->set($ckey, $rs, EXPIRETIME_2);
                        //error_log($ckey, 3, '/home/www/html/logs/BlogArticle_ckey'.date("Y-m-d H").'.log');
                        return $rs;
                    }
                    return false;
                }
                return $rs;
            } else {
                $ckey = config_item('K1028');
                $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
                $ckey = str_replace('{PageNo}', ($data['StartNo'] / $data['QryCount'] + 1), $ckey);
                $ckey = str_replace('{PageSize}', ($data['QryCount']), $ckey);

                $rs = $this->cache->get($ckey);

                if (!$rs || ($data['FlagCode'] != $rs['FlagCode'])) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type, $data)) {
                        $getData = array();
                        $getData['RetRecords'] = 0;
                        if ($rs['RetRecords'] > 0) {
                            $getData['Record'] = $rs['Record'];
                            $getData['RetRecords'] = $rs['RetRecords'];
                        }
                        $getData['FlagCode'] = $data['FlagCode'];
                        $this->cache->set($ckey, $getData, EXPIRETIME_2);

                        unset($rs);
                        return $getData;
                    }
                    return false;
                }
                return $rs;
            }
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);
            if ($this->_checkrs($rs, $type)) {
                if ($data['StartNo'] == -1) {
                    return $rs['TtlRecords'];
                }
                $getData['RetRecords'] = 0;
                if ($rs['RetRecords'] > 0) {
                    $getData['Record'] = $rs['Record'];
                    $getData['RetRecords'] = $rs['RetRecords'];
                }
                unset($rs);
                return $getData;
            } else {
                return false;
            }
        }
    }

    //获取文章评论的主人回复列表  //ParentCommentID
    function getArtParentCommentList($data) {

        $type = 'B305';
        $data['BlogType'] = isset($data['BlogType']) ? $data['BlogType'] : 1;
        //$data['OrderBy']    = isset($data['OrderBy'])? $data['OrderBy']:1;
        $data['OrderBy'] = 1;  //时间最近的在最后
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;

        if (ISCACHE) {
            if ($data['StartNo'] == -1) {
                $ckey = config_item('K1030');
                $ckey = str_replace('{ParentCommentID}', $data['ParentCommentID'], $ckey);
                $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
                $rs = $this->cache->get($ckey);

                if (!$rs) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type)) {
                        $rs['FlagCode'] = md5($rs['TtlRecords'] . time());
                        $this->cache->set($ckey, $rs, EXPIRETIME_2);
                        return $rs;
                    } else {
                        return false;
                    }
                }
                return $rs;
            } else {
                $ckey = config_item('K1031');
                $ckey = str_replace('{ParentCommentID}', $data['ParentCommentID'], $ckey);
                $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
                $rs = $this->cache->get($ckey);

                if (!$rs || ($data['FlagCode'] != $rs['FlagCode'])) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);

                    if ($this->_checkrs($rs, $type)) {
                        $getData = array();
                        $getData['RetRecords'] = 0;
                        if ($rs['RetRecords'] > 0) {
                            $getData['Record'] = $rs['Record'];
                            $getData['RetRecords'] = $rs['RetRecords'];
                        }
                        $getData['FlagCode'] = $data['FlagCode'];
                        $this->cache->set($ckey, $getData, EXPIRETIME_2);
                        unset($rs);
                        return $getData;
                    } else {
                        return false;
                    }
                }
                return $rs;
            }
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);
            if ($this->_checkrs($rs, $type)) {
                if ($data['StartNo'] == -1) {
                    return $rs['TtlRecords'];
                }
                $getData = array();
                $getData['RetRecords'] = 0;
                if ($rs['RetRecords'] > 0) {
                    $getData['Record'] = $rs['Record'];
                    $getData['RetRecords'] = $rs['RetRecords'];
                }
            } else {
                return false;
            }
            unset($rs);
            return $getData;
        }
    }

    //添加评论
    function aupdComment($data) {
        $type = 'B306';
        
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);
        if ($this->_checkrs($rs, $type)) {
            if (ISCACHE) {
                if (isset($data['ArticleIDs'])) {
                    $ArticleIDs = explode(',', $data['ArticleIDs']);
                    foreach ($ArticleIDs as $ArticleID) {
                        if (isset($data['ParentCommentID']) && $data['ParentCommentID'] > 0) {
                            $ckey = config_item('K1030');
                            $ckey = str_replace('{ParentCommentID}', $data['ParentCommentID'], $ckey);
                            $ckey = str_replace('{ArticleID}', $ArticleID, $ckey);
                            $this->cache->delete($ckey);
                        }

                        $ckey = config_item('K1027');
                        $ckey = str_replace('{ArticleID}', $ArticleID, $ckey);
                        $this->cache->delete($ckey);
                    }
                } else {
                    if (isset($data['ParentCommentID']) && $data['ParentCommentID'] > 0) {
                        $ckey = config_item('K1030');
                        $ckey = str_replace('{ParentCommentID}', $data['ParentCommentID'], $ckey);
                        $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
                        $this->cache->delete($ckey);
                    }
                    $ckey = config_item('K1027');
                    $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
                    $this->cache->delete($ckey);

                    $ckey = config_item('K2015'); //个人博客页面最新评论用
                    $ckey = str_replace('{UserIDs}', $data['articleUserid'], $ckey);
                    $this->cache->delete($ckey);

                    $ckey = config_item('K2016'); //个人博客页面最新评论用
                    $ckey = str_replace('{UserIDs}', $data['articleUserid'], $ckey);
                    $this->cache->delete($ckey);
                    
                    $ckey = config_item('K1014');
            		$ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
            		$ckey = str_replace('{MemberID}', $data['ArtMemberID'], $ckey);
            		$this->cache->delete($ckey);
                }
            }
        }
        //this
        return $rs;
        //this
    }

    //删除文章评论
    function delComment($data) {

        $type = 'B307';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);


        if ($this->_checkrs($rs, $type)) {
            if (ISCACHE) {
                $ckeyComment = config_item('K1061');
                $ckeyComment = str_replace('{MemberID}', $data['ArtMemberIDs'], $ckeyComment);
                $this->cache->delete($ckeyComment);
                if (isset($data['ArticleIDs'])) {
                    $ArticleIDs = explode(',', $data['ArticleIDs']);
                    foreach ($ArticleIDs as $ArticleID) {
                        if (isset($data['ParentCommentID']) && $data['ParentCommentID'] > 0) {
                            $ckey = config_item('K1030');
                            $ckey = str_replace('{ParentCommentID}', $data['ParentCommentID'], $ckey);
                            $ckey = str_replace('{ArticleID}', $ArticleID, $ckey);
                            $this->cache->delete($ckey);
                        }

                        $ckey = config_item('K1027');
                        $ckey = str_replace('{ArticleID}', $ArticleID, $ckey);
                        $this->cache->delete($ckey);


                        $ckey = config_item('K2015'); //个人博客页面最新评论用
                        $ckey = str_replace('{UserIDs}', $data['articleUserid'], $ckey);
                        $this->cache->delete($ckey);

                        $ckey = config_item('K2016'); //个人博客页面最新评论用
                        $ckey = str_replace('{UserIDs}', $data['articleUserid'], $ckey);
                        $this->cache->delete($ckey);
                        
                        $ckey = config_item('K1014');
            			$ckey = str_replace('{ArticleID}', $ArticleID, $ckey);
            			$ckey = str_replace('{MemberID}', $data['ArtMemberIDs'], $ckey);
            			$this->cache->delete($ckey);
                    }
                } else {

                    if (isset($data['ParentCommentID']) && $data['ParentCommentID'] > 0) {
                        $ckey = config_item('K1030');
                        $ckey = str_replace('{ParentCommentID}', $data['ParentCommentID'], $ckey);
                        $ckey = str_replace('{ArticleID}', $ArticleID, $ckey);
                        $this->cache->delete($ckey);
                    }
                    $ckey = config_item('K1027');
                    $ckey = str_replace('{ArticleID}', $ArticleID, $ckey);
                    $this->cache->delete($ckey);
                }
            }
            return true;
        }
        return false;
    }

    //从后台点击某评论跳转到文章页时，当前的评论id
    function getPageNoByCommentID($ArticleID, $CommentID, $CommentNumber) {
        $type = 'B301';
        $data['BlogType'] = 1;
        //$data['OrderBy']    = isset($data['OrderBy'])? $data['OrderBy']:1; 
        $data['StartNo'] = 0;
        $data['OrderBy'] = 0;
        $data['ArticleID'] = $ArticleID;

        $getData = array();

        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type)) {
            $getData['RetRecords'] = 0;
            if ($rs['RetRecords'] > 0) {
                $getData['Record'] = $rs['Record'];
                $getData['RetRecords'] = $rs['RetRecords'];
            }
        }

        unset($rs);

        if ($getData['RetRecords'] > 0) {
            $k = 1;
            foreach ($getData['Record'] as $item) {
                if ($item['CommentID'] == $CommentID) {
                    return intval($k / $CommentNumber) + 1;
                }
                $k++;
            }
        }
        return 0;
    }

    //获取最新评论
    function getNewestCommentList($data) {

        $type = 'B303';
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;

        if (ISCACHE) {
            if ($data['StartNo'] == -1) {
                $ckey = config_item('K2015');
                $ckey = str_replace('{UserIDs}', $data['ArticleUserID'], $ckey);

                $rs = $this->cache->get($ckey);

                if (!$rs) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type)) {
                        $this->cache->set($ckey, $rs['TtlRecords'], EXPIRETIME_1);
                        return $rs['TtlRecords'];
                    } else {
                        return false;
                    }
                }
                return $rs;
            } else {
                $ckey = config_item('K2016');
                $ckey = str_replace('{UserIDs}', $data['ArticleUserID'], $ckey);

                $rs = $this->cache->get($ckey);

                if (!$rs) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);

                    if ($this->_checkrs($rs, $type)) {
                        $getData = array();
                        if ($rs['RetRecords'] > 0) {
                            $getData['Record'] = $rs['Record'];
                            $getData['RetRecords'] = $rs['RetRecords'];

                            if ($rs['RetRecords'] == 1) {
                                $getData['Record'] = array('0' => $getData['Record']);
                            }
                        }

                        $this->cache->set($ckey, $getData, EXPIRETIME_1);
                        return $getData;
                    } else {
                        return false;
                    }
                }
                return $rs;
            }
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);
            if ($this->_checkrs($rs, $type)) {
                if ($data['StartNo'] == -1) {
                    return $rs['TtlRecords'];
                }
                $getData = array();
                if ($rs['RetRecords'] > 0) {
                    $getData['Record'] = $rs['Record'];

                    if ($rs['RetRecords'] == 1) {
                        $getData['Record'] = array('0' => $getData['Record']);
                    }
                    $getData['RetRecords'] = $rs['RetRecords'];
                }
            } else {
                return false;
            }
            return $getData;
        }
    }

}

//end class
?>