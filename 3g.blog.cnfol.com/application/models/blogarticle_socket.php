<?php

/*
  --|author leicc
  —-|个人博客的相关操作
  --|包括获取个人博客列表、添加个人博客
  --|合法性检查 以及获取个人博客信息
  --|modify  2011-8-23  lifeng
 */

class Blogarticle_socket extends MY_Model {

    function Blogarticle_socket() {
        parent::MY_Model();
    }

    //获取文章访客记录
    function getArticleVisitor($ArticleID) {
        $type = 'B375';
        $data['ArticleIDs'] = $ArticleID;
        $data['StartNo'] = 0;
        $data['QryCount'] = 1;
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs)) {
            $rs = (isset($rs['Record']) && $rs['Record'] != false) ? $rs['Record'] : array();
            return $rs;
        }
        return false;
    }

    //更新文章访客记录
    function setArticleVisitor($data) {
        $type = 'B376';
        $rs = $this->socket['newblog']->senddata($type, $data);

        $rs = xmltoarray($rs);
        if ($this->_checkrs($rs)) {
            //清空缓存
            $this->cache->delete('visitor_article_' . $data['ArticleID']);
            return $rs;
        }
        return false;
    }

    /*
      --|获取个人博客文章列表
      --|缓存key：1015,1016
     */

    function getMemberArticleList($data) {
        if (empty($data['MemberID']))
            return false;
        $type = 'B215';

        $data['IsSummary'] = isset($data['IsSummary']) ? $data['IsSummary'] : '1';
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;
        $data['QryCount'] = isset($data['QryCount']) ? $data['QryCount'] : 10;
        $StartDate = isset($data['StartDate']) ? date("Ymd", strtotime($data['StartDate'])) : '';
        $EndDate = isset($data['EndDate']) ? date("Ymd", strtotime($data['EndDate'])) : '';

        $ismul = isset($data['IsMultimedia']) ? $data['IsMultimedia'] : '0';
        $data['SelfRecommend'] = isset($data['SelfRecommend']) ? $data['SelfRecommend'] : '';

        if (ISCACHE) {
            if ($data['StartNo'] == -1) {

                if ($ismul == 1) {
                    $key = 'ismul_1';
                } else if ($ismul == 2) {
                    $key = 'ismul_2';
                } else if ($data['SelfRecommend'] != '') {
                    $key = 'Recommend';
                } else if ($data['IsTop'] != '') {
                    $key = 'istop';
                } else {
                    $key = 'all';
                }

                $ckey = config_item('K1015');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $rs = $this->cache->get($ckey);

                if (!$rs) {
                    if ($data['SelfRecommend'] == '') {
                        unset($data['SelfRecommend']);
                    }
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);

                    if ($this->_checkrs($rs, $type, $data)) {
                        $getData = array();
                        $getData[$key] = isset($rs['TtlRecords']) ? $rs['TtlRecords'] : 0;
                        $getData['FlagCode'] = md5(time());
                        $getData['UTopCnt'] = $rs['Record']['UTopCnt'];

                        if ((isset($getData[$key]) && $getData[$key] > 0) || (isset($getData['UTopCnt']) && $getData['UTopCnt'] > 0)) {
                            $this->cache->set($ckey, $getData, EXPIRETIME_1);
                        }
                        return $getData;
                    } else {
                        return false;
                    }
                } else if (!$rs[$key]) {
                    if ($data['SelfRecommend'] == '') {
                        unset($data['SelfRecommend']);
                    }
                    $rs2 = $this->socket['newblog']->senddata($type, $data);
                    $rs2 = xmltoarray($rs2);
                    if ($this->_checkrs($rs2, $type, $data)) {

                        $rs[$key] = isset($rs2['TtlRecords']) ? $rs2['TtlRecords'] : 0;

                        if (isset($rs[$key]) && $rs[$key] > 0) {
                            $this->cache->set($ckey, $rs, EXPIRETIME_1);
                        }
                        return $rs;
                    } else {
                        return false;
                    }
                }

                return $rs;
            } else {
                $ckey = config_item('K1016');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $ckey = str_replace('{PageNo}', ($data['StartNo'] / $data['QryCount'] + 1), $ckey);
                $ckey = str_replace('{StartDate}', $StartDate, $ckey);
                $ckey = str_replace('{EndDate}', $EndDate, $ckey);

                $ckey = str_replace('{SelfRecommend}', $data['SelfRecommend'], $ckey);
                $ckey = str_replace('{ismul}', $ismul, $ckey);
                $ckey = str_replace('{istop}', $data['IsTop'], $ckey);

                $rs = $this->cache->get($ckey);

                if (!$rs || ($data['FlagCode'] != $rs['FlagCode'])) {

                    $getData = array();
                    if ($data['SelfRecommend'] == '') {
                        unset($data['SelfRecommend']);
                    }
                    $rs = $this->socket['newblog']->senddata($type, $data);

                    $rs = xmltoarray($rs);

                    $rs['RetRecords'] = $this->returnRecords($rs['RetRecords'], $rs['Record']);

                    if ($this->_checkrs($rs, $type, $data)) {
                        $getData['RetRecords'] = 0;
                        if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
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
            if ($data['SelfRecommend'] == '') {
                unset($data['SelfRecommend']);
            }

            if ($data['StartNo'] == -1) {
                if ($ismul == 1) {
                    $key = 'ismul_1';
                } else if ($ismul == 2) {
                    $key = 'ismul_2';
                } else if ($data['SelfRecommend'] != '') {
                    $key = 'Recommend';
                } else if ($data['IsTop'] != '') {
                    $key = 'istop';
                } else {
                    $key = 'all';
                }


                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type)) {
                    $rs[$key] = $rs['TtlRecords'];

                    return $rs;
                }
            } else {

                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                //error_log(print_r($data,true).'|'.print_r($rs,true), 3, '/home/httpd/logs/a23132.log');
                $rs['RetRecords'] = $this->returnRecords($rs['RetRecords'], $rs['Record']);

                $getData = array();
                if ($this->_checkrs($rs, $type)) {
                    $getData['RetRecords'] = 0;
                    if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
                        $getData['RetRecords'] = $rs['RetRecords'];
                        $getData['Record'] = $rs['Record'];
                    }
                }
                unset($rs);

                return $getData;
            }
        }
    }

    function returnRecords($RetRecords, $Record) {
        if (empty($Record['0'])) {
            $Record = array('0' => $Record);
        }

        if ($RetRecords > 0) {
            return $RetRecords;
        } else if (!empty($Record)) {
            return count($Record);
        } else {
            return 0;
        }
    }

    /**
     * 获取草稿箱列表
     * @param type $data
     * @return boolean
     */
    function getMemberDraftboxList($data) {
        if (empty($data['MemberID']))
            return false;
        $type = 'B215';
        $data['property'] = 5;
        if (ISCACHE) {
            $ckey = config_item('K2020');
            $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
            $rs = $this->cache->get($ckey);
            if (!$rs) {
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);
                if ($this->_checkrs($rs, $type, $data)) {
                    $getData = array();
                    $getData['TtlRecords'] = isset($rs['TtlRecords']) ? $rs['TtlRecords'] : 0;
                    $getData['FlagCode'] = md5($getData['TtlRecords'] . time());
                    $getData['UTopCnt'] = $rs['RetRecords'];
                    $getData['Record'] = $rs['Record'];

                    if ((isset($getData['TtlRecords']) && $getData['TtlRecords'] > 0) || (isset($getData['UTopCnt']) && $getData['UTopCnt'] > 0)) {
                        $this->cache->set($ckey, $getData, EXPIRETIME_1);
                    }
                    return $getData;
                } else {
                    return false;
                }
            }
            return $rs;
        } else {
            if ($data['SelfRecommend'] == '') {
                unset($data['SelfRecommend']);
            }
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);

            $getData = array();
            if ($this->_checkrs($rs, $type)) {
                $getData['RetRecords'] = 0;
                if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
                    $getData['RetRecords'] = $rs['RetRecords'];
                    $getData['Record'] = $rs['Record'];
                }
            }
            unset($rs);

            return $getData;
        }
    }

    /*
      --|获取个人博客首页文章列表和个人推荐文章
     */

    function getMemberArticleListIndex($data) {
        if (empty($data['MemberID']))
            return false;
        $type = 'B215';

        $data['IsSummary'] = isset($data['IsSummary']) ? $data['IsSummary'] : '1';
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;
        $data['QryCount'] = isset($data['QryCount']) ? $data['QryCount'] : 10;
        $data['SelfRecommend'] = isset($data['SelfRecommend']) ? $data['SelfRecommend'] : -1;

        if (ISCACHE) {
            $ckey = config_item('K1040');
            $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
            $ckey = str_replace('{SelfRecommend}', $data['SelfRecommend'], $ckey);

            $rs = $this->cache->get($ckey);

            if (!$rs) {
                $getData = array();
                if ($data['SelfRecommend'] == '') {
                    unset($data['SelfRecommend']);
                }

                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);
                if ($this->_checkrs($rs, $type)) {
                    $getData['RetRecords'] = 0;
                    if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
                        $getData['RetRecords'] = $rs['RetRecords'];
                        $getData['Record'] = $rs['Record'];
                        $this->cache->set($ckey, $getData, EXPIRETIME_1);
                    }

                    unset($rs);
                    return $getData;
                }
                return false;
            }
            return $rs;
        } else {
            if ($data['SelfRecommend'] == '') {
                unset($data['SelfRecommend']);
            }
            if ($data['StartNo'] == -1) {
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type)) {
                    echo $rs['TtlRecords'];
                    return isset($rs['TtlRecords']) ? $rs['TtlRecords'] : 0;
                }
            } else {
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                $getData = array();
                if ($this->_checkrs($rs, $type)) {
                    $getData['RetRecords'] = 0;
                    if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
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
      --|根据栏目分类获得文章列表
     */

    function getMemberArticleListSort($data) {

        if (empty($data['MemberID']))
            return false;
        $type = 'B215';

        $data['IsSummary'] = isset($data['IsSummary']) ? $data['IsSummary'] : '1';
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;
        $data['QryCount'] = isset($data['QryCount']) ? $data['QryCount'] : 10;

        $ismul = isset($data['IsMultimedia']) ? $data['IsMultimedia'] : '0';
        $data['SelfRecommend'] = isset($data['SelfRecommend']) ? $data['SelfRecommend'] : '';

        $cachekey['SortID'] = isset($data['SortID']) ? $data['SortID'] : '';
        $cachekey['StartDate'] = isset($data['StartDate']) ? $data['StartDate'] : '';
        if ($cachekey['StartDate'] != '') {
            $cachekey['StartDate'] = date('Y-m-d', strtotime($cachekey['StartDate']));
        }

        if (ISCACHE) {
            if ($data['StartNo'] == -1) {
                $ckey = config_item('K1042');

                if ($ismul == 1) {
                    $key = 'ismul_1';
                } else if ($ismul == 2) {
                    $key = 'ismul_2';
                } else if ($data['SelfRecommend'] != '') {
                    $key = 'Recommend';
                } else if ($data['IsTop'] != '') {
                    $key = 'istop';
                } else {
                    $key = 'all';
                }

                $key = $cachekey['StartDate'] . $cachekey['SortID'] . $key;

                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);

                $rs = $this->cache->get($ckey);
                //error_log(print_r($rs,true), 3, '/home/httpd/logs/a1.log');
                if (!$rs) {

                    if ($data['SelfRecommend'] == '') {
                        unset($data['SelfRecommend']);
                    }
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type)) {
                        $getData = array();
                        $getData[$key] = isset($rs['TtlRecords']) ? $rs['TtlRecords'] : 0;
                        $getData['FlagCode'] = md5(time());
                        $getData['UTopCnt'] = $rs['Record']['UTopCnt'];
                        if ((isset($getData[$key]) && $getData[$key] > 0) || (isset($getData['UTopCnt']) && $getData['UTopCnt'] > 0)) {
                            $this->cache->set($ckey, $getData, EXPIRETIME_1);
                        }
                        return $getData;
                    } else {
                        return false;
                    }
                } else if (!$rs[$key]) {
                    if ($data['SelfRecommend'] == '') {
                        unset($data['SelfRecommend']);
                    }
                    $rs2 = $this->socket['newblog']->senddata($type, $data);
                    $rs2 = xmltoarray($rs2);
                    if ($this->_checkrs($rs2, $type, $data)) {

                        $rs[$key] = isset($rs2['TtlRecords']) ? $rs2['TtlRecords'] : 0;

                        if (isset($rs[$key]) && $rs[$key] > 0) {
                            $this->cache->set($ckey, $rs, EXPIRETIME_1);
                        }
                        return $rs;
                    } else {
                        return false;
                    }
                }
                return $rs;
            } else {

                $ckey = config_item('K1043');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $ckey = str_replace('{PageNo}', ($data['StartNo'] / $data['QryCount'] + 1), $ckey);
                $ckey = str_replace('{SortID}', $data['SortID'], $ckey);

                $ckey = str_replace('{SelfRecommend}', $data['SelfRecommend'], $ckey);
                $ckey = str_replace('{ismul}', $ismul, $ckey);
                $ckey = str_replace('{istop}', $data['IsTop'], $ckey);

                $ckey = str_replace('{kind}', $cachekey['StartDate'] . $cachekey['SortID'], $ckey);

                $rs = $this->cache->get($ckey);


                if (!$rs || ($data['FlagCode'] != $rs['FlagCode'])) {

                    $getData = array();
                    if ($data['SelfRecommend'] == '') {
                        unset($data['SelfRecommend']);
                    }
                    $rs = $this->socket['newblog']->senddata($type, $data);

                    $rs = xmltoarray($rs);

                    $rs['RetRecords'] = $this->returnRecords($rs['RetRecords'], $rs['Record']);

                    if ($this->_checkrs($rs, $type)) {
                        $getData['RetRecords'] = 0;
                        if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
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

            if ($data['SelfRecommend'] == '') {
                unset($data['SelfRecommend']);
            }
            if ($data['StartNo'] == -1) {
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);


                if ($ismul == 1) {
                    $key = 'ismul_1';
                } else if ($ismul == 2) {
                    $key = 'ismul_2';
                } else if ($data['SelfRecommend'] != '') {
                    $key = 'Recommend';
                } else if ($data['IsTop'] != '') {
                    $key = 'istop';
                } else {
                    $key = 'all';
                }

                $key = $cachekey['StartDate'] . $cachekey['SortID'] . $key;

                if ($this->_checkrs($rs, $type)) {
                    $rs[$key] = $rs['TtlRecords'];
                    return $rs;
                }
            } else {

                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                $rs['RetRecords'] = $this->returnRecords($rs['RetRecords'], $rs['Record']);

                $getData = array();
                if ($this->_checkrs($rs, $type)) {
                    $getData['RetRecords'] = 0;
                    if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
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
      --|获取个人博客评论文章
     */

    function getCommentArticleList($data) {
        $type = 'B300';
        $data['OrderBy'] = isset($data['OrderBy']) ? $data['OrderBy'] : 1;
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;
        $data['QryCount'] = isset($data['QryCount']) ? $data['QryCount'] : 10;
        if (ISCACHE) {
            if ($data['StartNo'] == -1) {
                $ckey = config_item('K1061');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $rs = $this->cache->get($ckey);
                if (!$rs) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type)) {
                        $rs['FlagCode'] = md5($rs['TtlRecords'] . time());
                        $this->cache->set($ckey, $rs, EXPIRETIME_1);
                        return $rs;
                    } else {
                        return false;
                    }
                }
                return $rs;
            } else {
                $ckey = config_item('K1062');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $ckey = str_replace('{PageNo}', ($data['StartNo'] / $data['QryCount'] + 1), $ckey);
                $rs = $this->cache->get($ckey);
                if (!$rs || ($data['FlagCode'] != $rs['FlagCode'])) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type)) {
                        if ($rs['RetRecords'] > 0) {
                            $rs['Record'] = ($rs['RetRecords'] == 1) ? array(0 => $rs['Record']) : $rs['Record'];
                            $rs['FlagCode'] = $data['FlagCode'];
                            $this->cache->set($ckey, $rs, EXPIRETIME_1);
                        }
                        return $rs;
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
                    return (isset($rs['TtlRecords']) ? $rs['TtlRecords'] : 0);
                }
                if ($rs['RetRecords'] > 0) {
                    $rs['Record'] = ($rs['RetRecords'] == 1) ? array(0 => $rs['Record']) : $rs['Record'];
                }
            }
            return ( isset($rs['Record']) ? $rs['Record'] : false );
        }
    }

    /*
      --|修改个人博客文章
     */

    function modBlogArticle($data, $tmp = '') {
        $type = 'B242';
//        print_r($data);
        $StartDate = isset($data['StartDate']) ? date("Ymd", strtotime($data['StartDate'])) : '';
        $EndDate = isset($data['EndDate']) ? date("Ymd", strtotime($data['EndDate'])) : '';
        $rs = $this->socket['newblog']->senddata($type, $data);

        $rs = xmltoarray($rs);
//        var_dump($rs);
        if ($this->_checkrs($rs, $type) && $rs['TtlRecords'] > 0) {
            if (ISCACHE) {
                //编辑文章，先生成缓存，解决读写库不同步问题
                $ckey = config_item('K1014');
                $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $oArt = $this->cache->get($ckey);

                if ($oArt && $tmp) {
                    $oArt['Title'] = $data['Title'];
                    $oArt['Content'] = $tmp['Content_c'];
                    $oArt['Summary'] = $tmp['Summary_c'];
                    $oArt['Property'] = $data['Property'];
                    $oArt['ReadStatus'] = $data['ReadStatus'];
                    $oArt['SysTagID'] = $data['SysTagID'];
                    $oArt['SortID'] = $data['SortID'];
                    $oArt['SelfRecommend'] = $data['SelfRecommend'];
                    $oArt['ArticleType'] = 'edit';
                    $this->cache->set($ckey, $oArt, 60);
                } else {
                    $this->cache->delete($ckey);
                }

                $ckey = config_item('K1017');
                $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1015');
                //$ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                //$ckey = str_replace('{StartDate}', $StartDate, $ckey);
                //$ckey = str_replace('{EndDate}', $EndDate, $ckey);
                //$data['SelfRecommend'] = ($data['SelfRecommend']!='0')?$data['SelfRecommend']:'';
                //$ckey_all=str_replace('{SelfRecommend}','',$ckey);//删除全部文章memcache
                //$ckey = str_replace('{SelfRecommend}',$data['SelfRecommend'],$ckey);
                //$ismul = isset($data['IsMultimedia'])?$data['IsMultimedia']:'0';
                //$ckey_all=str_replace('{ismul}','0',$ckey_all);//删除全部文章memcache
                //$this->cache->delete($ckey_all);//删除全部文章memcache
                //$ckey = str_replace('{ismul}',$ismul,$ckey);
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1040');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $ckey = str_replace('{SelfRecommend}', '-1', $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1005');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $ckey = str_replace('{Type}', 0, $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1042');
                /*
                  $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                  $ckey = str_replace('{SortID}', $data['SortID'], $ckey);

                  $data['SelfRecommend'] = ($data['SelfRecommend']!='0')?$data['SelfRecommend']:'';

                  $ckey_all=str_replace('{SelfRecommend}','',$ckey);//删除全部文章memcache

                  $ckey = str_replace('{SelfRecommend}',$data['SelfRecommend'],$ckey);
                  $ismul = isset($data['IsMultimedia'])?$data['IsMultimedia']:'0';

                  $ckey_all=str_replace('{ismul}','0',$ckey_all);//删除全部文章memcache
                  $this->cache->delete($ckey_all);//删除全部文章memcache

                  $ckey = str_replace('{ismul}',$ismul,$ckey);
                 */
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);
            }
            return $rs;
        }
        return false;
    }

    /*
      --|增加个人博客文章
     */

    function addBlogArticle($data, $tmp) {
        $type = 'B242';
        $StartDate = isset($data['StartDate']) ? date("Ymd", strtotime($data['StartDate'])) : '';
        $EndDate = isset($data['EndDate']) ? date("Ymd", strtotime($data['EndDate'])) : '';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);
        // var_dump($data);
        // var_dump($rs);
        if ($this->_checkrs($rs, $type) && $rs['TtlRecords'] > 0) {
            $nData = $rs['Record'];

            if (ISCACHE) {
                //新增文章，先生成缓存，解决读写库不同步问题
                $ckey = config_item('K1014');
                $ckey = str_replace('{ArticleID}', $nData['ArticleID'], $ckey);
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);

                $result = array_merge($data, $nData);
                $result['Content'] = $tmp['Content_c'];
                $result['Summary'] = $tmp['Summary_c'];
                $result['ArticleType'] = 'add';
                $this->cache->set($ckey, $result, 60);

                $ckey = config_item('K1017');
                $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1015');
                /*
                  $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                  $ckey = str_replace('{StartDate}', $StartDate, $ckey);
                  $ckey = str_replace('{EndDate}', $EndDate, $ckey);

                  $data['SelfRecommend'] = ($data['SelfRecommend']!='0')?$data['SelfRecommend']:'';

                  $ckey_all=str_replace('{SelfRecommend}','',$ckey);//删除全部文章memcache

                  $ckey = str_replace('{SelfRecommend}',$data['SelfRecommend'],$ckey);
                  $ismul = isset($data['IsMultimedia'])?$data['IsMultimedia']:'0';

                  $ckey_all=str_replace('{ismul}','0',$ckey_all);//删除全部文章memcache
                  $this->cache->delete($ckey_all);//删除全部文章memcache

                  $ckey = str_replace('{ismul}',$ismul,$ckey);
                  error_log(print_r($ckey,true).'\r\n', 3, '/home/httpd/logs/aguesteverbrowse.log');
                 */
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1040');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $ckey = str_replace('{SelfRecommend}', '-1', $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1005');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $ckey = str_replace('{Type}', 0, $ckey);

                $this->cache->delete($ckey);

                $this->_setArticleSortArtNum($data);

                $ckey = config_item('K1042');
                /*
                  $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                  $ckey = str_replace('{SortID}', $data['SortID'], $ckey);

                  $data['SelfRecommend'] = ($data['SelfRecommend']!='0')?$data['SelfRecommend']:'';

                  $ckey_all=str_replace('{SelfRecommend}','',$ckey);//删除全部文章memcache

                  $ckey = str_replace('{SelfRecommend}',$data['SelfRecommend'],$ckey);
                  $ismul = isset($data['IsMultimedia'])?$data['IsMultimedia']:'0';

                  $ckey_all=str_replace('{ismul}','0',$ckey_all);//删除全部文章memcache
                  $this->cache->delete($ckey_all);//删除全部文章memcache

                  $ckey = str_replace('{ismul}',$ismul,$ckey);

                 */
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);
            }
            return $rs;
        } else {
            return false;
        }
    }

    //新增文章时取缓存，增加分类下文章数
    function _setArticleSortArtNum($data) {
        $ckey = config_item('K1023');
        $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
        $tmp = $this->cache->get($ckey);
        if ($tmp) {
            for ($i = 0; $i < count($tmp['Record']); $i++) {
                if ($tmp['Record'][$i]['SortID'] == $data['SortID']) {
                    $tmp['Record'][$i]['ArticleCount'] += 1;
                }
            }
            $this->cache->set($ckey, $tmp, 300);
        }
    }

    /*
      --|删除个人博客文章
      --|同步关联缓存：1014，1017，1013
     */

    function delBlogArticle($data) {
        $type = 'B244';
        $data['IsDel'] = 1;
        $StartDate = isset($data['StartDate']) ? date("Ymd", strtotime($data['StartDate'])) : '';
        $EndDate = isset($data['EndDate']) ? date("Ymd", strtotime($data['EndDate'])) : '';

        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type) && $rs['TtlRecords'] > 0) {
            if (ISCACHE) {
                $ckey = config_item('K1014');
                $ckey = str_replace('{ArticleID}', $data['ArticleIDs'], $ckey);
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1017');
                $ckey = str_replace('{ArticleID}', $data['ArticleIDs'], $ckey);
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);

                $this->cache->delete($ckey);

                $ckey = config_item('K1015');
                /*
                  $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                  $ckey = str_replace('{StartDate}', $StartDate, $ckey);
                  $ckey = str_replace('{EndDate}', $EndDate, $ckey);

                  $data['SelfRecommend'] = ($data['SelfRecommend']!='0')?$data['SelfRecommend']:'';

                  $ckey_all=str_replace('{SelfRecommend}','',$ckey);//删除全部文章memcache

                  $ckey = str_replace('{SelfRecommend}',$data['SelfRecommend'],$ckey);
                  $ismul = isset($data['IsMultimedia'])?$data['IsMultimedia']:'0';

                  $ckey_all=str_replace('{ismul}','0',$ckey_all);//删除全部文章memcache
                  $this->cache->delete($ckey_all);//删除全部文章memcache

                  $ckey = str_replace('{ismul}',$ismul,$ckey);
                 */
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1040');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $ckey = str_replace('{SelfRecommend}', '-1', $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1005');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1023');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1042');
                /*
                  $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                  $ckey = str_replace('{SortID}', $data['SortID'], $ckey);

                  $data['SelfRecommend'] = ($data['SelfRecommend']!='0')?$data['SelfRecommend']:'';

                  $ckey_all=str_replace('{SelfRecommend}','',$ckey);//删除全部文章memcache

                  $ckey = str_replace('{SelfRecommend}',$data['SelfRecommend'],$ckey);
                  $ismul = isset($data['IsMultimedia'])?$data['IsMultimedia']:'0';

                  $ckey_all=str_replace('{ismul}','0',$ckey_all);//删除全部文章memcache
                  $this->cache->delete($ckey_all);//删除全部文章memcache

                  $ckey = str_replace('{ismul}',$ismul,$ckey);

                 */
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);
            }
            return true;
        }
        return false;
    }

    /**
     * 删除草稿箱
     * @param array $data
     * @return boolean
     */
    function delBlogDrafArticle($data) {
        $type = 'B244';
        $data['IsDel'] = 1;

        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type) && $rs['TtlRecords'] > 0) {
            if (ISCACHE) {
                $ckey = config_item('K2020');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);
            }
            return true;
        }
        return false;
    }

    /*
      --|置顶个人博客文章
     */

    function topBlogArticle($data) {
        /*
          UpdateType
          ArticleID
          MemberID
          AppearTime

         */
        $type = 'B206';
        $StartDate = isset($data['StartDate']) ? date("Ymd", strtotime($data['StartDate'])) : '';
        $EndDate = isset($data['EndDate']) ? date("Ymd", strtotime($data['EndDate'])) : '';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type) && $rs['TtlRecords'] > 0) {

            if (ISCACHE) {

                $ckey = config_item('K1014');
                $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1017');
                $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1015');
                /*
                  $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                  $ckey = str_replace('{StartDate}', $StartDate, $ckey);
                  $ckey = str_replace('{EndDate}', $EndDate, $ckey);

                  $data['SelfRecommend'] = ($data['SelfRecommend']!='0')?$data['SelfRecommend']:'';
                  $ckey_all=str_replace('{SelfRecommend}','',$ckey);//删除全部文章memcache

                  $ckey = str_replace('{SelfRecommend}',$data['SelfRecommend'],$ckey);
                  $ismul = isset($data['IsMultimedia'])?$data['IsMultimedia']:'0';

                  $ckey_all=str_replace('{ismul}','0',$ckey_all);//删除全部文章memcache
                  $this->cache->delete($ckey_all);//删除全部文章memcache

                  $ckey = str_replace('{ismul}',$ismul,$ckey);
                 */
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1040');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $ckey = str_replace('{SelfRecommend}', '-1', $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1042');
                /*
                  $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                  $ckey = str_replace('{SortID}', $data['SortID'], $ckey);

                  $data['SelfRecommend'] = ($data['SelfRecommend']!='0')?$data['SelfRecommend']:'';

                  $ckey_all=str_replace('{SelfRecommend}','',$ckey);//删除全部文章memcache

                  $ckey = str_replace('{SelfRecommend}',$data['SelfRecommend'],$ckey);
                  $ismul = isset($data['IsMultimedia'])?$data['IsMultimedia']:'0';

                  $ckey_all=str_replace('{ismul}','0',$ckey_all);//删除全部文章memcache
                  $this->cache->delete($ckey_all);//删除全部文章memcache

                  $ckey = str_replace('{ismul}',$ismul,$ckey);

                 */
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1023');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);

                $ckey = config_item('K1005');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $this->cache->delete($ckey);
            }
            return true;
        }
        return false;
    }

    /*
      --|获取个人博客文章评论列表
      --|缓存key 1014
     */

    function getBlogArticleByID($data, $t = 'view') {
        //$data['IsChecked']     = isset($data['IsChecked'])? $data['IsChecked']:1;//博主0, 其他用户1
        $type = 'B240';

        if (ISCACHE && $t == 'view') {
            $ckey = config_item('K1014');
            $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
            $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);

            $rs = $this->cache->get($ckey);
            if (!$rs || $rs['ArticleType'] != 'db') {
                $tmp = $rs;
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type, $data) && isset($rs['Record'])) {
                    $rs['Record']['ArticleType'] = 'db';
                    $this->cache->set($ckey, $rs['Record'], EXPIRETIME_1);
                    return $rs['Record'];
                } else {
                    return isset($tmp) ? $tmp : false;
                }
            }
            return $rs;
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);

            if ($this->_checkrs($rs, $type) && isset($rs['Record'])) {
                return $rs['Record'];
            } else {
                return false;
            }
        }
    }

    /*
      --|获取文章统计
      --|缓存key 1017
     */

    function getBlogArticleStatByID($data) {
        $type = 'B342';
        if (!ISCACHE) {
            $ckey = config_item('K1017');
            $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
            $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
            //	error_log(date("Y-m-d H:i:s").'|'.$ckey.'|K1017B240'."\r\n", 3, '/home/www/html/logs/B125.log');

            $rs = $this->cache->get($ckey);

            if (!$rs) {
//                print_r($data);
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

//this
    /*
      --修改置顶字段并删除文章统计缓存
      --|缓存key 1017
     */
    function delBlogArticleStatByID($data) {
        $type = 'B342'; //修改置顶字段接口待定

        $ckey = config_item('K1017');
        $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
        $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);

        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);
        if ($this->_checkrs($rs, $type) && isset($rs['Record'])) {
            $this->cache->del($ckey);
            return $rs['Record'];
        } else {
            return false;
        }
    }

//this


    /*
      --|获取个人博客文章归档表
      --|缓存key 1018
     */
    function getBlogArticleArchive($data) {
        $type = 'B230';
        $data['Type'] = isset($data['Type']) ? $data['Type'] : 0;
        $data['StatDate'] = isset($data['StatDate']) ? $data['StatDate'] : '200601';
        $temp = array();

        if (ISCACHE) {
            $ckey = config_item('K1018');
            $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
            $ckey = str_replace('{Type}', $data['Type'], $ckey);
            $rs = $this->cache->get($ckey);

            if (!$rs) {
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type)) {
                    if ($rs['RetRecords'] == 1) {
                        $rs['Record'] = array(0 => $rs['Record']);
                    }
                    $temp = (isset($rs['Record']) ? $rs['Record'] : array());
                    $this->cache->set($ckey, $temp, EXPIRETIME_1);
                    return $temp;
                } else {
                    return false;
                }
            }
            return $rs;
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);
            //error_log(print_r($rs,true).'|'.print_r($data,true).'\r\n', 3, '/home/www/html/logs/cundan_article_'.date('Ymd').'.log');
            if ($this->_checkrs($rs, $type)) {
                if ($rs['RetRecords'] == 1) {
                    $rs['Record'] = array(0 => $rs['Record']);
                }
                $temp = (isset($rs['Record']) ? $rs['Record'] : array());
            }
            return $temp;
        }
    }

    /*
      --|获取个人博客标签文章列表
     */

    function getBlogTagArticle($data) {
        $type = 'B217';
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;
        $data['QryCount'] = isset($data['QryCount']) ? $data['QryCount'] : '10';
        $data['IsSummary'] = isset($data['IsSummary']) ? $data['IsSummary'] : 0;

        $ismul = isset($data['IsMultimedia']) ? $data['IsMultimedia'] : '0';
        $data['SelfRecommend'] = isset($data['SelfRecommend']) ? $data['SelfRecommend'] : '';

        if (ISCACHE) {
            if ($data['StartNo'] == -1) {

                $ckey = config_item('K1015');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);

                if ($ismul == 1) {
                    $key = 'ismul_1';
                } else if ($ismul == 2) {
                    $key = 'ismul_2';
                } else if ($data['SelfRecommend'] != '') {
                    $key = 'Recommend';
                } else if ($data['IsTop'] != '') {
                    $key = 'istop';
                } else {
                    $key = 'all';
                }

                $key = 'TagID_' . $data['TagID'] . $key;


                $rs = $this->cache->get($ckey);

                //error_log(print_r($rs,true), 3, '/home/httpd/logs/a111.log');

                if (!$rs) {

                    if ($data['SelfRecommend'] == '') {
                        unset($data['SelfRecommend']);
                    }
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type)) {
                        $getData = array();
                        $getData[$key] = isset($rs['TtlRecords']) ? $rs['TtlRecords'] : 0;
                        $getData['FlagCode'] = md5(time());

                        if ((isset($getData[$key]) && $getData[$key] > 0)) {
                            $this->cache->set($ckey, $getData, EXPIRETIME_1);
                        }
                        return $getData;
                    } else {
                        return false;
                    }
                } else if (!$rs[$key]) {
                    if ($data['SelfRecommend'] == '') {
                        unset($data['SelfRecommend']);
                    }
                    $rs2 = $this->socket['newblog']->senddata($type, $data);
                    $rs2 = xmltoarray($rs2);
                    if ($this->_checkrs($rs2, $type, $data)) {

                        $rs[$key] = isset($rs2['TtlRecords']) ? $rs2['TtlRecords'] : 0;

                        if (isset($rs[$key]) && $rs[$key] > 0) {
                            $this->cache->set($ckey, $rs, EXPIRETIME_1);
                        }
                        return $rs;
                    } else {
                        return false;
                    }
                }

                return $rs;
            } else {
                $ckey = config_item('K1029');
                $ckey = str_replace('{TagID}', $data['TagID'], $ckey);
                $ckey = str_replace('{PageNo}', ($data['StartNo'] / $data['QryCount'] + 1), $ckey);

                $ckey = str_replace('{SelfRecommend}', $data['SelfRecommend'], $ckey);
                $ckey = str_replace('{ismul}', $ismul, $ckey);
                $ckey = str_replace('{istop}', $data['IsTop'], $ckey);

                $rs = $this->cache->get($ckey);
                if (!$rs || ($data['FlagCode'] != $rs['FlagCode'])) {
                    if ($data['SelfRecommend'] == '') {
                        unset($data['SelfRecommend']);
                    }

                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type)) {
                        if (isset($rs['Record']) && $rs['RetRecords'] == 1) {
                            $rs['Record'] = array(0 => $rs['Record']);
                        }
                        $rs['FlagCode'] = $data['FlagCode'];
                        $this->cache->set($ckey, $rs, EXPIRETIME_1);
                        return $rs;
                    } else {
                        return false;
                    }
                }
                return $rs;
            }
        } else {

            if ($data['SelfRecommend'] == '') {
                unset($data['SelfRecommend']);
            }


            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);

            if ($this->_checkrs($rs, $type)) {
                if ($data['StartNo'] == -1) {

                    if ($ismul == 1) {
                        $key = 'ismul_1';
                    } else if ($ismul == 2) {
                        $key = 'ismul_2';
                    } else if ($data['SelfRecommend'] != '') {
                        $key = 'Recommend';
                    } else if ($data['IsTop'] != '') {
                        $key = 'istop';
                    } else {
                        $key = 'all';
                    }

                    $key = 'TagID_' . $data['TagID'] . $key;
                    $rs[$key] = $rs['TtlRecords'];

                    return isset($rs) ? $rs : 0;
                } else if (isset($rs['Record']) && $rs['RetRecords'] == 1) {
                    $rs['Record'] = array(0 => $rs['Record']);
                }
                return $rs;
            }
            return false;
        }
    }

    //首页文章推荐
    function RecmmondArticle($data) {
        $type = 'B325';
        $data['IsCheck'] = isset($data['IsCheck']) ? $data['IsCheck'] : '0';
        $data['BlogType'] = isset($data['BlogType']) ? $data['BlogType'] : '1';
        $data['RecommendID'] = isset($data['RecommendID']) ? $data['RecommendID'] : 0;

        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type)) {
            return true;
        }
        return false;
    }

    function getApBlogArticleByID($data) {
        $type = 'B252';

        if (ISCACHE) {
            $ckey = config_item('K1058');
            $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
            $rs = $this->cache->get($ckey);

            if (!$rs) {
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type) && isset($rs['Record'])) {
                    $this->cache->set($ckey, $rs['Record'], EXPIRETIME_1);
                    return $rs['Record'];
                } else {
                    return false;
                }
            }
            return $rs;
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);

            if ($this->_checkrs($rs, $type) && isset($rs['Record'])) {
                return $rs['Record'];
            } else {
                return false;
            }
        }
    }

    //文章转载
    function articleTransshipment($data) {

        $type = 'B242';
        $rs = $this->socket['newblog']->senddata($type, $data);

        $rs = xmltoarray($rs);
        error_log(print_r($data, true) . '|' . print_r($rs, true), 3, '/home/httpd/logs/ainsertfailed.log');
        if ($this->_checkrs($rs)) {

            //$ckey = config_item('K1072');
            //$ckey = str_replace('{articleid}', $data['FocusArtArticleID'], $ckey);
            //$num = $this->cache->get($ckey);
            //if (!$num) {
            //$this->cache->set($ckey,$rs['Record']+1,EXPIRETIME_2);
            //    $this->cache->set($ckey, 1, EXPIRETIME_2);
            //} else {
            //    $this->cache->set($ckey, $num + 1, EXPIRETIME_2);
            //}
            //删除文章详情缓存
            $ckey2 = config_item('K1014');
            $ckey2 = str_replace('{ArticleID}', $data['FocusArtArticleID'], $ckey2);
            $ckey2 = str_replace('{MemberID}', $data['FocusArtMemberID'], $ckey2);
            $this->cache->delete($ckey2);
            //删除文章详情缓存

            return '1';
        }
        return '0';
    }

    //获取文章转载次数
    function getTransshipmentNum($data) {
        $type = ''; //接口待定

        if (ISCACHE) {
            $ckey = config_item('K1072');
            $ckey = str_replace('{articleid}', $data['articleID'], $ckey);
            $rs = $this->cache->get($ckey);

            if (!$rs && $rs < 0) {
                //接口确定后删除
                $this->cache->set($ckey, 0, EXPIRETIME_2);
                return 0;
                //接口确定后删除

                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type) && isset($rs['Record'])) {
                    $this->cache->set($ckey, $rs['Record'], EXPIRETIME_2);
                    return $rs['Record'];
                } else {
                    return false;
                }
            }
            return $rs;
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);

            if ($this->_checkrs($rs, $type) && isset($rs['Record'])) {
                return $rs['Record'];
            } else {
                return false;
            }
        }
    }

    //文章收藏
    function articleCollect($data) {

        $type = 'B242';
        $rs = $this->socket['newblog']->senddata($type, $data);

        $rs = xmltoarray($rs);
        if ($this->_checkrs($rs)) {

            //$ckey = config_item('K1073');
            //$ckey = str_replace('{articleid}', $data['FocusArtArticleID'], $ckey);
            //$num = $this->cache->get($ckey);
            //删除文章详情缓存
            $ckey2 = config_item('K1014');
            $ckey2 = str_replace('{ArticleID}', $data['FocusArtArticleID'], $ckey2);
            $ckey2 = str_replace('{MemberID}', $data['FocusArtMemberID'], $ckey2);
            $this->cache->delete($ckey2);
            //删除文章详情缓存
            //if (!$num) {
            //$this->cache->set($ckey,$rs['Record']+1,EXPIRETIME_2);
            //   $this->cache->set($ckey, 1, EXPIRETIME_2);
            //} else {
            //    $this->cache->set($ckey, $num + 1, EXPIRETIME_2);
            //}
            return '1';
        }
        return '0';
    }

    //获取文章收藏次数
    function getArticleCollectNum($data) {

        $type = ''; //接口待定

        if (ISCACHE) {
            $ckey = config_item('K1073');
            $ckey = str_replace('{articleid}', $data['articleID'], $ckey);
            $rs = $this->cache->get($ckey);

            if (!$rs && $rs < 0) {
                //接口确定后删除
                $this->cache->set($ckey, 0, EXPIRETIME_2);
                return 0;
                //接口确定后删除

                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type) && isset($rs['Record'])) {
                    $this->cache->set($ckey, $rs['Record'], EXPIRETIME_2);
                    return $rs['Record'];
                } else {
                    return false;
                }
            }
            return $rs;
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);

            if ($this->_checkrs($rs, $type) && isset($rs['Record'])) {
                return $rs['Record'];
            } else {
                return false;
            }
        }
    }

    //获取文章是否被举报过
    function articleReport($data) {
        $type = ''; //接口待定

        if (!ISCACHE) {
            $ckey = config_item('K1074');
            $ckey = str_replace('{articleid}', $data['articleid'], $ckey);
            $rs = $this->cache->get($ckey);

            if (!$rs) {
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type) && isset($rs['Record'])) {
                    $this->cache->set($ckey, $rs['Record'], EXPIRETIME_1);
                    return $rs['Record'];
                } else {
                    return false;
                }
            }
            return $rs;
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);

            if ($this->_checkrs($rs, $type) && isset($rs['Record'])) {
                return $rs['Record'];
            } else {
                return false;
            }
        }
    }

    //将举报的文章添加到保10洁疑问列表中
    function addArticleReport($data) {
        $type = 'B273';

        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($rs['Code'] == '00') {
            return true;
        } else {
            return false;
        }
    }

    /*
      --|获取浏览过该文章的人还浏览过
     */

    function getGuestEverBrowse($data) {
        if (empty($data['articleid']))
            return false;

        $type = 'B247'; //获取浏览过该文章的人还浏览过的数据没有写入库，所以只要获取个人博客文章高手看盘名家看市的文章列表即可

        $data['Recomend'] = 2;
        $data['ShowMode'] = 0;
        $data['StartNo'] = mt_rand(1, 30);

        if (ISCACHE) {
            $ckey = config_item('articleeverbrowse');
            $ckey = str_replace('articleid', $data['articleid'], $ckey);

            $rs = unserialize($this->cache->get_all($ckey)); //$this->cache->get不可以取到缓存
            //error_log(print_r($rs,true).'\r\n', 3, '/home/httpd/logs/aguesteverbrowse.log');

            if (!$rs) {

                if (time() - $data['appeartime'] < 600) {
                    return false;
                }

                $keyStr = str_replace('articleid', $data['articleid'], config_item('articlevisitor'));
                $keyStr = $this->cache->get_all($keyStr);
                $visivor = isset($keyStr['VUsers']) ? count(unserialize($keyStr['VUsers'])) : 0;

                if ($visivor == 0) {
                    return false;
                }
                $data['QryCount'] = $visivor;

                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type)) {
                    if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {

                        foreach ($rs['Record'] as $key => $value) {
                            $rs['Record'][$key]['AppearTime'] = strtotime($value['AppearTime']);
                        }
                        $this->cache->setpure($ckey, serialize($rs['Record']), EXPIRETIME_5); //设置数据到cache(数据不作改变)
                        //			error_log(print_r($rs['Record'],true).'\r\n', 3, '/home/httpd/logs/aguesteverbrowse.log');
                        return $rs['Record'];
                    }
                }
                return false;
            }
            return $rs;
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);

            if ($this->_checkrs($rs, $type)) {
                if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
                    return $rs['Record'];
                }
            }

            return false;
        }
    }

    /* 获取个人博客首页文章列表 */

    function getArticleDynamic($data) {
        $type = 'B272';
        if (ISCACHE) {
            if ($data['StartNo'] == -1) {
                $ckey = config_item('K1076');
                $ckey = str_replace('{MemberID}', $data['UserID'], $ckey);
                $rs = $this->cache->get($ckey);

                if (!$rs) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if (isset($rs['TtlRecords']) && $rs['TtlRecords'] > 0) {
                        $this->cache->set($ckey, $rs['TtlRecords'], EXPIRETIME_1);
                        return $rs['TtlRecords'];
                    }

                    return false;
                }

                return $rs;
            }

            if ($data['StartNo'] == '0') {//只有1-15条做缓存
                $ckey = config_item('K1077');
                $ckey = str_replace('{MemberID}', $data['UserID'], $ckey);
                $rs = $this->cache->get($ckey);

                if (!$rs) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
                        if ($rs['RetRecords'] == 1) {

                            $this->cache->set($ckey, array($rs['Record']['ArticleID'] => $rs['Record']), EXPIRETIME_1);
                            return array($rs['Record']['ArticleID'] => $rs['Record']);
                        } else {
                            $newRs = array();
                            foreach ($rs['Record'] as $value) {
                                $newRs[$value['ArticleID']] = $value;
                            }
                            $this->cache->set($ckey, $newRs, EXPIRETIME_1);
                            return $newRs;
                        }
                    }

                    return false;
                }

                return $rs;
            } else {
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
                    if ($rs['RetRecords'] == 1) {
                        return array($rs['Record']['ArticleID'] => $rs['Record']);
                    } else {

                        $newRs = array();
                        foreach ($rs['Record'] as $value) {
                            $newRs[$value['ArticleID']] = $value;
                        }

                        return $newRs;
                    }
                } else {
                    return false;
                }
            }
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);

            if ($this->_checkrs($rs, $type)) {
                if ($data['StartNo'] == -1) {
                    if (isset($rs['TtlRecords']) && $rs['TtlRecords'] > 0) {
                        return $rs['TtlRecords'];
                    }
                    return false;
                } else {
                    if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
                        if ($rs['RetRecords'] == 1) {
                            return array($rs['Record']['ArticleID'] => $rs['Record']);
                        } else {

                            $newRs = array();
                            foreach ($rs['Record'] as $value) {
                                $newRs[$value['ArticleID']] = $value;
                            }

                            return $newRs;
                        }
                    } else {
                        return false;
                    }
                }
            } else {
                return false;
            }
        }
    }

    /* 获取个人博客首页动态文章 */

    function getArticleMovement($data) {

        $type = 'B272';

        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type)) {
            if ($data['StartNo'] == -1) {
                if (isset($rs['TtlRecords']) && $rs['TtlRecords'] > 0) {
                    return $rs['TtlRecords'];
                }
                return false;
            } else {
                if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
                    if ($rs['RetRecords'] == 1) {
                        return array($rs['Record']['ArticleID'] => $rs['Record']);
                    } else {

                        $newRs = array();
                        foreach ($rs['Record'] as $value) {
                            $newRs[$value['ArticleID']] = $value;
                        }

                        return $newRs;
                    }
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    function getDynamicCount($data) {
        $type = 'B270';

        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        //if($data['UserID']=='355031')
        //{
        //	error_log(print_r($data,true).'|'.print_r($rs,true).'\r\n', 3, '/home/httpd/logs/adyTime2.log');
        //}

        if ($this->_checkrs($rs, $type)) {

            return $rs['Record']['Count'];
        } else {
            return false;
        }
    }

    //功能：根据昵称获取博客域名(返回的status为1时博客关闭)
    function getBlogDomainName($data) {
        $type = 'B050';

        $data['Type'] = 4; //根据userid
        $data['StartNo'] = 0;
        $data['QryCount'] = -1;
        //$data['Function'] 	= 'getMemberBlogbyDomainName';

        if (ISCACHE) {
            $ckey = config_item('K1010');
            $ckey = str_replace('{QryData}', $data['QryData'], $ckey);
            $ckey = str_replace('{Type}', $data['Type'], $ckey);
            $rs = $this->cache->get($ckey);

            if (!$rs) {
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);
                if ($this->_checkrs($rs, $type, $data) && $rs['RetRecords'] > 0) {
                    $getData = $rs;
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
                $getData = $rs;
                unset($rs);
                return $getData;
            }
            return false;
        }
    }

    //查询各个系统标签频道下的文章列表
    function getSyaTagArticleList($TagID, $StartNo = 0, $RetCnt = 50, $FlagCode = 0) {
        $data['TagIDs'] = $TagID;
        $data['Type'] = 'B272';
//        $data['FlagCode'] = $FlagCode;
        $data['StartNo'] = $StartNo;
        $data['QryCount'] = $RetCnt;
        return $this->getAllArticleList($data);
    }

    //获取博客被采用的文章列表
    function getUseTagArticleList($TagID, $StartNo = 0, $RetCnt = 50, $tag = 0, $pagetype = 1) {
        //		global $config;
        if ($TagID == '1453,1462') {
            $data['TagIDs'] = $TagID;
            $data['PageType'] = $pagetype;
        } else if ($tag == 'Recommend') {
            $data['TagIDs'] = $TagID;
            $data['PageType'] = $pagetype;
        } else {
            $data['TagIDs'] = $TagID;
            $data['PageType'] = $pagetype;
        }

        $data['IsUsed'] = 1;
        $data['StartNo'] = $StartNo;
        $data['QryCount'] = $RetCnt;
        $data['Type'] = 'B260';

        return $this->B260($data);
    }

    function B260($data) {
        $type = $data['Type'];
//        print_r($data);
        $data['Platform'] = 1;
        if (ISCACHE) {
            $ckey = config_item('K2021');
            $ckey = str_replace('{QryCount}', $data['QryCount'], $ckey);
            $ckey = str_replace('{StartNo}', $data['StartNo'], $ckey);

            $tag = '';
            if ($data['TagID']) {
                $tag = $data['TagID'];
            } else if ($data['TagIDs']) {
                $tag = $data['TagIDs'];
            } else if ($data['Recommend']) {
                $tag = $data['Recommend'];
            } else if ($data['Recommends']) {
                $tag = $data['Recommends'];
            }

            $ckey = str_replace('{TagID}', $tag, $ckey);
            $ckey = str_replace('{Type}', $data['Type'], $ckey);
            $rs = $this->cache->get($ckey);
            //error_log(print_r($rs,true), 3, config_item('logPath').'a111.log');

            if (!$rs) {
                unset($data['Type']);
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type, $data) && $rs['RetRecords'] > 0) {
                    if ($rs['RetRecords'] == 1) {
                        $rs['Record'] = array(0 => $rs['Record']);
                    }
                    $getData = $rs;
                    $this->cache->set($ckey, $getData, EXPIRETIME_1);
                    unset($rs);
                    return $getData;
                } else {
                    return false;
                }
            }
            return $rs;
        } else {
            unset($data['Type']);

            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);


            $getData = array();
            if ($this->_checkrs($rs, $type, $data) && $rs['RetRecords'] > 0) {
                $getData = $rs;
                unset($rs);
                return $getData;
            }
            return false;
        }
    }

    /**
     * 首页 名家高手 列表
     * @param type $Recommend
     * @param type $StartNo
     * @param type $RetCnt
     * @param type $pagetype
     * @return type
     */
    function getMjArticle($Recommend, $StartNo = 0, $RetCnt = 60) {

        if ($Recommend == '3') {
            $data['TagID'] = 1474;
            $data['Recommend'] = $Recommend;
        } else if ($Recommend == '2') {
            $data['TagID'] = 1473;
            $data['Recommend'] = $Recommend;
        } else if ($Recommend == '4') {
            $data['Recommends'] = '2,3';
            $data['TagID'] = '1473,1474';
        } else {
            $data['Recommends'] = '2,3';
            $data['TagID'] = '1473,1474';
        }
        $data['PageType'] = 1;
//        $data['IsUsed'] = 1;
        $data['StartNo'] = $StartNo;
        $data['QryCount'] = $RetCnt;
        $data['Type'] = 'B260';

        return $this->B260($data);
    }

    /* start 刚修改
     * 查询名家，高手文章列表
     */

    function getRecommendArticleList($Recommend, $StartNo = 0, $RetCnt = 60, $FlagCode = 0) {
        if ($Recommend == '3') {
            $data['TagID'] = 1474;
            $data['Type'] = 'B272';
            $data['Recommend'] = $Recommend;
        } else if ($Recommend == '2') {
            $data['TagID'] = 1473;
            $data['Type'] = 'B272';
            $data['Recommend'] = $Recommend;
        } else if ($Recommend == '4') {
            $data['Recommends'] = '2,3';
            $data['Type'] = 'B272';
        } else {
            $data['TagID'] = '1473,1474';
            $data['Type'] = 'B272';
            $data['Recommend'] = $Recommend;
        }

        $data['FlagCode'] = $FlagCode;
        $data['StartNo'] = $StartNo;
        $data['QryCount'] = $RetCnt;


        return $this->getAllArticleList($data);
    }

    function getRecommendArticle($Recommend, $StartNo = 0, $RetCnt = 60, $FlagCode = 0, $pagetype = 1) {

        if ($Recommend == '3') {
            $data['TagID'] = 1474;
            $data['Type'] = 'B272';
            $data['Recommend'] = $Recommend;
        } else if ($Recommend == '2') {
            $data['TagID'] = 1473;
            $data['Type'] = 'B272';
            $data['Recommend'] = $Recommend;
        } else if ($Recommend == '4') {
            $data['Recommends'] = '2,3';
            $data['Type'] = 'B272';
        } else {
            $data['TagID'] = '1473,1474';
            $data['Type'] = 'B272';
            $data['Recommend'] = $Recommend;
        }

        $data['FlagCode'] = $FlagCode;
        $data['StartNo'] = $StartNo;
        $data['QryCount'] = $RetCnt;


        return $this->getAllArticleList($data);
    }

    /*
      --|获取各频道文章列表

     */

    function getAllArticleList($data) {
        $data['Platform'] = 1;//过滤鲜花文章
        if ($data['Type']) {
            $type = $data['Type'];
        } else {
            $type = 'B215';
        }


        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;
        $data['QryCount'] = isset($data['QryCount']) ? $data['QryCount'] : 10;
        $StartDate = isset($data['StartDate']) ? date("Ymd", strtotime($data['StartDate'])) : '';
        $EndDate = isset($data['EndDate']) ? date("Ymd", strtotime($data['EndDate'])) : '';

        $tag = '';
        if ($data['TagID']) {
            $tag = $data['TagID'];
        } else if ($data['TagIDs']) {
            $tag = $data['TagIDs'];
        } else if ($data['Recommend']) {
            $tag = $data['Recommend'];
        } else if ($data['Recommends']) {
            $tag = $data['Recommends'];
        }
//        print_r($data);

        if (ISCACHE) {
            if ($data['StartNo'] == -1) {
                $ckey = config_item('K7001');
                $ckey = str_replace('{MemberID}', $tag, $ckey);
                $ckey = str_replace('{StartDate}', $StartDate, $ckey);
                $ckey = str_replace('{EndDate}', $EndDate, $ckey);
                $rs = $this->cache->get($ckey);
                if (!$rs) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);

                    if ($this->_checkrs($rs, $type, $data)) {
                        $getData = array();
                        $getData['TtlRecords'] = isset($rs['TtlRecords']) ? $rs['TtlRecords'] : 0;
                        $getData['FlagCode'] = md5($getData['TtlRecords'] . time());


                        if ((isset($getData['TtlRecords']) && $getData['TtlRecords'] > 0) || (isset($getData['UTopCnt']) && $getData['UTopCnt'] > 0)) {
                            $this->cache->set($ckey, $getData, EXPIRETIME_1);
                        }
                        return $getData;
                    } else {
                        return false;
                    }
                }
                return $rs;
            } else {
                $ckey = config_item('K1016');
                $ckey = str_replace('{MemberID}', $tag, $ckey);
                $ckey = str_replace('{PageNo}', ($data['StartNo'] / $data['QryCount'] + 1), $ckey);
                $ckey = str_replace('{StartDate}', $StartDate, $ckey);
                $ckey = str_replace('{EndDate}', $EndDate, $ckey);

                $rs = $this->cache->get($ckey);

                if (!$rs || ($data['FlagCode'] != $rs['FlagCode'])) {

                    $getData = array();
                    $rs = $this->socket['newblog']->senddata($type, $data);

                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type, $data)) {
                        $getData['RetRecords'] = 0;
                        if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
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
            } else {

                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);

                $getData = array();
                if ($this->_checkrs($rs, $type)) {
                    $getData['RetRecords'] = 0;
                    if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
                        $getData['RetRecords'] = $rs['RetRecords'];
                        $getData['Record'] = $rs['Record'];
                    }
                }
                unset($rs);

                return $getData;
            }
        }
    }

    /**
     * 获取推荐博客列表
     * @param type $data
     * @return boolean
     */
    public function getRecomendBlogList($data) {
        $type = 'B078';
        if (ISCACHE) {
            $ckey = config_item('K2022');
            $ckey = str_replace('{QryCount}', $data['QryCount'], $ckey);
            $ckey = str_replace('{StartNo}', $data['StartNo'], $ckey);
            $rs = $this->cache->get($ckey);

            if (!$rs) {
                $rs = $this->socket['newblog']->senddata($type, $data);
                $rs = xmltoarray($rs);
                if ($this->_checkrs($rs, $type, $data) && $rs['RetRecords'] > 0) {
                    if ($rs['RetRecords'] == 1) {
                        $rs['Record'] = array(0 => $rs['Record']);
                    }
                    $getData = $rs;
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
                $getData = $rs;
                unset($rs);
                return $getData;
            }
            return false;
        }
    }

}

//end class
?>