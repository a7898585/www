<?php

/*
  --|博客列表  更多
  --|modify  2013-0925 jianglw
 */

class Blogmore_socket extends MY_Model {

    function Blogmore_socket() {
        parent::MY_Model();
    }

    /**
     * 根据自定义标签获取标签id
     * @param type $tag
     * '1461' => 股市天地  股市精萃
      '1445' => 基金
      '1463' => 经济杂谈 财经杂谈
      '1465' => 外汇
      '1433' => 期货
      '1464' => 港股
      '1469' => 政权
      '1447' => 理财
      '1449' => 保险
      '1451' => 银行
      '1453' => 黄金
      '1457' => 债券
      '1455' => 汽车
      '1459' => 休闲区
      '1471' => 美酒
      '1462' => 白银
      '1460' => 投资收藏
      '1446' => 信托
     */
    function getTagInfo($tag) {
        switch ($tag) {
            case 'hjby':
                $TagID = '1453,1462'; //黄金1453  白银1462
                $tagNmae = '黄金白银';
                break;
            case 'cjzt':
                $TagID = '1463';
                $tagNmae = '财经杂谈';
                break;
            case 'gsjs':
                $TagID = '1461';
                $tagNmae = '股市精萃';
                break;
            case 'jjwh':
                $TagID = '1445,1465'; //'1445' => 基金 '1465' => 外汇
                $tagNmae = '基金外汇';
                break;
            case 'yhbx':
                $TagID = '1449,1451'; //'1449' => 保险 '1451' => 银行
                $tagNmae = '银行保险';
                break;
            case 'ggqh':
                $TagID = '1433,1464'; //'1433' => 期货 '1464' => 港股
                $tagNmae = '港股期货';
                break;
            case 'xflc':
                $TagID = '1447'; //'1447' => 理财
                $tagNmae = '消费理财';
                break;
            case 'other':
                $TagID = '1446,1457,1460'; //信托 1446 债券 1457 投资收藏  1460
                $tagNmae = '其他';
                break;
            default:
                $TagID = '1446,1457,1460'; //信托1446 债券 1457 投资收藏  1460
                $tagNmae = '其他';
                break;
        }
        return array('tagid' => $TagID, 'tagname' => $tagNmae);
    }

}