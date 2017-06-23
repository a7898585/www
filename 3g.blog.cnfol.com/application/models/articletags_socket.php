<?php

/*
  --|博客标签的相关动作
  --|modify  2011-8-23  lifeng
  --|20120308 关闭缓存，因为这个模型访问量不大，时实性要求教高，所有不用缓存
 */

class Articletags_socket extends MY_Model {

    function Articletags_socket() {
        parent::MY_Model();
    }

    /*
      --|查询博客标签
     */

    function getArticleTagList($data) {
        if (empty($data['UserIDs']))
            return false;
        $type = 'B034';
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;
        $getData = array();

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

    /*
      --|查询博客个人主页我的标签
     */

    function getArticleMyTagList($data) {
        if (empty($data['UserIDs']))
            return false;

        $type = 'B034'; //接口待定
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;
        $getData = array();


        if (ISCACHE) {
            if ($data['StartNo'] == -1) {
                $ckey = config_item('K2013');
                $ckey = str_replace('{UserIDs}', $data['UserIDs'], $ckey);

                $rs = $this->cache->get($ckey);
                if (!$rs) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);

                    if ($this->_checkrs($rs, $type, $data)) {
                        $getData['TtlRecords'] = isset($rs['TtlRecords']) ? $rs['TtlRecords'] : 0;
                        $this->cache->set($ckey, $getData['TtlRecords'], EXPIRETIME_1);

                        return $getData['TtlRecords'];
                    } else {
                        return false;
                    }
                }

                return $rs;
            } else {
                $ckey = config_item('K2014');
                $ckey = str_replace('{UserIDs}', $data['UserIDs'], $ckey);

                $rs = $this->cache->get($ckey);

                if (!$rs) {
                    $rs = $this->socket['newblog']->senddata($type, $data);

                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type, $data)) {
                        if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
                            $getData['RetRecords'] = $rs['RetRecords'];
                            if ($rs['RetRecords'] == 1) {
                                $rs['Record'] = array('0' => $rs['Record']);
                            }
                            $getData['Record'] = $rs['Record'];
                            $this->cache->set($ckey, $getData, EXPIRETIME_1);
                        }

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
                } else {
                    return false;
                }
            } else {
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type)) {
                    if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
                        $getData['RetRecords'] = $rs['RetRecords'];
                        if ($rs['RetRecords'] == 1) {
                            $rs['Record'] = array('0' => $rs['Record']);
                        }
                        $getData['Record'] = $rs['Record'];

                        return $getData;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }
    }

    /*
      --|新增博客个人标签,添加完返回标签id
     */

    function addArticleTag($data) {
        if (empty($data['UserID']))
            return false;
        $type = 'B033';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);
        if ($this->_checkrs($rs, $type)) {
            return isset($rs['Record']) ? $rs['Record'] : false;
        } else {
            return false;
        }
    }

    /*
      --|删除个人博客分类
     */

    function delArticleTag($data) {
        if (empty($data['TagIDs']))
            return false;
        $type = 'B036';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);
        error_log(date("Y-m-d H:i:s") . print_r($rs, true) . print_r($data, true), 3, '/home/wuzg/B036.log');

        if ($this->_checkrs($rs, $type)) {
            $ckey = config_item('K2013'); //博客个人主页我的标签
            $ckey = str_replace('{UserIDs}', $data['UserIDs'], $ckey);
            //$this->cache->delete($ckey);
            $this->cache->set($ckey, '', 1); //上线后改回

            $ckey = config_item('K2014'); //博客个人主页我的标签
            $ckey = str_replace('{UserIDs}', $data['UserIDs'], $ckey);
            //$this->cache->delete($ckey);
            $this->cache->set($ckey, '', 1); //上线后改回

            return true;
        }
        return false;
    }

    /*
      --|文章发表调用热门标签
     */

    function getHotTagList($data) {
        $type = 'B034';
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;
        $getData = array();

        if (!ISCACHE) {
            $ckey = config_item('K2017');
            $rs = $this->cache->get($ckey);

            if (!$rs) {
                $rs = $this->socket['newblog']->senddata($type, $data);

                $rs = xmltoarray($rs);
                if ($this->_checkrs($rs, $type, $data)) {
                    if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
                        $getData['RetRecords'] = $rs['RetRecords'];
                        if ($rs['RetRecords'] == 1) {
                            $rs['Record'] = array('0' => $rs['Record']);
                        }
                        $getData['Record'] = $rs['Record'];

                        $this->cache->set($ckey, $getData, EXPIRETIME_2);
                    }

                    return $getData;
                } else {
                    return false;
                }
            }

            return $rs;
        } else {

            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);

            //error_log(print_r($rs, true).'|'.print_r($data, true), 3, '/home/httpd/logs/alog.log');

            if ($this->_checkrs($rs, $type)) {
                if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
                    $getData['RetRecords'] = $rs['RetRecords'];

                    if ($rs['RetRecords'] == 1) {
                        $rs['Record'] = array('0' => $rs['Record']);
                    }

                    $getData['Record'] = $rs['Record'];

                    return $getData;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

}

//end class
?>