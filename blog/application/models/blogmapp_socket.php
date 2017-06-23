<?php

/*
  --|author leicc
  —-|个人博客的相关操作
  --|包括获取个人博客列表、添加个人博客
  --|合法性检查 以及获取个人博客信息
  --|modify  2011-8-23  lifeng
 */

class Blogmapp_socket extends MY_Model {

    function Blogmapp_socket() {
        parent::MY_Model();
    }

    /*
      --获取推荐用户
     */

    function getRecomend($data) {

        $type = 'B533';

        $data['UserID'] = isset($data['UserID']) ? $data['UserID'] : '';
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;
        $data['QryCount'] = isset($data['QryCount']) ? $data['QryCount'] : 5;

        if (ISCACHE) {
            if ($data['StartNo'] == -1) {

                $ckey = config_item('K5001');
                $ckey = str_replace('{UserID}', $data['UserID'], $ckey);
                $rs = $this->cache->get($ckey);

                if (!$rs) {

                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);

                    if ($this->_checkrs($rs, $type, $data)) {
                        $getData = array();

                        $getData['FlagCode'] = md5(time());
                        $getData['TtlRecords'] = $rs['TtlRecords'];

                        if ((isset($getData['TtlRecords']) && $getData['TtlRecords'] > 0)) {
                            $this->cache->set($ckey, $getData, EXPIRETIME_1);
                        }
                        unset($rs);
                        return $getData;
                    } else {
                        return false;
                    }
                }

                return $rs;
            } else {
                $ckey = config_item('K5002');
                $ckey = str_replace('{UserID}', $data['UserID'], $ckey);
                $ckey = str_replace('{PageNo}', ($data['StartNo'] / $data['QryCount'] + 1), $ckey);

                $rs = $this->cache->get($ckey);

                //error_log(print_r($ckey,true), 3, '/home/httpd/logs/a23132.log');
                if (!$rs || ($data['FlagCode'] != $rs['FlagCode'])) {

                    $getData = array();
                    $rs = $this->socket['newblog']->senddata($type, $data);

                    $rs = xmltoarray($rs);

                    if ($this->_checkrs($rs, $type, $data)) {
                        $getData['RetRecords'] = 0;
                        if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {

                            if ($rs['RetRecords'] == 1) {
                                $rs['Record'] = array($rs['Record']);
                            }

                            $getData['RetRecords'] = $rs['RetRecords'];
                            $getData['Record'] = $rs['Record'];
                            $getData['FlagCode'] = $data['FlagCode'];
                            $this->cache->set($ckey, $getData, EXPIRETIME_1);
                        }
                        unset($rs);
                        return $getData;
                    } else {
                        return false;
                    }
                }
                return $rs;
            }
        } else {

            if ($data['StartNo'] == -1) {

                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type)) {

                    return $rs;
                }
                return false;
            } else {

                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                $getData = array();
                if ($this->_checkrs($rs, $type)) {
                    $getData['RetRecords'] = 0;
                    if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
                        if ($rs['RetRecords'] == 1) {
                            $rs['Record'] = array($rs['Record']);
                        }
                        $getData['RetRecords'] = $rs['RetRecords'];
                        $getData['Record'] = $rs['Record'];
                    }
                }
                unset($rs);

                return $getData;
            }
        }
    }

    /*
      --获取自己订阅的名家高手
     */

    function getPersonal($data) {

        $type = 'B535';

        if (empty($data['UserID'])) {
            return false;
        }

        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;
        $data['QryCount'] = isset($data['QryCount']) ? $data['QryCount'] : 5;

        if (ISCACHE) {
            if ($data['StartNo'] == -1) {

                $ckey = config_item('K5003');
                $ckey = str_replace('{UserID}', $data['UserID'], $ckey);
                $rs = $this->cache->get($ckey);

                if (!$rs) {

                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);

                    if ($this->_checkrs($rs, $type, $data)) {
                        $getData = array();

                        $getData['FlagCode'] = md5(time());
                        $getData['TtlRecords'] = $rs['TtlRecords'];

                        if ((isset($getData['TtlRecords']) && $getData['TtlRecords'] > 0)) {
                            $this->cache->set($ckey, $getData, EXPIRETIME_1);
                        }
                        unset($rs);
                        return $getData;
                    } else {
                        return false;
                    }
                }

                return $rs;
            } else {
                $ckey = config_item('K5004');
                $ckey = str_replace('{UserID}', $data['UserID'], $ckey);
                $ckey = str_replace('{PageNo}', ($data['StartNo'] / $data['QryCount'] + 1), $ckey);

                $rs = $this->cache->get($ckey);

                //error_log(print_r($ckey,true), 3, '/home/httpd/logs/a23132.log');
                if (!$rs || ($data['FlagCode'] != $rs['FlagCode'])) {

                    $getData = array();
                    $rs = $this->socket['newblog']->senddata($type, $data);

                    $rs = xmltoarray($rs);

                    if ($this->_checkrs($rs, $type, $data)) {
                        $getData['RetRecords'] = 0;
                        if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {

                            if ($rs['RetRecords'] == 1) {
                                $rs['Record'] = array($rs['Record']);
                            }

                            $getData['RetRecords'] = $rs['RetRecords'];
                            $getData['Record'] = $rs['Record'];
                            $getData['FlagCode'] = $data['FlagCode'];
                            $this->cache->set($ckey, $getData, EXPIRETIME_1);
                        }
                        unset($rs);
                        return $getData;
                    } else {
                        return false;
                    }
                }
                return $rs;
            }
        } else {

            if ($data['StartNo'] == -1) {

                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type)) {

                    return $rs;
                }
                return false;
            } else {

                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                $getData = array();
                if ($this->_checkrs($rs, $type)) {
                    $getData['RetRecords'] = 0;
                    if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
                        if ($rs['RetRecords'] == 1) {
                            $rs['Record'] = array($rs['Record']);
                        }
                        $getData['RetRecords'] = $rs['RetRecords'];
                        $getData['Record'] = $rs['Record'];
                    }
                }
                unset($rs);

                return $getData;
            }
        }
    }

    /*
      订阅博主
     */

    function addRecomend($data) {

        $type = 'B531';
        $ckey = config_item('K5001'); //未添加的名家高手列表total
        $ckey = str_replace('{UserID}', $data['UserID'], $ckey);

        $ckeyp = config_item('K5003'); //已添加的名家高手列表total
        $ckeyp = str_replace('{UserID}', $data['UserID'], $ckeyp);

        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type)) {

            $this->cache->delete($ckey);
            $this->cache->delete($ckeyp);
            return $rs;
        }

        return false;
    }

    /*
      取消订阅
     */

    function delRecomend($data) {

        $type = 'B532';
        $ckey = config_item('K5001'); //未添加的名家高手列表total
        $ckey = str_replace('{UserID}', $data['UserID'], $ckey);

        $ckeyp = config_item('K5003'); //已添加的名家高手列表total
        $ckeyp = str_replace('{UserID}', $data['UserID'], $ckeyp);

        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type)) {

            $this->cache->delete($ckey);
            $this->cache->delete($ckeyp);
            return $rs;
        }

        return false;
    }

    /*
      搜索名家高手
     */

    function searchRecomend($data) {

        $type = 'B534';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type)) {

            return $rs;
        }

        return false;
    }

    /*
      --|获取文章评论列表,mobile用
     */

    function getArtCommentListMobile($data) {
        $type = 'B301';
        $data['BlogType'] = isset($data['BlogType']) ? $data['BlogType'] : 1;
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;
        $data['OrderBy'] = 1;
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

                        return $rs;
                    }
                    return false;
                }
                return $rs;
            } else {
                $ckey = config_item('K5005');
                $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
                $ckey = str_replace('{PageNo}', ($data['StartNo'] / $data['QryCount'] + 1), $ckey);
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

            //error_log(print_r($data,true).'|'.print_r($rs,true).'|', 3, '/home/httpd/logs/a11.log');

            if ($this->_checkrs($rs, $type)) {
                if ($data['StartNo'] == -1) {
                    return $rs;
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

}

//end class
?>