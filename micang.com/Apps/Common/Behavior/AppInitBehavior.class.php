<?php
/**
 * 项目配置初始化
 * @author Jansen<6206574@qq.com>
 * @copyright 258.com
 */
namespace Common\Behavior;
use Think\Behavior;
final class AppInitBehavior extends Behavior {
    public function run(&$params) {
        $this->checkRunScene();
        //$this->checkMobile();
    }
    /**
     * 自动检测是生产环境还是开发测试环境
     */
    private function checkRunScene(){
        if (strpos(I('server.HTTP_HOST'), '.beta') > 0){
            define('RUN_IN_DEVELOP', true);
        }elseif (strpos(I('server.HTTP_HOST'), '.dev') > 0){
            define('RUN_IN_DEVELOP', true);
        }else{
            define('RUN_IN_DEVELOP', false);
        }
    }
    private function checkMobile() {
        if (strpos(I('server.HTTP_HOST'), 'www.') === false)    return false;
        if (preg_match('/IUC|JUC/i', I('server.HTTP_USER_AGENT'))) {
            header("Content-type: text/html; charset=UTF-8");
            $msg = '我们检测到您正在使用UC浏览器的“极速模式”，为了使您能更好的浏览网站信息，请关闭“极速模式”。<br />';
            $msg .= '关闭方法：<br />';
            $msg .= 'IPHONE：浏览器菜单&raquo;“设置”选项卡&raquo;“极速模式”<br />';
            $msg .= 'Android：点击“=”号图标&raquo;右上角向下箭头&raquo;“极速模式”';
            exit($msg);
        }
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])){
            $this->gotoMobileWeb();
        }
        //此条摘自TPM智能切换模板引擎，适合TPM开发
        if(isset($_SERVER['HTTP_CLIENT']) && 'PhoneClient'==$_SERVER['HTTP_CLIENT']){
            $this->gotoMobileWeb();
        }
        //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], 'wap')){
            $this->gotoMobileWeb();
        }
        //判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array('iphone','ipad','ios','android','Windows Phone OS','WPOS','WP7','IEMobile','BlackBerry','nokia');
            //从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(".implode('|', $clientkeywords).")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                $this->gotoMobileWeb();
            }
        }
        //协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                $this->gotoMobileWeb();
            }
        }
    }
    private function gotoMobileWeb() {
        header('Location: http://m.'.str_replace('www.', '', I('server.HTTP_HOST')));
        exit();
    }
}

?>