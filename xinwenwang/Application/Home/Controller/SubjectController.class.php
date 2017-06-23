<?php

namespace Home\Controller;

use Think\Controller;

class SubjectController extends HomeCommonController {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        $page = max(I('get.p'), 1);
        $type_id = I('get.type_id');
        $type = D('NewsType')->getSubjectList();
        $where = array(
            '_string' => 'show_type=2 or show_type=3',
            'subject_id' => array('in', implode(',', array_keys($type))),
        );
        $order = 'update_time desc';
        $ckey = 'subject_list_num5';
        $data = getMemcache($ckey);
        if (empty($data['data_list'])) {
            $data = D('News')->getListLimit($where, 5, $order);
            setMemcache($ckey, $data, 60 * 30);
        }

        $list_first = array_slice($data['data_list'], 0, 1);
        $this->assign('list_first', $list_first[0]);
        $this->assign('list', array_slice($data['data_list'], 1, 4));
        $seo = array(
            't' => "专题_专题中心_新闻网__新闻王",
            'k' => "专题新闻,专题新闻资讯，最新专题新闻,热门专题新闻,专题热点新闻，专题新闻网",
            'd' => "新闻王专题频道是专业的专题，专题新闻,新闻中心,热门专题新闻,专题热点新闻，专题新闻网，每天24小时滚动报道最新最热门的专题新闻资讯。"
        );
        $this->assign('seo', $seo);
        $this->assign('type', $type);
        $this->assign('navtap', 'subject');
        $this->display();
    }

    public function lists() {
        $model = D('News');
        $page = max(I('get.p'), 1);
        $type_id = I('get.type_id');
        $where = array(
            'subject_id' => $type_id,
        );
        $limit = 32;
        $order = 'update_time desc';
        $key = 'news_list_subject_' . $type_id;
        $key .= '_' . $page . '_' . $limit;
        $key .= '_update_time_desc';

        $data = getMemcache($key);
        if (empty($data['data_list'])) {
            $data = $model->getWhereList($where, $page, $limit, $order);
            setMemcache($key, $data, 60 * 15);
        }
        $this->assign('list', $data['data_list']);
        //输出分页 用于搜索引擎抓取
        $this->assign('page_html', showHomePage($data['total'], $limit));

        $this->assign('nav', I('get.n'));
        $hkey = 'news_info_hot_subject_' . $type_id;
        $list = getMemcache($hkey);
        if (empty($list)) {
            $list = $model->getHotnew($where);
            setMemcache($hkey, $list, 60 * 60);
        }
        $this->assign('hot_list', $list);
        $tkey = 'news_type_name_t' . $type_id;
        $type_name = getMemcache($tkey);
        if (empty($type_name)) {
            $type_name = M('NewsType')->where(array('id' => $type_id))->getField('title');
            setMemcache($tkey, $type_name, 60 * 60 * 24);
        }
        $seo = array(
            't' => "{$type_name}_{$type_name}新闻_新闻中心_{$type_name}新闻网__新闻王",
            'k' => "{$type_name}新闻,{$type_name}新闻资讯，最新{$type_name}新闻,热门{$type_name}新闻,{$type_name}热点新闻，{$type_name}新闻网",
            'd' => "新闻王{$type_name}频道是专业的{$type_name}，{$type_name}新闻,新闻中心,热门{$type_name}新闻,{$type_name}热点新闻，{$type_name}新闻网，每天24小时滚动报道最新最热门的{$type_name}新闻资讯。"
        );
        $this->assign('seo', $seo);
        $this->assign('type_id', $type_id);
        $this->assign('type_name', $type_name);
        $this->display();
    }

}