<?php

/*
  --|author leicc
  —-|频道页文章列表的获取
  --|包括获取推荐文章以及标签文章的获取
  --|modify  2011-8-24  lifeng
 */

class Channel_socket extends MY_Model {

    function Channel_socket() {
        parent::MY_Model();
    }

    /*
      --|标签文章列表的获取
     */

    function getSyaTagArticleList($data) {

        $type = 'B250';
        $data['ShowMode'] = isset($data['ShowMode']) ? $data['ShowMode'] : 0;
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : -1;
        $data['QryCount'] = isset($data['QryCount']) ? $data['QryCount'] : 60;
        $data['IsPrime'] = isset($data['IsPrime']) ? $data['IsPrime'] : 0;
        $data['TagID'] = isset($data['TagID']) ? $data['TagID'] : 0;

        if (ISCACHE) {
            /* 配置文件固定页数，所以不取总数，直接根据参数取文章 */

            $ckey = config_item('K1041');
            $ckey = str_replace('{IsPrime}', $data['IsPrime'], $ckey);
            $ckey = str_replace('{TagID}', $data['TagID'], $ckey);
            $ckey = str_replace('{PageNo}', ($data['StartNo'] / $data['QryCount'] + 1), $ckey);

            $rs = $this->cache->get($ckey);

            if (!$rs) {
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);
                if ($this->_checkrs($rs, $type)) {
                    if ($rs['RetRecords'] == 1) {
                        $rs['Record'] = array(0 => $rs['Record']);
                    }
                    $this->cache->set($ckey, $rs, EXPIRETIME_1);
                    return $rs;
                }
                return false;
            }
            return $rs;
        } else {

            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);

