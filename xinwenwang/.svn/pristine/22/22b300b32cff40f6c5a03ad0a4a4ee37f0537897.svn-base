<?php

namespace Mobile\Controller;

use Common\Model\DingyueModel;
use Common\Model\NewsModel;
use Think\Log;

class NewsController extends MobileCommonController {

    public function _initialize() {
        parent::_initialize();
    }

    public function tuijian() {
        $this->display("Index/index");
    }

    public function hot() {
        $model = new NewsModel();
        $page = max(I('request.p'), 1);
        $where = array();
        $where['update_time'] = array('between', array(strtotime('-3 day'), time()));

        $order = 'good_sum desc';
        $data = $model->getWhereList($where, $page, 20, $order);
        $this->assign('list', $data['data_list']);
        if (IS_POST) {
            $this->ajaxReturn(array('code' => 200, 'data' => $data['data_list']));
        }
        $pager = showMobilePage($data['total'], 20);
        $this->assign('pager', $pager);

        $comments = $model->getHotComments($where, 1, 5);
        $this->assign('comments', $comments['data_list']);
        $seo = array(
            't' => '新闻王热点频道提供最热评论，点赞新闻，提高新闻浏览质量。',
            'k' => '热点新闻，热点资讯，热点评论，点赞',
            'd' => '《新闻王》网罗天下事，每天24小时滚动报道最新鲜，最热门的国内，国际及社会等新闻。'
        );
        $this->assign('seo', $seo);
        $this->assign('nav', I('get.n'));
        $this->display("Index/index");
    }

    public function lists() {
        $model = new NewsModel();
        $page = max(I('request.p'), 1);
        $type_id = I('get.type_id');
        $where = array(
            'type_id' => $type_id,
        );
        $order = 'update_time desc';
        $data = $model->getWhereList($where, $page, 20, $order);
        $this->assign('list', $data['data_list']);
        $this->assign('total', $data['total']);
        if (IS_POST) {
            $this->ajaxReturn(array('code' => 200, 'data' => $data['data_list']));
        }
        $this->assign('nav', I('get.n'));
        $type_name = M('NewsType')->where(array('id' => $type_id))->getField('title');
        $seo = array(
            't' => "{$type_name}新闻_新闻中心_新闻王",
            'k' => "{$type_name}新闻,{$type_name}新闻报道,热门{$type_name}新闻",
            'd' => "新闻王{$type_name}频道是专业的{$type_name}新闻资讯频道，每天24小时滚动报道最新最热门的{$type_name}新闻资讯。"
        );
        $comments = $model->getHotComments($where, 1, 5);
        $this->assign('comments', $comments['data_list']);
        $this->assign('seo', $seo);
        $this->display("Index/index");
    }

    public function info() {
        $id = I('get.id');
        $info = D('News')->getInfo($id);
        //该频道是否订阅
        $sid = $info['dingyue_id'];
        $user = cookie('user_info');
        $uid = $user['id'];
//        $m = new DingyueModel();
//        $temp = $m->isDingyue($uid, $sid);
//        $isdingyue = empty($temp) ? "+ 订阅" : '已订阅';
//        $islike = empty($temp) ? "like" : "unlike";
//        $this->assign('isdingyue', $isdingyue);
//        $this->assign('islike', $islike);


        $this->assign('info', $info);
        $comments = D('News')->getCommentsByNewsId($id);
        $this->assign('comments', $comments['data_list']);
        //热门新闻
        $m = new \Home\Model\NewsModel();
//        $where['dingyue_id'] = array('eq', $sid);
//        $data = $m->getWhereList($where,8);
        $hot_list = $m->getReComendList($info, 20);
        $h_num = count($hot_list);
        if (empty($hot_list) || $h_num < 20) {
            $hot_list2 = $m->getRelateList($info['dingyue_id'], 20 - $h_num);
            $hot_list = array_merge($hot_list, $hot_list2);
        }
        $hot_list3 = array();
        foreach ($hot_list as $value) {
            $hot_list3[] = $m->getInfo($value['id']);
        }
        $this->assign('hot_list', $hot_list3);
        $this->assign('id', $id);
        //test

        $this->display('info');
    }

    public function dingyue() {
        //该频道是否订阅
        $id = I('get.id');
        $user = cookie('user_info');
        $uid = $user['id'];
        $m = new DingyueModel();
        $temp = $m->isDingyue($uid, $id);
        $isdingyue = empty($temp) ? "+ 订阅" : '已订阅';
        $islike = empty($temp) ? "like" : "unlike";
        $this->assign('isdingyue', $isdingyue);
        $this->assign('islike', $islike);

        $model = new NewsModel();
        $page = max(I('request.p'), 1);
        $where = array(
            'dingyue_id' => $id,
        );
        $data = $model->getDingYueList($where, $page, 20);
        // 显示隐藏分页拱搜索引擎抓取
        $this->assign('page_html', showPage($data['total'], 20));
        $this->assign('list', $data['data_list']);
        //print_r($data);
        if (IS_POST) {
            $this->ajaxReturn(array('code' => 200, 'data' => $data['data_list']));
            exit;
        }
        $d_model = new DingyueModel();
        $dingyue = $d_model->getInfo($id);
//        print_r($dingyue);exit;
        $this->assign('dingyue', $dingyue);
        $this->display('dingyue');
    }

