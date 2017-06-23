<?php

/*
  --|author leicc
  —-|个人博客的相关操作
  --|包括获取个人博客列表、添加个人博客
  --|合法性检查 以及获取个人博客信息
  --|modify  2011-8-24  lifeng
 */

class Bloglink_socket extends MY_Model {

    function Bloglink_socket() {
        parent::MY_Model();
    }

    /*
      --|return 0 getCount  1 getList
      --|获取个人博客友情链接数
      --|缓存key  1019,1020
      --|参数flag 是否判断缓存，true是前台访客展示（取所有），false为博主在个人控制面版使用（分页），所以不使用缓存
     */

    function getLinkList($data, $flag = true) {
        if (empty($data['MemberID']))
            return false;
        $type = 'B155';

        $data['Status'] = isset($data['Status']) ? $data['Status'] : '0';
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : -1;
        $data['IsPublic'] = isset($data['IsPublic']) ? $data['IsPublic'] : 0;

        if (ISCACHE && $flag) {
            if ($data['StartNo'] == -1) {
                $ckey = config_item('K1020');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $ckey = str_replace('{IsPublic}', $data['IsPublic'], $ckey);
                $rs = $this->cache->get($ckey);

                if (!$rs) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type)) {
                        $rs['FlagCode'] = md5($rs['TtlRecords'] . time());
                        $this->cache->set($ckey, $rs, EXPIRETIME_4);
                        return $rs;
                    } else {
                        return false;
                    }
                }
                return $rs;
            } else {
                $ckey = config_item('K1019');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $ckey = str_replace('{IsPublic}', $data['IsPublic'], $ckey);
                $rs = $this->cache->get($ckey);

                if (!$rs || ($data['FlagCode'] != $rs['FlagCode'])) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type)) {
                        $getData = array();
                        $getData['RetRecords'] = 0;
                        if (isset($rs['RetRecords']) && ($rs['RetRecords'] > 0)) {
                            $getData['RetRecords'] = $rs['RetRecords'];
                            $getData['Record'] = $rs['Record'];
                        }
                        $getData['FlagCode'] = $data['FlagCode'];
                        $this->cache->set($ckey, $getData, EXPIRETIME_4);
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
                    return $rs;
                }
                $getData = array();
                $getData['RetRecords'] = 0;
                if (isset($rs['RetRecords']) && ($rs['RetRecords'] > 0)) {
                    $getData['RetRecords'] = $rs['RetRecords'];
                    $getData['Record'] = $rs['Record'];
                }
            } else {
                return false;
            }
            unset($rs);
            return $getData;
        }
    }

    /*
      --|增修友情链接
      --|同步关联缓存1019,1020
     */

    function aupdLink($data) {
        if (empty($data['MemberID']))
            return false;
        $type = 'B156';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type, $data)) {
            if ($rs['TtlRecords'] > 0) {
                if (ISCACHE) {
                    $this->_delLinkCache($data);
                }
                return true;
            }
        }
        return false;
    }

    function _delLinkCache($data) {
        $ckey = config_item('K1020');
        $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
        $ckey = str_replace('{IsPublic}', '-1', $ckey);
        $this->cache->delete($ckey);

        $ckey = config_item('K1020');
        $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
        $ckey = str_replace('{IsPublic}', '0', $ckey);
        $this->cache->delete($ckey);

        $ckey = config_item('K1020');
        $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
        $ckey = str_replace('{IsPublic}', '1', $ckey);
        $this->cache->delete($ckey);

        $ckey = config_item('K1021');
        $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
        $this->cache->delete($ckey);
    }

    /*
      --|删除友情链接
      --|同步关联缓存1019,1020
     */

    function delLink($data) {
        if (empty($data['MemberID']))
            return false;
        $type = 'B157';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);
        if ($this->_checkrs($rs, $type)) {
            if ($rs['TtlRecords'] > 0) {
                if (ISCACHE) {
                    $this->_delLinkCache($data);
                }
                return true;
            }
        }
        return false;
    }

    /*
      --|获取个人博客友情链接分类
      --|缓存key: 1021,1022
     */

    function getLinkSortList($data) {
        if (empty($data['MemberID']))
            return false;
        $type = 'B150';
        $data['Status'] = isset($data['Status']) ? $data['Status'] : '0';
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : -1;

        if (ISCACHE) {
            if ($data['StartNo'] == -1) {
                $ckey = config_item('K1021');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $rs = $this->cache->get($ckey);
                if (!$rs) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type)) {
                        $rs['FlagCode'] = md5($rs['TtlRecords'] . time());
                        $this->cache->set($ckey, $rs, EXPIRETIME_4);
                        return $rs;
                    } else {
                        return false;
                    }
                }
                return $rs;
            } else {
                $ckey = config_item('K1022');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $rs = $this->cache->get($ckey);
                if (!$rs || ($data['FlagCode'] != $rs['FlagCode'])) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type)) {
                        $getData = array();
                        $getData['RetRecords'] = $rs['RetRecords'];
                        $getData['Record'] = isset($rs['Record']) ? $rs['Record'] : array();
                        $getData['FlagCode'] = $data['FlagCode'];
                        $this->cache->set($ckey, $getData, EXPIRETIME_4);
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
                $getData['RetRecords'] = $rs['RetRecords'];
                $getData['Record'] = isset($rs['Record']) ? $rs['Record'] : array();
            } else {
                return false;
            }
            unset($rs);
            return $getData;
        }
    }

    /*
      --|增修博客友情链接分类
      --|同步关联缓存：1019,1020,1021,1022
     */

    function aupdLinkSort($data) {
        if (!isset($data['MemberID']))
            return false;
        $type = 'B151';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type)) {
            if ($rs['TtlRecords'] > 0) {
                if (ISCACHE) {
                    $this->_delLinkCache($data);
                }
                return true;
            }
        }
        return false;
    }

    /*
      --|删除链接分类
      --|同步关联缓存：1019,1020,1021,1022
     */

    function delLinkSort($data) {
        if (!isset($data['LinkSortIDs']))
            return false;
        $type = 'B152';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type)) {
            if ($rs['TtlRecords'] > 0) {
                if (ISCACHE) {
                    $this->_delLinkCache($data);
                }
                return true;
            }
        }
        return false;
    }

}

//end class
?>