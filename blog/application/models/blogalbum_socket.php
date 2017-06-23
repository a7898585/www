<?php

/*
  --|author leicc
  —-|个人博客的相关操作
  --|包括获取个人博客列表、添加个人博客
  --|合法性检查 以及获取个人博客信息
  --|modify  2011-8-23  lifeng
 */

class Blogalbum_socket extends MY_Model {

    function Blogalbum_socket() {
        parent::MY_Model();
    }

    //获取用户相册列表
    function getblogalbum($data) {
        $type = 'U422';
        $data['Type'] = isset($data['Type']) ? $data['Type'] : '2';

        if (ISCACHE) {
            if ($data['StartNo'] == -1) {
                $ckey = config_item('K1033');
                $ckey = str_replace('{MemberID}', $data['RelationID'], $ckey);
                $rs = $this->cache->get($ckey);

                if (!$rs) {
                    $rs = $this->socket['passport']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($rs['Code'] == '00') {
                        $this->cache->set($ckey, $rs['TtlRecords']);
                        return $rs['TtlRecords'];
                    }
                }
                return $rs;
            } else {
                $ckey = config_item('K1034');
                $ckey = str_replace('{MemberID}', $data['RelationID'], $ckey);
                $rs = $this->cache->get($ckey);

                if (!$rs || $rs['count'] == 0) {
                    $rs = $this->socket['passport']->senddata($type, $data);
                    $rs = xmltoarray($rs);

                    if ($rs['Code'] == '00') {
                        $list = array();
                        #记录条数
                        $count = $rs['RetRecords'];
                        $list = isset($rs['Record']) ? $rs['Record'] : array();
                        if ($rs['RetRecords'] == 1) {
                            $list = array(0 => $rs['Record']); #构造成二维数组
                        }
                        $rs = array('count' => $count, 'list' => $list);
                        $this->cache->set($ckey, $rs);
                        return $rs;
                    }
                }
                return $rs;
            }
        } else {
            $rs = $this->socket['passport']->senddata($type, $data);
            $rs = xmltoarray($rs);

            if ($rs['Code'] == '00') {
                if ($data['StartNo'] == -1) {
                    return $rs['TtlRecords'];
                }
                $list = array();
                #记录条数
                $count = $rs['RetRecords'];
                $list = isset($rs['Record']) ? $rs['Record'] : array();
                if ($rs['RetRecords'] == 1) {
                    $list = array(0 => $rs['Record']); #构造成二维数组
                }
                $rs = array('count' => $count, 'list' => $list);
            }
            return $rs;
        }
    }

    //获取相册信息
    function getAlubmInfoById($data) {
        $type = 'U422';
        if (ISCACHE) {
            $ckey = config_item('K1035');
            $ckey = str_replace('{MemberID}', $data['RelationID'], $ckey);
            $ckey = str_replace('{AlbumID}', $data['AlbumID'], $ckey);
            $rs = $this->cache->get($ckey);
            if (!$rs) {
                $rs = $this->socket['passport']->senddata($type, $data);
                $rs = xmltoarray($rs);
                $list = false;
                if ($rs['Code'] == '00') {
                    $list = isset($rs['Record']) ? $rs['Record'] : false;
                    $this->cache->set($ckey, $list);
                }
                return $list;
            }
            return $rs;
        } else {
            $rs = $this->socket['passport']->senddata($type, $data);
            $rs = xmltoarray($rs);
            $list = false;
            if ($rs['Code'] == '00') {
                $list = isset($rs['Record']) ? $rs['Record'] : false;
            }
            return $list;
        }
    }

    //获取单张图片
    function getPhotoById($data) {
        $type = 'U425';
        $data['StartNo'] = 0;
        $data['QryCount'] = 1;
        if (ISCACHE) {
            $ckey = config_item('K1032');
            $ckey = str_replace('{PhotoID}', $data['PhotoID'], $ckey);
            $ckey = str_replace('{AlbumID}', $data['AlbumID'], $ckey);
            $ckey = str_replace('{UserID}', $data['UserID'], $ckey);
            $rs = $this->cache->get($ckey);
            if (!$rs) {
                $rs = $this->socket['passport']->senddata($type, $data);
                $rs = xmltoarray($rs);
                if ($rs['Code'] == '00') {
                    $tempInfo = (isset($rs['Record'])) ? $rs['Record'] : '';
                    $this->cache->set($ckey, $tempInfo);
                    return $tempInfo;
                }
                return false;
            }
            return $rs;
        } else {
            $rs = $this->socket['passport']->senddata($type, $data);
            $rs = xmltoarray($rs);
            if ($rs['Code'] == '00') {
                return (isset($rs['Record'])) ? $rs['Record'] : '';
            }
            return false;
        }
    }

