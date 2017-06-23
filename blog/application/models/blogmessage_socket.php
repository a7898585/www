<?php

class blogmessage_socket extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    /*
     * 发送留言
     * 关联  K1090
     */

    function sendMessage($param) {
        $type = 'B500';
        $StartDate = isset($param['StartDate']) ? $param['StartDate'] : 0;
        $EndDate = isset($param['EndDate']) ? $param['StartDate'] : 0;
        $rs = $this->socket['newblog']->senddata($type, $param);
        $rs = xmltoarray($rs);

        if ($rs['Code'] == '00') {

            $ckey = $this->config->item("K1090");
            $ckey = str_replace('{UserID}', $param['UserID'], $ckey);
            $ckey = str_replace('{StartDate}', $StartDate, $ckey);
            $ckey = str_replace('{EndDate}', $EndDate, $ckey);
            $this->cache->del($ckey);

            return $rs['Record'];
        } else {
            return false;
        }
    }

    /**
     * @ delMsg 删除用户会话
     * 关联的是K1090
     * @param array $param
     * UserID 用户ID
     * SessionIDs 会话ID
     * @access public
     * @return int
     */
    public function delMsg($param) {

        $type = 'B503';
        $param['StartNo'] = isset($param['StartNo']) ? $param['StartNo'] : 0;
        $param['QryCount'] = isset($param['QryCount']) ? $param['QryCount'] : 0;
        $StartDate = isset($param['StartDate']) ? $param['StartDate'] : 0;
        $EndDate = isset($param['EndDate']) ? $param['StartDate'] : 0;
        $rs = $this->socket['newblog']->senddata($type, $param);
        $rs = xmltoarray($rs);

        if ($rs['Code'] == '00' && $rs['TtlRecords'] > 0) {

            $ckey = $this->config->item("K1090");
            $ckey = str_replace('{UserID}', $param['UserID'], $ckey);
            $ckey = str_replace('{StartDate}', $StartDate, $ckey);
            $ckey = str_replace('{EndDate}', $EndDate, $ckey);


            $this->cache->del($ckey);

            return 100;
        } else {
            return 101;
        }
    }

    /*
     *   留言页面
     *   缓存键值key  K1090  K1091
     */

    public function leaveMessageList($param) {

        //	error_log(date("Y-m-d H:i:s")."|".print_r($param, true)."\r\n", 3, '/home/wuzg/message.log');

        if (empty($param['UserID'])) {
            return false;
        }

        $type = "B506";
        $param['StartNo'] = isset($param['StartNo']) ? $param['StartNo'] : 0;
        $param['QryCount'] = isset($param['QryCount']) ? $param['QryCount'] : 0;
        $StartDate = isset($param['StartDate']) ? $param['StartDate'] : 0;
        $EndDate = isset($param['EndDate']) ? $param['EndDate'] : 0;

        if (ISCACHE) {
            if ($param['StartNo'] == -1) {
                $ckey = $this->config->item("K1090");
                $ckey = str_replace('{UserID}', $param['UserID'], $ckey);
                $ckey = str_replace('{StartDate}', $StartDate, $ckey);
                $ckey = str_replace('{EndDate}', $EndDate, $ckey);

                //		error_log('list'.date("Y-m-d H:i:s")."|".$ckey."\r\n", 3, '/home/wuzg/message.log');

                $rs = $this->cache->get($ckey);

                $rs = "";

                if (!$rs) {
                    $rs = $this->socket['newblog']->senddata($type, $param);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type, $param)) {
                        $getDate = array();
                        $getDate['TtlRecords'] = $rs['TtlRecords'];
                        $getDate['flagCode'] = md5($rs['TtlRecords'] . time());

                        if (isset($getDate['TtlRecords']) && $getDate['TtlRecords'] > 0) {
                            $this->cache->set($ckey, $getDate, EXPIRETIME_1);
                        }

                        return $getDate;
                    } else {
                        return false;
                    }
                }

                return $rs;
            } else {
                $ckey = $this->config->item("K1091");
                $ckey = str_replace('{UserID}', $param['UserID'], $ckey);
                $ckey = str_replace('{PageNo}', ($param['StartNo'] / $param['QryCount'] + 1), $ckey);
                $ckey = str_replace('{StartDate}', $StartDate, $ckey);
                $ckey = str_replace('{EndDate}', $EndDate, $ckey);

                $rs = $this->cache->get($ckey);
                $rs = "";
                if (!$rs || $rs['flagCode'] != $param['flagCode']) {
                    $rs = $this->socket['newblog']->senddata($type, $param);
                    $rs = xmltoarray($rs);

                    $getDate = array();
                    if ($this->_checkrs($rs)) {
                        $getDate['RetRecords'] = $rs['RetRecords'];

                        $getDate['Record'] = $rs['Record'];

                        if ($rs['RetRecords'] == 1) {
                            $getDate['Record'] = array('0' => $getDate['Record']);
                        }

                        $getDate['flagCode'] = $param['flagCode'];


                        $this->cache->set($ckey, $getDate, EXPIRETIME_1);
                        unset($rs);
                        //	var_dump($getDate);
                        return $getDate;
                    } else {
                        return false;
                    }
                }

                return $rs;
            }
        } else {
            if ($param['StartNo'] == -1) {
                $rs = $this->socket['passport']->senddata($type, $param);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type, $param)) {
                    return $rs;
                } else {
                    return false;
                }
            } else {
                $rs = $this->socket['passport']->senddata($type, $param);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type, $param)) {
                    return $rs;
                } else {
                    return false;
                }
            }
        }
    }

}

//end class
?>