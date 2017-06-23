<?php

namespace Home\Controller;

use Think\Upload\Driver\Upyun;

/**
 * Class AppController
 * @package Home\Controller
 */
class AppController extends HomeCommonController {

    public function index() {
        if (isMobile()) {
            header("Location:" . str_replace('www', 'm', C('URL_DOMAIN')) . "");
        }
        $seo = array(
            't' => '下载中心_新闻_新闻网_新闻资讯_新闻中心-新闻王',
            'k' => '下载中心，新闻，新闻网，新闻中心，新闻报道，最新新闻，热门新闻，体育新闻，娱乐新闻，社会新闻',
            'd' => '《新闻王》网罗天下事，每天24小时滚动报道最新鲜，最热门的国内新闻资讯，国际新闻资讯及社会新闻等。三位一体的阅读方式，让你在任何环境下都能一手掌握国内外最新动态'
        );
        $this->assign('seo', $seo);
        $this->display();
    }

    public function uploadPic() {

        $imgurl = 'http://static.missevan.cn/boardimgs/2015/07/29/14381396402346.jpg';

        echo C('YOU_PAI_YUN') . uploadToUpyun($imgurl);
        exit;
        $upyun = new Upyun(C('UPLOAD_TYPE_CONFIG'));
        $upyun->checkRootPath('./Uploads/');
        $imgurl = 'http://static.missevan.cn/boardimgs/2015/07/29/14381396402346.jpg';
        $img_info = pathinfo($imgurl);
        if (strpos($imgurl, "\"")) {
            $imgurl = substr($imgurl, 0, strpos($imgurl, "\""));
            $img_info['basename'] = time() . ".jpg";
        }
        $file = array(
            'type' => '',
            'md5' => $imgurl,
            'savepath' => '/' . date('Y-m-d') . '/',
            'savename' => $img_info['basename'],
            'tmp_name' => $imgurl,
        );
        $temp = $upyun->save($file);
        var_dump($temp);
        $news_img_url = '/Uploads' . $file['savepath'] . $file['savename'];
        echo C('YOU_PAI_YUN') . $news_img_url;
        exit;
        if ($temp == 1) {
            $news_img_url = '/Uploads' . $file['savepath'] . $file['savename'];
            $img_arr[] = $news_img_url;
            $xstr = str_replace($imgurl, C('YOU_PAI_YUN') . $news_img_url, $xstr);
        }
        exit;
//            
//        $url = 'http://static.missevan.cn/boardimgs/2015/07/29/14381396402346.jpg';
//        $tempu = parse_url($url);
////        print_r($tempu);exit;
//        $saveName = $tempu['path'];
//        $ext = strrchr($url, ".");
//        if ($ext != ".gif" && $ext != ".jpg" && $ext != ".png")
//            return false;
//        $saveName = date("YmdHis") . $ext;
//        $filename = './Public/upload/' . $saveName;
//        getImg($url, $filename);
        $saveName = '2015072915172336.jpg';
        $filename = './Public/upload/20150729151726.jpg';
//        exit;
        $upyun = new Upyun(C('UPLOAD_TYPE_CONFIG'));
        $upyun->checkRootPath('./Uploads/');
//        $value['photo_thumb_path'] = '/images/user/no_photo_user.jpg';
//            $array = explode('/', $value['photo_thumb_path']);
//            print_r($array);
//            print_r(array_pop($array));
//        $tempu = parse_url($url);
//        print_r($tempu);exit;
//        $saveName = $filename;
        $savepath = '/news/' . date('Ymd') . '/';

        $file = array(
            'type' => 'image/jpeg',
            'md5' => md5('/Public/upload/20150729151726.jpg'),
            'savepath' => $savepath,
            'savename' => $saveName,
            'tmp_name' => $filename,
        );
        print_r($file);
//            exit;
        $temp = $upyun->save($file, true);
        var_dump($temp);
    }

}