    //获取用户博客的相册图片信息
    function getAlbumPhoteList($data) {
        $type = 'U425';
        $data['AlbumID'] = isset($data['AlbumID']) ? $data['AlbumID'] : 0;

        if (ISCACHE) {
            $ckey = config_item('K1036');
            $ckey = str_replace('{AlbumID}', $data['AlbumID'], $ckey);
            $ckey = str_replace('{MemberID}', $data['RelationID'], $ckey);
            $rs = $this->cache->get($ckey);
            if (!$rs) {
                $rs = $this->socket['passport']->senddata($type, $data);
                $rs = xmltoarray($rs);
                if ($rs['Code'] == '00') {
                    $list = array();
                    #记录条数
                    $count = $rs['RetRecords'];
                    $list = isset($rs['Record']) ? $rs['Record'] : array();
                    if ($rs['RetRecords'] == 1) {
                        $list = array(0 => $rs['Record']); #构造成二维数组
                    }
                    $rs = array(
                        'count' => $count,
                        'list' => $list
                    );
                    $this->cache->set($ckey, $rs);
                }
            }
            return $rs;
        } else {
            $rs = $this->socket['passport']->senddata($type, $data);
            $rs = xmltoarray($rs);

            if ($rs['Code'] == '00') {
                $list = array();
                #记录条数
                $count = $rs['RetRecords'];
                $list = isset($rs['Record']) ? $rs['Record'] : array();
                if ($rs['RetRecords'] == 1) {
                    $list = array(0 => $rs['Record']); #构造成二维数组
                }
                $rs = array(
                    'count' => $count,
                    'list' => $list
                );
            }
            return $rs;
        }
    }