            if ($this->_checkrs($rs, $type)) {
                if ($data['StartNo'] == -1) {
                    return $rs['TtlRecords'];
                }
                if ($rs['RetRecords'] == 1) {
                    $rs['Record'] = array(0 => $rs['Record']);
                }
                return $rs;
            }
            return false;
        }
    }

    /*
      --|按推荐值获取文章列表
     */

    function getRecommendArticle($data) {
        $type = 'B247';
        $data['ShowMode'] = isset($data['ShowMode']) ? $data['ShowMode'] : 0;
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;
        $data['QryCount'] = isset($data['QryCount']) ? $data['QryCount'] : 60;

        if (ISCACHE) {
            /* 配置文件固定页数，所以不取总数，直接根据参数取文章 */

            $ckey = config_item('K1044');
            $ckey = str_replace('{Recomend}', $data['Recomend'], $ckey);
            $ckey = str_replace('{PageNo}', ($data['StartNo'] / $data['QryCount'] + 1), $ckey);
            $rs = $this->cache->get($ckey);

            if (!$rs) {
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type)) {
                    if ($rs['RetRecords'] == 1) {
                        $rs['Record'] = array(0 => $rs['Record']);
                    }
                    $this->cache->set($ckey, $rs, EXPIRETIME_1);
                    return $rs;
                }
                return false;
            }
            return $rs;
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);

            if ($this->_checkrs($rs, $type)) {
                if ($data['StartNo'] == -1) {
                    return $rs['TtlRecords'];
                }
                if ($rs['RetRecords'] == 1) {
                    $rs['Record'] = array(0 => $rs['Record']);
                }
            }
            return $rs;
        }
    }

    /*
      --|获取首页用户推荐的文章列表，需要后台审核过之后才能取
     */

    function getUserRecommendArticle($data) {
        $type = 'B326';
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : -1;
        $data['QryCount'] = isset($data['QryCount']) ? $data['QryCount'] : 60;

        if (ISCACHE) {
            if ($data['StartNo'] == -1) {
                $ckey = config_item('K1046');
                $rs = $this->cache->get($ckey);
                if (!$rs) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type)) {
                        $rs['FlagCode'] = md5($rs['TtlRecords'] . time());
                        $this->cache->set($ckey, $rs, EXPIRETIME_1);
                        return $rs;
                    }
                    return 0;
                }
                return $rs;
            } else {
                $ckey = config_item('K1047');
                $ckey = str_replace('{PageNo}', ($data['StartNo'] / $data['QryCount'] + 1), $ckey);
                $rs = $this->cache->get($ckey);
                if (!$rs || $data['FlagCode'] != $rs['FlagCode']) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type)) {
                        if ($rs['RetRecords'] == 1) {
                            $rs['Record'] = array(0 => $rs['Record']);
                        }
                        $temp['Record'] = isset($rs['Record']) ? $rs['Record'] : array();
                        $temp['FlagCode'] = $data['FlagCode'];
                        $this->cache->set($ckey, $temp, EXPIRETIME_1);
                        return $temp;
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
                if ($rs['RetRecords'] == 1) {
                    $rs['Record'] = array(0 => $rs['Record']);
                }
                $temp = isset($rs['Record']) ? $rs['Record'] : array();
                return $temp;
            }
            return 0;
        }
    }

    /*
      --|首页博客搜索
     */

    function getSearchBlog($data) {
        //	var_dump($data);
        $type = 'B051';
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : -1;
        $data['QryCount'] = isset($data['QryCount']) ? $data['QryCount'] : 50;
        $rs = $this->socket['newblog']->senddata($type, $data);

        $rs = xmltoarray($rs);
        //var_dump($rs);
        if ($this->_checkrs($rs, $type)) {
            if ($data['StartNo'] == -1) {
                return $rs['TtlRecords'];
            }
            if ($rs['RetRecords'] == 1) {
                $rs['Record'] = array(0 => $rs['Record']);
            }
            return isset($rs['Record']) ? $rs['Record'] : array();
        }
        return 0;
    }

    /*
      --|首页标签文章搜索
     */

    function getSearchTagArticle($data) {
        $type = 'B218';
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : -1;
        $data['QryCount'] = isset($data['QryCount']) ? $data['QryCount'] : 50;
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type)) {
            if ($data['StartNo'] == -1) {
                return $rs['TtlRecords'];
            }
            if ($rs['RetRecords'] == 1) {
                $rs['Record'] = array(0 => $rs['Record']);
            }
            return isset($rs['Record']) ? $rs['Record'] : array();
        }
        return 0;
    }

    /*
      --|首页标签文章搜索
     */

    function getHotTopicArticleList($data) {
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : -1;
        $dbinc = config_item('hw');
        static $link = NULL;
        if (!is_resource($link)) {
            $link = mysql_connect($dbinc['server'], $dbinc['username'], $dbinc['password']) or die(mysql_error());
            mysql_select_db($dbinc['dbname'], $link);
            mysql_query("set names {$dbinc['charset']}");
        }
        if ($data['StartNo'] == -1) {
            $SQL = "select count(id) as num from hwitem where bid=64";
            $rs = mysql_query($SQL);
            $row = mysql_fetch_row($rs);
            mysql_free_result($rs);
            return intval($row[0]);
        } else {
            $return = array();
            $SQL = "select * from hwitem where bid=64 order by id desc limit {$data['StartNo']},{$data['QryCount']}";
            $rs = mysql_query($SQL);
            while ($row = mysql_fetch_array($rs, MYSQL_ASSOC)) {
                $return[] = $row;
            }
            mysql_free_result($rs);
            mysql_close($link);
            $link = NULL;
            return $return;
        }
    }

//多空调查
    function duoKong($sign) {
        $ckey = config_item('K1046');
        $ckey = 'duokongdiaocha';
        $rs = $this->cache->get($ckey);

        if (!$rs) {
            $rs = array('date' => strtotime(date('Ymd')), 'kanduo' => 0, 'kankong' => 0, 'panzheng' => 0);
            if (!empty($sign)) {
                $rs[$sign] = $rs[$sign] + 1;
            }
            $this->cache->set($ckey, $rs, EXPIRETIME_5);
            return $rs;
        }

        $nowtime = strtotime(date('Ymd'));
        if ($rs['date'] < $nowtime && date('G') >= 6) {
            $rs = array('date' => $nowtime, 'kanduo' => 0, 'kankong' => 0, 'panzheng' => 0);
            if (!empty($sign)) {
                $rs[$sign] = $rs[$sign] + 1;
            }
            $this->cache->set($ckey, $rs, EXPIRETIME_5);
            return $rs;
        }

        if (!empty($sign)) {
            $rs[$sign] = $rs[$sign] + 1;
        }

        $this->cache->set($ckey, $rs, EXPIRETIME_5);

        return $rs;
    }

//获取黄金博客页面第一次打开时显示的13条微博
    function getWeiBo($url) {
        $ckey = 'goldengetweibofirst';
        $rs = $this->cache->get($ckey);
        if (!$rs) {
            $result = curl_post($url);
            $this->cache->set($ckey, $result, EXPIRETIME_3);
            return $result;
        } else {
            return $rs;
        }
    }

}

//end class
?>