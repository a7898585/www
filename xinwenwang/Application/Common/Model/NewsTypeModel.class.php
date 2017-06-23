<?php

namespace Common\Model;

use Think\Model;

class NewsTypeModel extends Model {

    public function getPage($currentPage, $map, $numPerPage, $noshow = false) {
        $page['pageNum'] = $currentPage;
        $page['totalCount'] = $this->where($map)->count();
        if (!$noshow) {
            $pager = pager($page['totalCount'], $numPerPage);
            $data = array();
            $data['numPerPage'] = $numPerPage;
            $data['totalCount'] = $page['totalCount'];
            $data['pagehtml'] = $pager;
            return $data;
        }
        return $page;
    }

    final public function getNavList() {
        $mk = "navs_list";
        $result = F($mk);
        if ($result)
            return $result;
        $temp1 = M('NewsType')->field('id,title,pinyin')->where(array('is_city' => '0', 'is_show' => '1'))->order('id asc')->select();
        $result = array();
        foreach ($temp1 as $item) {
            $result[$item['id']] = $item;
        }
        F($mk, $result); //
        return $result;
    }

    /**
     * 获取主题列表
     * @return type
     */
    final public function getSubjectList() {
        $mk = "subject_list";
//        $result = F($mk);
        if ($result)
            return $result;
        $temp1 = M('NewsType')->field('id,title,pinyin')->where(array('is_city' => '0', 'is_show' => '1', 'is_subject' => '1'))->order('order_id desc')->select();
        $result = array();
        foreach ($temp1 as $item) {
            $result[$item['id']] = $item;
        }
        F($mk, $result); //
        return $result;
    }

}