    //添加相册
    function addAlbum($data) {

        $type = 'U420';
        $data['AlbumID'] = isset($data['AlbumID']) ? $data['AlbumID'] : '0';
        $data['CoverID'] = isset($data['CoverID']) ? $data['CoverID'] : '0';
        $data['Type'] = isset($data['Type']) ? $data['Type'] : '2';
        $rs = $this->socket['passport']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($rs['Code'] == '00') {
            if (ISCACHE) {
                $ckey = config_item('K1033');
                $ckey = str_replace('{MemberID}', $data['RelationID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1034');
                $ckey = str_replace('{MemberID}', $data['RelationID'], $ckey);
                $this->cache->delete($ckey);

                if ($data['AlbumID'] > 0) {
                    $ckey = config_item('K1035');
                    $ckey = str_replace('{AlbumID}', $data['AlbumID'], $ckey);
                    $ckey = str_replace('{MemberID}', $data['RelationID'], $ckey);
                    $this->cache->delete($ckey);
                }
            }
            return isset($rs['Record']['AlbumID']) ? $rs['Record']['AlbumID'] : true;
        }
        return 0;
    }

    //删除相册
    function delAlbum($data) {
        $type = 'U421';
        $rs = $this->socket['passport']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($rs['Code'] == '00') {
            if (ISCACHE) {
                $ckey = config_item('K1033');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1034');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);

                if ($data['AlbumID'] > 0) {
                    $ckey = config_item('K1035');
                    $ckey = str_replace('{AlbumID}', $data['AlbumID'], $ckey);
                    $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                    $this->cache->delete($ckey);

                    $ckey = config_item('K1036');
                    $ckey = str_replace('{AlbumID}', $data['AlbumID'], $ckey);
                    $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                    $this->cache->delete($ckey);
                }
            }
            return true;
        }
        return false;
    }

    //新增相片信息
    function addPhoto($data) {
        $type = 'U423';
        $data['PhotoID'] = isset($data['PhotoID']) ? $data['PhotoID'] : 0;
        $data['IsCover'] = isset($data['IsCover']) ? $data['IsCover'] : 0;
        $data['SaveType'] = 2;

        $rs = $this->socket['passport']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type)) {
            if (ISCACHE) {
                $ckey = config_item('K1036');
                $ckey = str_replace('{AlbumID}', $data['AlbumID'], $ckey);
                $ckey = str_replace('{MemberID}', $data['RelationID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1033');
                $ckey = str_replace('{MemberID}', $data['RelationID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1034');
                $ckey = str_replace('{MemberID}', $data['RelationID'], $ckey);
                $this->cache->delete($ckey);

                if ($data['PhotoID'] > 0) {
                    $ckey = config_item('K1032');
                    $ckey = str_replace('{PhotoID}', $data['PhotoID'], $ckey);
                    $ckey = str_replace('{AlbumID}', $data['AlbumID'], $ckey);
                    $ckey = str_replace('{UserID}', $data['UserID'], $ckey);
                    $this->cache->delete($ckey);
                }
            }
            return true;
        }
        return false;
    }

    //删除图片信息
    function delPhoto($data) {
        $type = 'U424';
        $rs = $this->socket['passport']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type)) {
            if (ISCACHE) {
                $ckey = config_item('K1036');
                $ckey = str_replace('{AlbumID}', $data['AlbumID'], $ckey);
                $ckey = str_replace('{MemberID}', $data['RelationID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1033');
                $ckey = str_replace('{MemberID}', $data['RelationID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1034');
                $ckey = str_replace('{MemberID}', $data['memberid'], $ckey);
                $this->cache->delete($ckey);

                if ($data['PhotoID'] > 0) {
                    $ckey = config_item('K1032');
                    $ckey = str_replace('{PhotoID}', $data['PhotoID'], $ckey);
                    $ckey = str_replace('{AlbumID}', $data['AlbumID'], $ckey);
                    $ckey = str_replace('{UserID}', $data['UserID'], $ckey);
                    $this->cache->delete($ckey);
                }
            }
            return true;
        }
        return false;
    }

    //增修图片评论
    function addPhotoComment($data) {
        $type = 'U426';
        $rs = $this->socket['passport']->senddata($type, $data);
        $rs = xmltoarray($rs);
        if ($this->_checkrs($rs, $type)) {
            if (ISCACHE) {
                $ckey = config_item('K1037');
                $ckey = str_replace('{AlbumID}', $data['AlbumID'], $ckey);
                $ckey = str_replace('{PhotoID}', $data['PhotoID'], $ckey);
                $this->cache->delete($ckey);
            }
            return true;
        }
        return false;
    }

    //删除图片评论
    function delPhotoComment($data) {
        $type = 'U427';
        $rs = $this->socket['passport']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type)) {
            if (ISCACHE) {
                $ckey = config_item('K1037');
                $ckey = str_replace('{AlbumID}', $data['AlbumID'], $ckey);
                $ckey = str_replace('{PhotoID}', $data['PhotoID'], $ckey);
                $this->cache->delete($ckey);
            }
            return true;
        }
        return false;
    }

    //获取图片评论
    //获取用户博客的相册图片信息
    function getPhotoComment($data) {
        $type = 'U428';
        $data['OrderType'] = isset($data['OrderType']) ? $data['OrderType'] : 1;
        if (ISCACHE) {
            #记录条数
            if (-1 == $data['StartNo']) {
                $ckey = config_item('K1037');
                $ckey = str_replace('{AlbumID}', $data['AlbumID'], $ckey);
                $ckey = str_replace('{PhotoID}', $data['PhotoID'], $ckey);
                $rs = $this->cache->get($ckey);

                if (!$rs) {
                    $rs = $this->socket['passport']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type)) {
                        $rs['TtlRecords'] = ((isset($rs['TtlRecords'])) ? $rs['TtlRecords'] : 0);
                        $rs['FlagCode'] = md5($rs['TtlRecords'] . time());
                        $this->cache->set($ckey, $rs);
                        return $rs;
                    } else {
                        return false;
                    }
                }
                return $rs;
            } else {
                $ckey = config_item('K1038');
                $ckey = str_replace('{AlbumID}', $data['AlbumID'], $ckey);
                $ckey = str_replace('{PhotoID}', $data['PhotoID'], $ckey);
                $ckey = str_replace('{PageNo}', ($data['StartNo'] / $data['QryCount'] + 1), $ckey);
                $rs = $this->cache->get($ckey);

                if (!$rs || ($data['FlagCode'] != $rs['FlagCode'])) {
                    $rs = $this->socket['passport']->senddata($type, $data);
                    $rs = xmltoarray($rs);

                    if ($this->_checkrs($rs, $type)) {
                        if (isset($rs['RetRecords']) && $rs['RetRecords'] == 1) {
                            $rs['Record'] = array(0 => $rs['Record']); #构造成二维数组
                        }
                        $getData['Record'] = (isset($rs['Record']) ? $rs['Record'] : false);
                        $getData['FlagCode'] = $data['FlagCode'];
                        $this->cache->set($ckey, $getData);

                        return $getData;
                    }
                    return false;
                }
                return $rs;
            }
        } else {
            $rs = $this->socket['passport']->senddata($type, $data);
            $rs = xmltoarray($rs);

            if ($this->_checkrs($rs, $type)) {
                #记录条数
                if (-1 == $data['StartNo']) {
                    return ((isset($rs['TtlRecords'])) ? $rs['TtlRecords'] : 0);
                }

                if (isset($rs['RetRecords']) && $rs['RetRecords'] == 1) {
                    $rs['Record'] = array(0 => $rs['Record']); #构造成二维数组
                }
                return (isset($rs['Record']) ? $rs['Record'] : false);
            }
            return false;
        }
    }

}

//end class
?>