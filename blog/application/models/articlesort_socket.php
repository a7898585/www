<?php

/*
  --|author leicc
  —-|个人博客的相关操作
  --|包括获取个人博客列表、添加个人博客
  --|合法性检查 以及获取个人博客信息
  --|modify  2011-8-23  lifeng
 */

class Articlesort_socket extends MY_Model {

    function Articlesort_socket() {
        parent::MY_Model();
    }

    /*
      --|查詢博客文章分類列表
     */

    function getArticleSortList($data) {
        if (empty($data['MemberID']))
            return false;
        $type = 'B200';
        $data['Status'] = isset($data['Status']) ? $data['Status'] : 0;
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;
        $getData = array();

        if (ISCACHE) {

            if ($data['StartNo'] == -1) {
                $ckey = config_item('K1025');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $rs = $this->cache->get($ckey);
                if (!$rs) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type)) {
                        $rs['FlagCode'] = md5($rs['TtlRecords'] . time());
                        $this->cache->set($ckey, $rs, EXPIRETIME);
                        return $rs;
                    } else {
                        return false;
                    }
                }
                return $rs;
            } else {
                if (isset($data['AjaxList']) && $data['AjaxList'] == 1) {
                    $ckey = config_item('K1023');
                    $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                    $rs = $this->cache->get($ckey);

                    if (!$rs) {
                        $rs = $this->socket['newblog']->senddata($type, $data);
                        $rs = xmltoarray($rs);
                        if ($this->_checkrs($rs, $type)) {
                            $getData['RetRecords'] = 0;
                            if ($rs['RetRecords'] > 0) {
                                $getData['Record'] = $rs['Record'];
                                $getData['RetRecords'] = $rs['RetRecords'];
                            }
                            $this->cache->set($ckey, $getData);
                            unset($rs);
                            return $getData;
                        } else {
                            return false;
                        }
                    }
                    return $rs;
                } else {
                    $ckey = config_item('K1024');
                    $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                    $ckey = str_replace('{PageNo}', ($data['StartNo'] / $data['QryCount'] + 1), $ckey);

                    if (!$data['first']) {
                        $rs = $this->cache->get($ckey);
                    }

                    if (!$rs || ($data['FlagCode'] != $rs['FlagCode'])) {
                        $rs = $this->socket['newblog']->senddata($type, $data);

                        $rs = xmltoarray($rs);
                        $getData = array();
                        if ($this->_checkrs($rs, $type)) {
                            $getData['RetRecords'] = 0;
                            if ($rs['RetRecords'] > 0) {
                                $getData['Record'] = $rs['Record'];
                                $getData['RetRecords'] = $rs['RetRecords'];
                            }
                            $getData['FlagCode'] = $data['FlagCode'];
                            if (!$data['first']) {
                                $this->cache->set($ckey, $getData);
                            }
                            unset($rs);

                            return $getData;
                        } else {
                            return false;
                        }
                    }


                    return $rs;
                }
            }
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);
            //error_log(print_r($data,true).'|'.print_r($rs,true), 3, '/home/www/html/logs/a1.log');
            if ($this->_checkrs($rs, $type)) {
                if ($data['StartNo'] == -1) {
                    return $rs['TtlRecords'];
                }

                $getData['RetRecords'] = 0;
                if ($rs['RetRecords'] > 0) {
                    $getData['Record'] = $rs['Record'];
                    $getData['RetRecords'] = $rs['RetRecords'];
                }
            }
            unset($rs);
            return $getData;
        }
    }

    /*
      --|增修个人博客文章分类数目
     */

    function addArticleSort($data) {
        if (empty($data['MemberID']))
            return false;
        $type = 'B202';
        $data['SortID'] = isset($data['SortID']) ? $data['SortID'] : '0';
        $data['Status'] = isset($data['Status']) ? $data['Status'] : '0';
        $data['SubTitle'] = (strlen($data['SubTitle']) == 0) ? "  " : $data['SubTitle'];
        $data['Intro'] = (strlen($data['Intro']) == 0) ? "  " : $data['Intro'];

        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type)) {
            if (ISCACHE) {
                $ckey = config_item('K1026');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $ckey = str_replace('{SortID}', $data['SortID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1025');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1023');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);
            }
            if (isset($data['return']) && $data['return'] == 1) {
                return $rs;
            }
            return true;
        }
        return false;
    }

    /*
      --|删除个人博客分类
     */

    function delArticleSort($data) {
        $type = 'B205';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type)) {
            if (ISCACHE) {
                $ckey = config_item('K1026');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $ckey = str_replace('{SortID}', $data['SortID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1025');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1023');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);
            }
            return true;
        }
        return false;
    }

    /*
      --|获取个人博客分类详情
     */

    function getSortInfoByID($data) {
        if (empty($data['MemberID']) || empty($data['SortID']))
            return false;

        $type = 'B201';
        if (ISCACHE) {
            $ckey = config_item('K1026');
            $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
            $ckey = str_replace('{SortID}', $data['SortID'], $ckey);
            $rs = $this->cache->get($ckey);

            if (!$rs) {
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);
                $getData = array();
                if ($this->_checkrs($rs, $type) && $rs['RetRecords'] > 0) {
                    $getData = $rs['Record'];
                }
                $this->cache->set($ckey, $getData);
                return $getData;
            }
            return $rs;
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);

            $getData = array();
            if ($this->_checkrs($rs, $type) && $rs['RetRecords'] > 0) {
                $getData = $rs['Record'];
            }
            return $getData;
        }
    }

}

//end class
?>