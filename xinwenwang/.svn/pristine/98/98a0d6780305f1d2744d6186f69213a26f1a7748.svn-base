<?php

namespace Home\Controller;

use Home\Model\DingyueModel;
use Home\Model\NewsModel;
use Think\Controller;

class NewsController extends HomeCommonController {

    public function _initialize() {
        parent::_initialize();
    }

    public function tuijian() {
        $this->display("Index/index");
    }

    public function hot() {


        $model = new NewsModel();
        $page = max(I('get.p'), 1);
        $where = array();
        $where['update_time'] = array('between', array(strtotime('-3 day'), time()));
        $order = 'good_sum desc';
        $data = $model->getWhereList($where, $page, 50, $order);
        $this->assign('list', $data['data_list']);
        $this->assign('page_html', showHomePage($data['total'], 50));
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
        $page = max(I('get.p'), 1);
        $type_id = I('get.type_id');
        $where = array(
            'type_id' => $type_id,
        );
        $limit = 50;
        $order = 'update_time desc';
        $key = 'news_list_' . $type_id;
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
        $this->display("Index/index");
    }

    public function info() {
        $id = I('get.id');
        $key = 'news_info_' . $id;
        $info = getMemcache($key);
        if (empty($info)) {
            $info = D('News')->getInfo($id);
            setMemcache($key, $info, 60 * 60 * 24);
        }
        if (empty($info)) {
            Header("HTTP/1.1 404");
            Header("Location:" . C('URL_DOMAIN'));
        }
        //该频道是否订阅
        $sid = $info['dingyue_id'];
        $user = cookie('user_info');
        $uid = $user['id'];
        if ($uid) {
            $cdata['uid'] = $uid;
            $cdata['email'] = $user['email'];
            $cdata['title'] = $info['title'];
            $cdata['url'] = $info['url'];
            $cdata['add_time'] = time();
            M('UserView')->add($cdata);
        }
        $m = new DingyueModel();
        $temp = $m->isDingyue($uid, $sid);
        $isdingyue = empty($temp) ? "+ 订阅" : '已订阅';
        $islike = empty($temp) ? "like" : "unlike";
        $this->assign('isdingyue', $isdingyue);
        $this->assign('islike', $islike);

        //内容分页
//        $content = $info['html'];
//        $ipage = I('get.p') ? intval(I('get.p')) : 1;
//        $CP = new \Common\Extend\Cutpage();
//        $CP->pagestr = $content;
//        $CP->cut_str();
//        $info['html'] = $CP->pagearr[$ipage - 1];
//        $pageH = showHomePage($CP->sum_page, 1);strip_tags($info['html'])
        $this->assign('info', $info);
        $ckey = 'news_info_comments_' . $id;
        $comments = getMemcache($ckey);
        if (empty($comments['data_list'])) {
            $comments = D('News')->getCommentsByNewsId($id);
            setMemcache($ckey, $comments, 60 * 5);
        }
        $this->assign('comments', $comments['data_list']);

//        $this->assign('pageHtml', showHomePage($CP->sum_page, 1));
        //热门推荐
        $m = new NewsModel();
        $where['dingyue_id'] = array('eq', $sid);
        $hkey = 'news_info_hot_' . $sid;
        $list = getMemcache($hkey);
        if (empty($list)) {
            $list = $m->getHotnew($where);
            setMemcache($hkey, $list, 60 * 60);
        }
        $this->assign('hot_list', $list);
        $this->assign('id', $id);
        //相关推荐数据
        $rkey = 'news_info_relate_recomend_' . $id;
        $recomend_list = getMemcache($rkey);
        if (empty($recomend_list)) {
            $recomend_list = $m->getReComendList($info);
            if (empty($recomend_list)) {
                $recomend_list = $m->getRelateList($info['type_id']);
//            if ($this->is_crawler()) {
//                foreach ($read_list as $item) {
//                    M('SpidersBaidu')->where(array('id' => $item['id']))->data(array('is_status' => '1'))->save();
//                }
//            }
            }
            setMemcache($rkey, $recomend_list, 60 * 60);
        }
        $this->assign('read_list', $recomend_list);
        //上下篇
        $pn_news = $m->getPreNextNews($id);
        $this->assign('pn_news', $pn_news);
        
        $tinfo = M('NewsType')->field('title,pinyin')->where(array('id' => $info['type_id']))->find();
        $type_name = $tinfo['title'];
        $this->assign('nav', $tinfo['pinyin']);
        $this->assign('type_name', $type_name);
        $seo = array(
            't' => $info['title'] . '-新闻王（xinwenwang.com）网罗天下事',
            'k' => $info['title'] . ',' . $type_name . ',新闻王',
            'd' => msubstr($info['html'], 0, 150)
        );
        $this->assign('seo', $seo);
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
        $page = max(I('post.p'), 1);
        $where = array(
            'dingyue_id' => $id,
        );
        $data = $model->getDingYueList($where, $page, 20, '');
        // 显示隐藏分页拱搜索引擎抓取
        $this->assign('page_html', showHomePage($data['total'], 20));
        $this->assign('list', $data['data_list']);
        //print_r($data);
        if (IS_POST) {
            $this->ajaxReturn(array('code' => 200, 'data' => $data));
            exit;
        }

        $d_model = new DingyueModel();
        $dingyue = $d_model->getInfo($id);
        $this->assign('dingyue', $dingyue);
        $seo = array(
            't' => $dingyue['name'] . '-' . $dingyue['name'] . '订阅_新闻王',
            'k' => $dingyue['name'] . '，' . $dingyue['name'] . '订阅，' . $dingyue['name'] . '订阅列表',
            'd' => $dingyue['name'] . '是' . $dingyue['name'] . '订阅页面，24小时提供最全面，最统一，最新鲜，最热门的' . $dingyue['name'] . '资讯'
        );
        $this->assign('seo', $seo);
        $this->display('dingyue');
    }

    public function add_dingyue() {
        $user = cookie('user_info');
        $sid = I('post.id');
        $status = I('post.status');
        $uid = $user['id'];
        $m = new DingyueModel();
        if ($status == 'unlike') {
            $temp = $m->addDingyue($uid, $sid);
        } else {
            $temp = $m->deleteDingyue($uid, $sid);
        }
        if (!$temp) {
            $this->ajaxReturn(0);
        } else {
            $this->ajaxReturn(1);
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
        $com_id = I('post.com_id');
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
            $temp = $model->addComment($uid, $content, $news_id, $com_id);
            $r_data = $user;
            $r_data['news_id'] = $temp;
            $r_data['head_pic'] = setUpUrl($user['head_pic']);
        } else {
            $this->ajaxReturn(array('code' => 202));
            $temp = $model->guestComment($news_id, $content, $com_id);
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

    private function is_crawler() {
        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $spiders = array(
//            'Googlebot', // Google 爬虫
            'Baiduspider', // 百度爬虫
//            'Yahoo! Slurp', // 雅虎爬虫
//            'YodaoBot', // 有道爬虫
//            'msnbot' // Bing爬虫
                // 更多爬虫关键字
        );
        foreach ($spiders as $spider) {
            $spider = strtolower($spider);
            if (strpos($userAgent, $spider) !== false) {
                return true;
            }
        }
        return false;
    }

}