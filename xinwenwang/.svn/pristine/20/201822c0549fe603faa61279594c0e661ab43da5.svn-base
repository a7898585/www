<?php

namespace Home\Controller;

use Think\Upload\Driver\Upyun;

/**
 * Class AppController
 * @package Home\Controller
 */
class UpController extends HomeCommonController {

    public function headpic() {
        $user = $this->USER;
        $uid = $user['id'];
        header('Content-Type: text/html; charset=utf-8');
        $result = array();
        $result['success'] = false;
        $success_num = 0;
        $msg = '';
//上传目录
        $dir = $_SERVER['DOCUMENT_ROOT'] . "\Uploads";

// 取服务器时间+8位随机码作为部分文件名，确保文件名无重复。
        $filename = date("YmdHis") . floor(microtime() * 1000) . createRandomCode(8);
// 处理原始图片开始------------------------------------------------------------------------>
//默认的 file 域名称是__source，可在插件配置参数中自定义。参数名：src_field_name
        $source_pic = $_FILES["__source"];
        $sourceFileName = $source_pic["name"];

        //原始文件的扩展名(不包含“.”)
        $sourceExtendName = substr($sourceFileName, strripos($sourceFileName, "."));
//如果在插件中定义可以上传原始图片的话，可在此处理，否则可以忽略。
        if ($source_pic) {
//            if ($source_pic['error'] > 0) {
//                $msg .= $source_pic['error'];
//            } else {
//                //原始图片的文件名，如果是本地或网络图片为原始文件名、如果是摄像头拍照则为 *FromWebcam.jpg
//                //保存路径
//                $savePath = "$dir\php_source_$filename." . $sourceExtendName;
//                //当前头像基于原图的初始化参数（只有上传原图时才会发送该数据，且发送的方式为POST），用于修改头像时保证界面的视图跟保存头像时一致，提升用户体验度。
//                //修改头像时设置默认加载的原图url为当前原图url+该参数即可，可直接附加到原图url中储存，不影响图片呈现。
//                $init_params = $_POST["__initParams"];
//                $result['sourceUrl'] = toVirtualPath($savePath) . $init_params;
//                move_uploaded_file($source_pic["tmp_name"], $savePath);
//                $success_num++;
//            }
        }
//<------------------------------------------------------------------------处理原始图片结束
// 处理头像图片开始------------------------------------------------------------------------>
//头像图片(file 域的名称：__avatar1,2,3...)。
        $avatars = array("__avatar1", "__avatar2", "__avatar3");
        $avatars_length = count($avatars);
        $avatar = $_FILES[$avatars[0]];
        $picurl = '';
        for ($i = 0; $i < $avatars_length; $i++) {
            $avatar_number = $i + 1;
            if ($avatar['error'] > 0) {
                $msg .= $avatar['error'];
            } else {
                if (!$picurl) {
                    $avatar['name'] = $filename . '.jpg';
//                    print_r($avatar);
                    $picurl = uploadPhoto($avatar);
//                    print_r($picurl);
                    if ($picurl['status'] == 1) {
                        $picurl = $picurl['url'];
                        $temp = M('Users')->data(array('head_pic' => $picurl))->where(array('id' => $uid))->save();
                    }
                } else {
                    $result['sourceUrl'] = setUpUrl($picurl);
                    $success_num++;
                }
            }
        }
//        for ($i = 0; $i < $avatars_length; $i++) {
//            $avatar = $_FILES[$avatars[$i]];
//            $avatar_number = $i + 1;
//            if ($avatar['error'] > 0) {
//                $msg .= $avatar['error'];
//            } else {
//                $savePath = "$dir\php_avatar" . $avatar_number . "_$filename.jpg";
//                $result['avatarUrls'][$i] = toVirtualPath($savePath);
//                move_uploaded_file($avatar["tmp_name"], $savePath);
//                $success_num++;
//            }
//        }
//<------------------------------------------------------------------------处理头像图片结束
//upload_url中传递的额外的参数，如果定义的method为get请换为$_GET
        $result["userid"] = $uid;
        $result["username"] = $user["username"];

        $result['msg'] = $msg;
        if ($success_num > 0) {
            $user['head_pic'] = $picurl;
            cookie('user_info', $user);
            $result['success'] = true;
        }
//返回图片的保存结果（返回内容为json字符串）
        echo json_encode($result);
        exit;
    }

}