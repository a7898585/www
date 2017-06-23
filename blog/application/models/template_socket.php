<?php

/*
  --|author leicc
  —-|个人博客样式的相关操作
 */

class Template_socket extends MY_Model {

    function Template_socket() {
        parent::MY_Model();
    }

    /*
      --|获取博客样式分类列表
     */

    function getShareCssList($data) {
        $type = 'B117';
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : -1;
        $data['QryCount'] = isset($data['QryCount']) ? $data['QryCount'] : 9;
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);
        $getData = false;
        if ($this->_checkrs($rs, $type)) {
            if ($data['StartNo'] == -1) {
                return $rs['TtlRecords'];
            } else {
                if (isset($rs['RetRecords'])) {
                    $getData['RetRecords'] = $rs['RetRecords'];
                }
                if (isset($rs['Record'])) {
                    $getData['Record'] = $rs['Record'];
                }
            }
        }
        return $getData;
    }

    //更新模板样式信息
    function aupdCssStyle($data) {
        $type = 'B118';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type) && $rs['RetRecords'] > 0) {
            return $rs['Record']['StyleID'];
        }
        return false;
    }

    //删除样式模板信息
    function delCssStyle($data) {
        $type = 'B119';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type) && $rs['RetRecords'] > 0) {
            return true;
        }
        return false;
    }

    //获取样式分类信息
    function getCssStyleSortList($data) {
        $type = 'B120';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);
        $getData = array();
        $getData['RetRecords'] = 0;
        if ($this->_checkrs($rs, $type) && $rs['RetRecords'] > 0) {
            $getData['RetRecords'] = $rs['RetRecords'];
            $getData['Record'] = $rs['Record'];
        }
        return $getData;
    }

    //获取样式信息
    function getCssStyleInfoById($data) {
        $type = 'B117';
        $data['StartNo'] = 0;
        $data['QryCount'] = 1;

        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if (!$this->_checkrs($rs, $type) || !isset($rs['Record'])) {
            $rs['Record'] = array();
        }
        return $rs['Record'];
    }

}

//end class
?>