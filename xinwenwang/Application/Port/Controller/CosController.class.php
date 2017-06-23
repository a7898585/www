<?php

namespace Port\Controller;

use Org\Util\String;
use Port\Model\DingyueModel;
use Think\Log;
use Think\Upload\Driver\Upyun;

class CosController extends PortCommonController {

    protected $type_id = null;

    public function _initialize() {
        parent::_initialize();
        $this->type_id = I('get.t', '0', 'intval');
    }

    public function response() {
        $dm = new DingyueModel();
        $data = json_decode($_POST ['data'], 1);
        $result = array();
        if (empty($data)) {
            echo ' null';
            exit;
        }
//        $data[] = array('title'=>'阿里巴巴283亿元入股苏宁19.99% 成第二大股东');
        foreach ($data as $item) {
            if (!$item['wid']) {
                continue;
            }
            $id = M('News')->where(array('title' => $item['title']))->getField('id');
//            $id = M('News')->where(array('wid' => $item['wid'], 'type_id' => $item['sort']))->getField('id');
            if ($id) {
                $result[] = array('wid' => $item['wid'], 'sort' => $item['sort'], 'url' => "http://www.xinwenwang.com/r" . $id);
                continue;
            }
            $desc = String::msubstr($this->trimall(strip_tags($item['content'])), 0, 120);
            $desc = $this->trimall($desc);
            $good_sum = rand(-20, 100); 
            $bad_sum = rand(-5,10);
            $good_sum = $good_sum < 0?0:$good_sum;
            $bad_sum = $bad_sum < 0?0:$bad_sum;
            $dataAll = array(
                'type_id' => $item['sort']?$item['sort']:0,
                'wid' => $item['wid']?$item['wid']:0,
                'title' => $item['title'],
                'html' => $item['content'],
                'keyword' => $item['keywords'],
                'source_name' => $item['newsSource'],
                'source_url' => $item['pageurl'],
                'update_time' => time(),
                'add_time' => time(),
                'good_sum' => $good_sum,
                'bad_sum' => $bad_sum,
                'dingyue_id' => $dm->getInfoByName($item['newsSource']),
                'intro' => $desc,
                'is_desc' => '0'
            );
            if ($item['picture']) {
                if (checkPicStatus($item['picture']) == false) {
                    $picture = uploadToUpyun($item['picture']);
                    if ($picture)
                        $item['picture'] = C('YOU_PAI_YUN') . $picture;
                }
                $dataAll['img_list'] = json_encode(array($item['picture']));
            }
            if ($dataAll['type_id'] == 1828) {
                $dataAll['is_show'] = '0';
            }
            if (!$dataAll['wid']) {
                continue;
            }
            $id = M('News')->add($dataAll);
            $dataAll['id'] = $id;
            addNewsKey($dataAll);
//            if($id){
//                M('NewsHtml')->add(array('news_id'=>$id,'html'=>$dataAll['html']));
//            }
            $result[] = array('wid' => $item['wid'], 'sort' => $item['sort'], 'url' => "http://www.xinwenwang.com/r" . $id);
            unset($item, $dataAll);
        }
        Log::write('标题' . $dataAll['title']);
        echo json_encode($result);
        exit;
    }

    public function xiaohua() {
        $data = array(
            'title' => I('post.title'),
            'html' => I('post.content'),
            'type_id' => 1828,
            'add_time' => time(),
            'update_time' => time(),
            'source_name' => "笑话集",
            'source_url' => "http://www.jokeji.cn/",
        );
        $temp = M('News')->add($data);
        if ($temp) {
            echo 1;
        } else {
            echo 0;
        }
        exit;
    }

    /**
     * 取文章图片（目前在李剑的服务器定时触发生成）
     */
//`show_type` enum('1','2','3','4') DEFAULT '1' COMMENT '1：无图模式，2：单图模式，3：多图模式，4：广告模式',
    /**
     * 正则过滤图片<5kb 更新数据库 img_list 及 img_status = 2
     */
    public function uploadImg() {
        set_time_limit(0);
        G('begin');
        $list = M('News')->where(array('img_status' => '0'))->order('id desc')->limit(25)->select();
        if (empty($list)) {
            exit();
        }
        foreach ($list as $item) {
            G('e1');
            echo G('begin', 'e1') . 's(1)';
            $data = $this->replaceimg($item['html'], $item['title']);
            G('e2');
            echo G('begin', 'e2') . 's(2)';
            $size = count($data['img_list']);
            $img_list = array();
            $show_type = '1';
            if ($size >= 3) {
                $show_type = '3';
                $img_list = array_slice($data['img_list'], 0, 3);
            } else if ($size > 0) {
                $show_type = '2';
                $img_list = array_slice($data['img_list'], 0, 1);
            }
            $param = array('html' => $data['html'], 'show_type' => $show_type, 'img_status' => '2');
            if (!empty($img_list)) {
                $param['img_list'] = json_encode($img_list);
            }
            G('e3');
            echo G('begin', 'e3') . 's(3)';
            M('News')->data($param)->where(array('id' => $item['id']))->save();
            G('e4');
            echo G('begin', 'e4') . 's(4)';
//            echo M('News')->getLastSql();exit;
//            echo $item['id'].'<br/>';
//            exit;
        }
        G('end');
        echo G('begin', 'end') . 's';
        exit;
    }