    public function tool_dingyue() {
        $user = cookie('user_info');
        $sid = I('post.id');
        $status = I('post.status');
        $uid = $user['id'];
        $m = new DingyueModel();
        if ($status == 'like') {
            $temp = $m->addDingyue($uid, $sid);
        } else {
            $temp = $m->deleteDingyue($uid, $sid);
        }
        if (!$temp) {
            $this->ajaxReturn(array('code' => 201));
        } else {
            $this->ajaxReturn(array('code' => 200));
        }
    }

    //用户对评论顶踩
    final public function usergb() {
        $id = I('post.id');
        $gb = I('post.gb');
        $m = new NewsModel();
        $temp = $m->usergb($id, $gb);
        if (!$temp) {
            $this->ajaxReturn(0);
        } else {
            $this->ajaxReturn(1);
        }
    }

    #工具类
    //赞

    final public function good() {
        $id = I('post.id');
        $news = new NewsModel();
        $news->updGoodSum($id);
        echo 1;
    }

    //不赞
    final public function bad() {
        $id = I('post.id');
        $news = new NewsModel();
        $news->updBadSum($id);
        echo 1;
    }

    /**
     * 发表评论
     */
    final public function add_comment() {
        $user = cookie('user_info');
        $news_id = I('post.news_id');
        $uid = $user['id'];
        $content = I('post.c');
        $model = new NewsModel();
        $url = 'http://fk.258.com:84/GetFilterKey?key=';
        $file = file_get_contents($url . $content);
        $keyword = array_unique(array_filter(json_decode($file)));
        if ($keyword) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '您的评论含有非法字符,请修改后再次提交!'));
            exit;
        }
        if ($user) {
            $temp = $model->addComment($uid, $content, $news_id);
            $r_data = $user;
            $r_data['news_id'] = $temp;
            $r_data['head_pic'] = setUpUrl($user['head_pic']);
        } else {
            $temp = $model->guestComment($news_id, $content);
            $ip = get_online_ip();
            $arr = get_city_by_ip($ip);
            if ($arr && is_array($arr)) {
                $city = $arr['city'] ? $arr['city'] : '';
            }
            $r_data = array(
                'username' => $city . ' ip为' . $ip . '的网友',
                'head_pic' => '/Public/Home/images/default.png',
                'news_id' => $temp
            );
        }

        if (!$temp) {
            $this->ajaxReturn(array('code' => 201));
        } else {
            $this->ajaxReturn(array('code' => 200, 'data' => $r_data));
        }
    }

    final public function test() {
        //$list = M('News')->field('id,img_list')->where(array('show_type'=>'2'))->select();
        $list = F('tests');
        foreach ($list as $item) {
            $img_list = json_decode($item['img_list']);
            $data_img = array();
            foreach ($img_list as $img) {
                $data_img[] = setUpUrl($img);
            }
            $data = array(
                'id' => $item['id'],
                'img_list' => json_encode($data_img)
            );
            M('News')->save($data);
        }


        exit;
        $list = M('Dingyue')->field('id,name')->select();
        foreach ($list as $item) {
            M('News')->where(array('source_name' => $item['name']))->data(array('dingyue_id' => $item['id']))->save();
        }
    }

    final public function test2() {
        exit;
//        $list = M('News')->field('id,img_list')->where(array('show_type'=>'3'))->select();
        $list = F('tests2');
        foreach ($list as $item) {
            $img_list = json_decode($item['img_list']);
            $data_img = array();
            foreach ($img_list as $img) {
                $data_img[] = setUpUrl($img);
            }
            $data = array(
                'id' => $item['id'],
                'img_list' => json_encode($data_img)
            );
            M('News')->save($data);
        }


        exit;
        $list = M('Dingyue')->field('id,name')->select();
        foreach ($list as $item) {
            M('News')->where(array('source_name' => $item['name']))->data(array('dingyue_id' => $item['id']))->save();
        }
    }

    final public function setmap() {
        $file = fopen("errorlink.txt", "r") or exit("Unable to open file!");
//Output a line of the file until the end is reached
        while (!feof($file)) {
            $url = trim(fgets($file));
            if ($url)
                M('Url')->data(array('url' => fgets($file)))->add();
        }
        fclose($file);
    }

    final public function makeXml() {
        $list = M('Url')->where(array('status' => '0'))->order('id asc')->limit(40000)->select();
        if (!count($list)) {
            echo 111111;
            exit;
        }
        $xml = $this->buildXML($list);
        echo file_put_contents('./Uploads/' . time() . 'xml', $xml);
        M('Url')->where(array('status' => '0'))->order('id asc')->limit(40000)->data(array('status' => '1'))->save();
    }

    private function generate($url, $lastmod, $changefreq, $priority) {
        $url = htmlspecialchars($url);
        return "<url>\n<loc>$url</loc>\n<lastmod>$lastmod</lastmod>\n<changefreq>$changefreq</changefreq>\n<priority>$priority</priority>\n</url>\n";
    }

    private function buildXML($urls) {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        foreach ($urls as $item) {
            $xml .= $this->generate($item['url'], date('Y-m-d', time()), 'daily', '1.0');
        }
        $xml .= '</urlset>';
        return $xml;
    }

}