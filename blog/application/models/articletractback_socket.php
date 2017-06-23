<?php

/*
  --|博客标签的相关动作
  --|modify  2011-8-23  lifeng
 */

class Articletractback_socket extends MY_Model {

    function Articletractback_socket() {
        parent::MY_Model();
    }

    /*
      --|获取引用通告列表
     */

    function getTrackBackList($data) {
        $type = 'B330';
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;

        if (ISCACHE) {
            if ($data['StartNo'] == -1) {
                $ckey = config_item('K1056');
                $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
                $rs = $this->cache->get($ckey);
                if (!$rs) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);

                    if ($this->_checkrs($rs, $type)) {
                        $rs['FlagCode'] = md5($rs['TtlRecords'] . time());
                        $this->cache->set($ckey, $rs);
                        return $rs;
                    } else {
                        return false;
                    }
                }
                return $rs;
            } else {
                $ckey = config_item('K1057');
                $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
                $ckey = str_replace('{PageNo}', ($data['StartNo'] / $data['QryCount'] + 1), $ckey);
                $rs = $this->cache->get($ckey);
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
                        $this->cache->set($ckey, $getData);
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

    /*
      --|新增博客文章应用通告
     */

    function addTractBack($data) {
        $type = 'B331';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);
        if ($this->_checkrs($rs, $type)) {
            if (ISCACHE) {
                $ckey = config_item('K1056');
                $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
                $this->cache->delete($ckey);
            }
            return true;
        }
        return false;
    }

    /*
      --|删除博客文章引用通告
     */

    function delTractBack($data) {
        $type = 'B332';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);
        if ($this->_checkrs($rs, $type)) {
            if (ISCACHE) {
                $ckey = config_item('K1056');
                $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
                $this->cache->delete($ckey);
            }
            return true;
        }
        return false;
    }

}

//end class
?>