    public function uploadOneImg() {
        set_time_limit(0);
        G('begin');
        $id = I('get.id');
        $list = M('News')->where(array('id' => $id))->select();
        if (empty($list)) {
            exit();
        }
        foreach ($list as $item) {
            G('e1');
            echo G('begin', 'e1') . 's(1)';
            $data = $this->replaceimg($item['html'], $item['title']);
            G('e2');
            echo G('begin', 'e2') . 's(2)';
            $size = count($data['img_list']);
            $img_list = array();
            $show_type = '1';
            if ($size >= 3) {
                $show_type = '3';
                $img_list = array_slice($data['img_list'], 0, 3);
            } else if ($size > 0) {
                $show_type = '2';
                $img_list = array_slice($data['img_list'], 0, 1);
            }
            $param = array('html' => $data['html'], 'show_type' => $show_type, 'img_status' => '2');
            if (!empty($img_list)) {
                $param['img_list'] = json_encode($img_list);
            }
            G('e3');
            echo G('begin', 'e3') . 's(3)';
            M('News')->data($param)->where(array('id' => $item['id']))->save();
            G('e4');
            echo G('begin', 'e4') . 's(4)';
//            echo M('News')->getLastSql();exit;
//            echo $item['id'].'<br/>';
//            exit;
        }
        G('end');
        echo G('begin', 'end') . 's';
        exit;
    }

    private function replaceimg($xstr, $title = '') {
        $img_arr = array();
        //匹配图片的src
        preg_match_all("/<img.*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/", $xstr, $match);
//        print_r($match);exit;
        if (count($match[1]) < 1) {
            return array('html' => $xstr, 'img_list' => $img_arr);
        }
        $i = 0;
        foreach ($match[1] as $key => $imgurl) {
            if (!empty($imgurl)) {
                //保存图片到服务器
                if (strpos($imgurl, "\"")) {
                    $imgurl = substr($imgurl, 0, strpos($imgurl, "\""));
//                    $img_info['basename'] = time().".jpg";
                }
                $img_size = $this->remote_filesize($imgurl);

                if (checkPicStatus($imgurl) == false) {
                    $picture = uploadToUpyun($imgurl);
                    if ($picture)
                        $imgurl = C('YOU_PAI_YUN') . $picture;
                }
                if ((intval($img_size) / 1024) > 8) {
                    if ($i < 4) {
                        $img_arr[] = $imgurl;
                    }
                    $i++;
                }

                $xstr = str_replace($match[0][$key], "<img alt='{$title}' title='{$title}' src='" . $imgurl . "' class='news_info_img' />", $xstr);
            }
        }
        return array('html' => $xstr, 'img_list' => $img_arr);
    }

