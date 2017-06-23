<?php

namespace Home\Controller;

use Common\Extend\Page;
use Home\Model\NewsModel;

class IndexController extends HomeCommonController {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        $model = new NewsModel();
        $page = max(I('request.p'), 1);
        if ($page > 50)
            $page = 50;
        $limit = 50;
        $where = array(
            'type_id' => array('in', $model->getIds())
        );
        $key = 'index_list_n' . $page . '_updatetime_desc';

        $data = getMemcache($key);
        if (empty($data)) {
            $data = $model->getWhereList($where, $page, $limit, 'update_time desc');
            setMemcache($key, $data, 60 * 15);
        }
        $this->assign('list', $data['data_list']);
        //输出分页 用于搜索引擎抓取
        $this->assign('page_html', showHomePage(2500, $limit));


        $seo = array(
            't' => '新闻_新闻网_新闻资讯_新闻中心-新闻王',
            'k' => '新闻，新闻网，新闻中心，新闻报道，最新新闻，热门新闻，体育新闻，娱乐新闻，社会新闻',
            'd' => '《新闻王》网罗天下事，每天24小时滚动报道最新鲜，最热门的国内新闻资讯，国际新闻资讯及社会新闻等。三位一体的阅读方式，让你在任何环境下都能一手掌握国内外最新动态'
        );
        $this->assign('navtap', 'index');
        $this->assign('seo', $seo);
        $this->assign('link_show', $page == 1 ? '1' : '0');
        $this->display();
    }

    public function updates() {
        $page = max(I('request.p'), 1);
        $ckey = 'news_comments_updates_p' . $page . '_n20';
        $data = getMemcache($ckey);
        if (empty($data['data_list'])) {
            $data = D('News')->updates(array(), $page, 20);
            setMemcache($ckey, $data, 60 * 30);
        }

        if (IS_POST) {
            $this->ajaxReturn(array('code' => 200, 'data' => $data));
            exit;
        }
//        var_dump($data['data_list']);
        $this->assign('list', $data['data_list']);
        //一周最热
        $time = strtotime(date('Y-m-d'));
        $wheres['update_time'] = array('gt', $time - 86400 * 7);
        $seo = array(
            't' => '新闻动态_新闻网_新闻资讯_新闻中心-新闻王',
            'k' => '新闻动态，新闻网动态，新闻中心，新闻报道，最新新闻，热门新闻，体育新闻，娱乐新闻，社会新闻',
            'd' => '《新闻王》网罗天下事，每天24小时滚动报道最新鲜，最热门的国内新闻资讯，国际新闻资讯及社会新闻等。三位一体的阅读方式，让你在任何环境下都能一手掌握国内外最新动态'
        );
        $this->assign('navtap', 'updates');
        $this->assign('seo', $seo);
        $nkey = 'news_list_updates_num15';
        $hot = getMemcache($nkey);
        if (empty($hot['data_list'])) {
            $hot = D('News')->getWhereList($wheres, 1, 15, 'is_hot desc');
            setMemcache($nkey, $hot, 60 * 30);
        }
        $this->assign('hot_list', $hot['data_list']);
        $this->display();
    }

    public function search() {
        $model = new NewsModel();
        $keyword = I('get.keyword');
        $page = max(I('request.p'), 1);

        $data = $model->getSearchList($keyword, '*', $page, 20, 'update_time desc');
        $this->assign('list', $data['data']);
        $seo = array(
            't' => $keyword . '搜索_新闻网_新闻资讯_新闻中心-新闻王',
            'k' => '新闻动态，新闻网动态，新闻中心，新闻报道，最新新闻，热门新闻，体育新闻，娱乐新闻，社会新闻',
            'd' => '《新闻王》网罗天下事，每天24小时滚动报道最新鲜，最热门的国内新闻资讯，国际新闻资讯及社会新闻等。三位一体的阅读方式，让你在任何环境下都能一手掌握国内外最新动态'
        );
        $this->assign('seo', $seo);
        $this->assign('nav', 'serach');
        $this->assign('page_html', showHomePage($data['total'], 20));
        $this->display();
    }

    public function hot_comments() {
        $page = max(I('post.p'), 1);
        $model = new NewsModel();
        $comments = $model->getHotComments(array(), $page, 5);
        $this->ajaxReturn(array('code' => 200, 'data' => $comments));
    }

}