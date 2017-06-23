<?php

/*
  --|author leicc
  —-|个人博客的相关操作
  --|包括获取个人博客列表、添加个人博客
  --|合法性检查 以及获取个人博客信息.
  --|modify  2011-8-23  lifeng
 */

class Memberblog_socket extends MY_Model {

    function Memberblog_socket() {
        parent::MY_Model();
    }

    /*
      获取个人博客权限信息
     */

    function getAccessList($data, $memberid = 0) {
        $type = 'B054';
        if (ISCACHE) {
            $ckey = config_item('K1009');
            $ckey = str_replace('{GroupIDs}', $data['GroupIDs'], $ckey);
            $rs = $this->cache->get($ckey);

            if (!$rs) {
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type) && isset($rs['Record'])) {
                    $access = '';
                    if (isset($rs['Record']['AccessID'])) {
                        $rs['Record'] = array(0 => $rs['Record']);
                    }
                    foreach ($rs['Record'] as $record) {
                        $access .= ',' . $record['AccessID'];
                        $access = trim($access, ',');
                    }
                    $access_arr = explode(',', $access);
                    $this->cache->set($ckey, $access_arr);
                    return $access_arr;
                }
                return false;
            }
            return $rs;
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);
            if ($this->_checkrs($rs, $type) && isset($rs['Record'])) {
                $access = '';
                if (isset($rs['Record']['AccessID'])) {
                    $rs['Record'] = array(0 => $rs['Record']);
                }
                foreach ($rs['Record'] as $record) {
                    $access .= ',' . $record['AccessID'];
                    $access = trim($access, ',');
                }
                return explode(',', $access);
            }
            return false;
        }
    }

    /*
      --|获取个人博客列表
      --|根据用户ID查博客列表
     */

    function getMemberBlogListByUserID($data) {
        if (empty($data['QryData']) || intval($data['QryData']) <= 0)
            return false;
        $type = 'B050';
        $data['OrderBy'] = isset($data['OrderBy']) ? $data['OrderBy'] : 0;
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : -1;
        $data['Type'] = 4; //根据UserID
        $data['Function'] = 'getMemberBlogListByUserID';

        if (ISCACHE) {

            if ($data['StartNo'] == -1) {
                $ckey = config_item('K1039');
                $ckey = str_replace('{QryData}', $data['QryData'], $ckey);
                $ckey = str_replace('{Type}', $data['Type'], $ckey);
                $rs = $this->cache->get($ckey);
                if (!$rs) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type, $data) && $rs['TtlRecords'] > 0) {
                        $this->cache->set($ckey, $rs['TtlRecords'], EXPIRETIME_1);
                        return $rs['TtlRecords'];
                    } else {
                        return false;
                    }
                }
                return $rs;
            } else {
                $ckey = config_item('K1011');
                $ckey = str_replace('{QryData}', $data['QryData'], $ckey);
                $ckey = str_replace('{Type}', $data['Type'], $ckey);
                $ckey = str_replace('{QryCount}', $data['QryCount'], $ckey);
                $rs = $this->cache->get($ckey);
                if (!$rs) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type, $data)) {
                        $getData['Record'] = $rs['Record'];
                        $getData['RetRecords'] = $rs['RetRecords'];
                        if ($rs['RetRecords'] > 0) {
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
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);

            $getData = array();

            if ($this->_checkrs($rs, $type)) {
                $getData = array();
                if ($data['StartNo'] == -1) {
                    return $rs['TtlRecords'];
                }
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

    /*
      --|通过域名获取博客信息
     */

    function getMemberBlogByDomainName($data) {
        if (empty($data['QryData']))
            return false;
        $type = 'B050';
        $data['OrderBy'] = isset($data['OrderBy']) ? $data['OrderBy'] : 0;
        $data['Type'] = 3; //根据域名
        $data['StartNo'] = 0;
        $data['QryCount'] = 1;
        $data['Function'] = 'getMemberBlogbyDomainName';

        if (ISCACHE) {
            $ckey = config_item('K1010');
            $ckey = str_replace('{QryData}', $data['QryData'], $ckey);
            $ckey = str_replace('{Type}', $data['Type'], $ckey);
            $rs = $this->cache->get($ckey);

            if (!$rs) {
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);
                if ($this->_checkrs($rs, $type, $data) && $rs['RetRecords'] > 0) {
                    $getData = $rs['Record'];
                    $this->cache->set($ckey, $getData, EXPIRETIME_1);
                    unset($rs);
                    return $getData;
                } else {
                    return false;
                }
            }
            return $rs;
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);
            $getData = array();
            if ($this->_checkrs($rs, $type, $data) && $rs['RetRecords'] > 0) {
                $getData = $rs['Record'];
                unset($rs);
                return $getData;
            }
            return false;
        }
    }

    function getMemberBlogByMemberID($data) {
        if (empty($data['QryData']))
            return false;
        $type = 'B050';
        $data['OrderBy'] = isset($data['OrderBy']) ? $data['OrderBy'] : 0;
        $data['Type'] = 6; //根据博客ID
        $data['StartNo'] = 0;
        $data['QryCount'] = 1;
        $data['Function'] = 'getMemberBlogByMemberID';

        if (ISCACHE) {
            $ckey = config_item('K1010');
            $ckey = str_replace('{QryData}', $data['QryData'], $ckey);
            $ckey = str_replace('{Type}', $data['Type'], $ckey);
            $rs = $this->cache->get($ckey);

            if (!$rs) {
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);
                if ($this->_checkrs($rs, $type, $data) && $rs['RetRecords'] > 0) {
                    $getData = $rs['Record'];
                    $this->cache->set($ckey, $getData, EXPIRETIME_1);
                    unset($rs);
                    return $getData;
                } else {
                    return false;
                }
            }
            return $rs;
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);
            $getData = array();
            if ($this->_checkrs($rs, $type, $data) && $rs['RetRecords'] > 0) {
                $getData = $rs['Record'];
                unset($rs);
                return $getData;
            }
            return false;
        }
    }

    /*
      --|更新博客基本信息
     */

    function updateBlogMember($data) {
        $type = 'B002';
        $data['IsPrimary'] = 1; //默认只能注册一个博客时，总是设为默认
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);
        if ($this->_checkrs($rs, $type)) {
            if (ISCACHE) {
                $ckey = config_item('K1010');
                $ckey = str_replace('{QryData}', $data['DomainName'], $ckey);
                $ckey = str_replace('{Type}', '3', $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1008');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1015');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);

                $data['SelfRecommend'] = isset($data['SelfRecommend']) ? $data['SelfRecommend'] : '';
                $ckey = str_replace('{SelfRecommend}', $data['SelfRecommend'], $ckey);
                $ismul = isset($data['IsMultimedia']) ? $data['IsMultimedia'] : '0';
                $ckey = str_replace('{ismul}', $ismul, $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1040');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $ckey = str_replace('{SelfRecommend}', '-1', $ckey);
                $this->cache->delete($ckey);
            }
            return true;
        }
        return false;
    }

    /*
      --|更新博客配置信息
     */

    function updateBlogConfig($data) {
        $type = 'B101';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type)) {
            if (ISCACHE) {
                $ckey = config_item('K1010');
                $ckey = str_replace('{QryData}', $data['DomainName'], $ckey);
                $ckey = str_replace('{Type}', '3', $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1008');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1015');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);

                $data['SelfRecommend'] = isset($data['SelfRecommend']) ? $data['SelfRecommend'] : '';
                $ckey = str_replace('{SelfRecommend}', $data['SelfRecommend'], $ckey);
                $ismul = isset($data['IsMultimedia']) ? $data['IsMultimedia'] : '0';
                $ckey = str_replace('{ismul}', $ismul, $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1040');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $ckey = str_replace('{SelfRecommend}', '-1', $ckey);
                $this->cache->delete($ckey);
            }
            return true;
        }
        return $rs['Code'];
    }

    function updateBlogNickName($data) {
        $type = 'B005';
        $rs = $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);
        if ($this->_checkrs($rs, $type) && $rs['Code'] == '00') {
            return true;
        }
        return false;
    }

    /*
      --|获取个人博客统计信息
     */

    function getMemberBlogStat($data) {
        if (empty($data['MemberIDs']))
            return false;
        $type = 'B060';
        $data['Type'] = 0; //博客于商城分离，所有固定只查博客统计
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;
        $data['QryCount'] = isset($data['QryCount']) ? $data['QryCount'] : 1;

        if (ISCACHE) {
            $ckey = config_item('K1005');
            $ckey = str_replace('{MemberID}', $data['MemberIDs'], $ckey);
            $rs = $this->cache->get($ckey);
            if (!$rs) {
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);
                if ($this->_checkrs($rs, $type, $data)) {
                    if ($rs['RetRecords'] > 0) {
                        $this->cache->set($ckey, $rs['Record'], EXPIRETIME);
                    }
                    return $rs['Record'];
                } else {
                    return false;
                }
            }
            return $rs;
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);
            if ($this->_checkrs($rs, $type)) {
                return $rs['Record'];
            } else {
                return false;
            }
        }
    }

    /**
     * 获取博客访问次数
     *   区别于getMemberBlogStat函数的是，
      该函数使用缓存数据，原因是点击量蛋疼的设计，会先写入缓存，
      缓存定时写入数据库，所以前端读取博客统计的时候，不能每次都从数据库读，不然数据不同步
     */
    function getMemberBlogStatByCache($param) {
        $ckey = config_item('K1071');
        $ckey = str_replace('{MemberID}', $param['MemberIDs'], $ckey);
        $result = $this->cache->get($ckey);

        return $result;
    }

    /*
      --|获取个人博客统60日点击明细
      --|只有博主才能访问的页面，关闭缓存
     */

    function getMemberBlogStatDetail($data) {
        $type = 'B340';
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;
        $data['QryCount'] = isset($data['QryCount']) ? $data['QryCount'] : 60;
        $timeint = strtotime($data['AccessBegin']); //转成时间戳，做为缓存key

        if (ISCACHE) {//关闭缓存
            $ckey = config_item('K1006');
            $ckey = str_replace('{MemberID}', $data['MemberIDs'], $ckey);
            $ckey = str_replace('{AccessBegin}', $timeint, $ckey);
            $this->cache->get($ckey);
            if (!$rs) {

                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);
                if ($this->_checkrs($rs, $type) && isset($rs['Record'])) {
                    if ($rs['RetRecords'] == 1) {
                        $rs['Record'] = array(0 => $rs['Record']);
                    }
                    $this->cache->set($ckey, $rs['Record']);
                    return $rs['Record'];
                }
                return false;
            }
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);
            if ($this->_checkrs($rs, $type) && isset($rs['Record'])) {
                if ($rs['RetRecords'] == 1) {
                    $rs['Record'] = array(0 => $rs['Record']);
                }
                return $rs['Record'];
            }
            return false;
        }
    }

    /*
      --|获取个人博客文章日期归档信息

      function getArchiveList($data)
      {
      if(isset($data['MemberID']))
      return false;

      $type = 'B230';
      $data['StatDate'] = isset($data['StatDate'])? $data['StatDate']:0;
      if(ISCACHE)
      {
      $ckey  = config_item('K1007');
      $ckey  = str_replace('{MemberID}',$data['MemberID'],$ckey);
      $ckey  = str_replace('{Type}',$data['Type'],$ckey);
      $ckey  = str_replace('{StatDate}',$data['StatDate'],$ckey);
      $rs    = $this->cache->get($ckey);

      if(!$rs){
      $rs = $this->socket['newblog']->senddata($type, $data);
      $rs = xmltoarray($rs);
      $getData = array();
      if($this->_checkrs($rs, $type))
      {
      $getData = $rs['Record'];
      $this->cache->set($ckey, $getData);
      return $getData;
      }
      else
      {
      return false;
      }

      }
      return $rs;
      }
      else
      {
      $rs = $this->socket['newblog']->senddata($type, $data);
      $rs = xmltoarray($rs);
      $getData = array();
      if($this->_checkrs($rs, $type))
      {
      $getData = $rs['Record'];
      }
      return $getData;
      }
      } */

    /*
      --|获取博客配置信息
     */

    function getBlogConfig($data) {
        $type = 'B100';
        if (ISCACHE) {
            $ckey = config_item('K1008');
            $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);

            $rs = $this->cache->get($ckey);
            //error_log(print_r($rs,true), 3, '/home/httpd/logs/a23132.log');
            if (!$rs) {
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);
                if ($this->_checkrs($rs, $type) && isset($rs['Record'])) {
                    $this->cache->set($ckey, $rs['Record'], EXPIRETIME_1);
                    return $rs['Record'];
                }
                return false;
            }
            return $rs;
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);

            if ($this->_checkrs($rs, $type) && isset($rs['Record'])) {
                return $rs['Record'];
            }
            return false;
        }
    }

    /*
      --|新增个人博客
     */

    function addNewBlog($data) {
        $type = 'B001';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type)) {
            if (ISCACHE) {
                $ckey = config_item('K1011');
                $ckey = str_replace('{QryData}', $data['UserID'], $ckey);
                $ckey = str_replace('{Type}', '4', $ckey);
                $this->cache->delete($ckey);
            }
            return $rs['Record']['MemberID'];
        }
        return false;
    }

    /*
      --|更新博客組別關係
     */

    function setMemberGroup($data) {
        $type = 'B004';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type) && $rs['TtlRecords'] > 0) {
            return true;
        }
        return false;
    }

    /*
      --|获取博客公告信息
     */

    function getBlogAffiche($data) {
        $type = 'B020';
        $data['Status'] = isset($data['Status']) ? $data['Status'] : '0';
        if ($data['Status'] == "") {
            $data['Status'] = '0';
        }

        if (ISCACHE) {
            $ckey = config_item('K1012');
            $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
            $rs = $this->cache->get($ckey);

            if (!$rs) {
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);
                $getData = array();
                if ($this->_checkrs($rs, $type)) {
                    if (isset($rs['Record'][0])) {
                        $getData = $rs['Record'][0];
                    }
                    if (isset($rs['Record'])) {
                        $getData = $rs['Record'];
                    }
                    $this->cache->set($ckey, $getData, EXPIRETIME_1);
                    return $getData;
                } else {
                    return false;
                }
            }
            return $rs;
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);

            if ($this->_checkrs($rs, $type)) {
                if (isset($rs['Record'][0]))
                    return $rs['Record'][0];
                if (isset($rs['Record']))
                    return $rs['Record'];
                return false;
            }
            return false;
        }
    }

    /*
      --|增修博客公告信息
     */

    function aupdBlogAffiche($data) {
        $type = 'B021';
        if (!isset($data['MemberID']))
            return false;
        if (!isset($data['Status']) || $data['Status'] == "") {
            $data['Status'] = '0';
        }
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type, $data) && $rs['TtlRecords'] > 0) {
            if (ISCACHE) {
                $ckey = config_item('K1012');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->set($ckey, '');

                $ckey = config_item('K2034');
                $ckey = str_replace('{memberid}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);
            }
            return true;
        }
        return false;
    }

    /*
      --|检查博客名和域名是否可用
      --|true 不可用
     */

    function checkRegInfo($data) {
        $type = 'B053';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type, $data) && $rs['RetRecords'] > 0) {
            return true;
        }
        return false;
    }

    /*
      --|通过memberid获取博客公告
     */

    function getMemberAfficheByMid($data) {
        if (trim($data['Content']) == '')
            return '';


        if (ISCACHE) {
            $ckey = config_item('K2034');
            $ckey = str_replace('{memberid}', $data['memberid'], $ckey);

            $rs = $this->cache->get($ckey);

            if (!$rs) {
                $getData = affichePicture(array('Content' => $data['Content']));
                $this->cache->set($ckey, $getData, EXPIRETIME_1);

                return $getData;
            }
            return $rs;
        } else {
            return affichePicture(array('Content' => $data['Content']));
        }
    }

}

//end class
?>