    /**
     * CURL获取远程图片大小
     * -----------------------------------------------------------------
     */
    private function remote_filesize($uri, $user = '', $pw = '') {
        ob_start();
        $ch = curl_init($uri);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        if (!empty($user) && !empty($pw)) {
            $headers = array('Authorization: Basic ' . base64_encode($user . ':' . $pw));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $okay = curl_exec($ch);
        curl_close($ch);
        $head = ob_get_contents();
        ob_end_clean();
        $regex = '/Content-Length:\s([0-9].+?)\s/';
        $count = preg_match($regex, $head, $matches);
        return isset($matches[1]) ? $matches[1] : 'unknown';
    }

//
//    public function upYun()
//    {
//        G('begin');
//        $list = M('News')->where(array('img_status' => '2'))->order('id desc')->limit(30)->select();
//        if(count($list)<1){
//            echo 'null';exit;
//        }
//        foreach ($list as $item) {
//            $img_list = json_decode($item['img_list']);
//
//            if(count($img_list)){
//                $data = $this->upImg($item['html'],$img_list);
//            }else{
//                $data = array('html'=>$item['html'],'img_list'=>$img_list);
//            }
//            M('News')->data(array('html'=>$data['html'],'img_list' => json_encode($data['img_list']), 'img_status' => '1'))->where(array('id' => $item['id']))->save();
////            echo M('News')->getLastSql();
////            echo $item['id'];
//
//        }
//        G('end');
//        echo G('begin', 'end') . 's';
//    }
//    private function upImg($xstr,$img_list)
//    {
//        $upyun = new Upyun(C('UPLOAD_TYPE_CONFIG'));
//        $upyun->checkRootPath('./Uploads/');
//        foreach($img_list as $imgurl){
//            $img_info = pathinfo($imgurl);
//            if (strpos($imgurl, "\"")) {
//                $imgurl = substr($imgurl, 0, strpos($imgurl, "\""));
//                $img_info['basename'] = time() . ".jpg";
//            }
//            $file = array(
//                'type' => '',
//                'md5' => $imgurl,
//                'savepath' => '/' . date('Y-m-d') . '/',
//                'savename' => $img_info['basename'],
//                'tmp_name' => $imgurl,
//            );
//            $temp = $upyun->save($file);
//            if ($temp == 1) {
//                $news_img_url = '/Uploads' . $file['savepath'] . $file['savename'];
//                $img_arr[] = $news_img_url;
//                $xstr = str_replace($imgurl, C('YOU_PAI_YUN') . $news_img_url, $xstr);
//            }
//        }
//        return array('html' => $xstr, 'img_list' => $img_arr);
//    }
    public function desc() {
        G('begin');
        exit();
        $list = M('News')->field('id,title,dingyue_id,html')->where(array('is_desc' => '0'))->order('id desc')->limit(1000)->select();
        foreach ($list as $item) {
            $desc = String::msubstr($this->trimall(strip_tags($item['html'])), 0, 120);
            $data = array('html' => $item['html'], 'id' => $item['id']);
            M('NewsHtml')->data($data)->add();
            M('SpidersBaidu')->data(array('id' => $item['id'], 'title' => $item['title'], 'dingyue_id' => $item['dingyue_id'], 'is_status' => '0'))->add();
            M('News')->where(array('id' => $item['id']))->data(array('is_desc' => '1', 'intro' => $desc))->save();
        }
        G('end');
        echo G('begin', 'end');
        exit;
    }

    private function trimall($str) {//删除空格
        $qian = array(" ", "　", "\t", "\n", "\r");
        $hou = array("", "", "", "", "");
        return str_replace($qian, $hou, $str);
    }

    public function replaceimgForDm() {
        set_time_limit(0);
//        $list = M('News')->field('id,html,title')->where(array('show_type' => array('gt', 1)))->order('id desc')->limit(10)->select();
//        print_r($list);
        $list = M('News')->field('id,html,title')->where(array('show_type' => array('gt', 1), 'type_id' => '1833', 'source_name' => 'M站'))->order('id desc')->limit(531, 100)->select();
        foreach ($list as $item) {
            $xstr = $item['html'];
            $title = $item['title'];
            $img_arr = array();
            //匹配图片的src
            preg_match_all("/<img.*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/", $xstr, $match);
            if (count($match[1]) < 1) {
                return array('html' => $xstr, 'img_list' => $img_arr);
            }
            $i = 0;
            foreach ($match[1] as $key => $imgurl) {
                if (!empty($imgurl)) {
                    //保存图片到服务器
                    if (strpos($imgurl, "\"")) {
                        $imgurl = substr($imgurl, 0, strpos($imgurl, "\""));
//                    $img_info['basename'] = time().".jpg";
                    }
                    $img_size = $this->remote_filesize($imgurl);
                    if (checkPicStatus($imgurl) == false) {
                        $picture = uploadToUpyun($imgurl);
                        if ($picture)
                            $imgurl = C('YOU_PAI_YUN') . $picture;
                    } else {
                        continue;
                    }
                    if ((intval($img_size) / 1024) > 8) {
                        if ($i < 4) {
                            $img_arr[] = $imgurl;
                        }
                        $i++;
                    }
                    $xstr = str_replace($match[0][$key], "<img alt='{$title}' title='{$title}' src='" . $imgurl . "' class='news_info_img' />", $xstr);
                }
            }
            $data = array('html' => $xstr, 'img_list' => $img_arr);
            $size = count($data['img_list']);
            $img_list = array();
            $show_type = '1';
            if ($size >= 3) {
                $show_type = '3';
                $img_list = array_slice($data['img_list'], 0, 3);
            } else if ($size > 0) {
                $show_type = '2';
                $img_list = array_slice($data['img_list'], 0, 1);
            }
//            print_r(array('html'=>$data['html'],'img_list'=>$img_list,'show_type'=>$show_type,'img_status'=>'1'));exit;
            M('News')->data(array('html' => $data['html'], 'img_list' => json_encode($img_list), 'show_type' => $show_type, 'img_status' => '2'))->where(array('id' => $item['id']))->save();
//            echo M('News')->getLastSql();exit;
//            echo $item['id'].'<br/>';
//            exit;
        }
    }

}