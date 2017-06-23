<?php

namespace Home\Controller;

use Home\Model\UsersCollectModel;
use Home\Model\DingyueModel;
use Think\Controller;
use Org\Util\String;
use Port\Model\UsersModel;
use Port\Controller\Mailcontroller;

/**
 * Class UserController
 * @package Home\Controller
 */
class UserController extends HomeCommonController {

    public function _initialize() {
        parent::_initialize();
    }

    public function pin() {
        $this->assign('selected', 'pin');
        $this->display('info');
    }

    public function subscribe() {
//        $user = cookie('user_info');
        $user = $this->USER;
        $uid = $user['id'];
        if (!$uid) {
            redirect('/');
        }
        $user = M('Users')->where(array('id' => $uid))->find();
        cookie('user_info', $user);
        $this->assign('USER', $user);
        $msg = I('get.msg');
        if (empty($msg)) {
            $msg = 'collect';
        }
//        $model = new DingyueModel();
//        //用户的订阅列表
//        $data = $model->getList($user['id']);
//        foreach ($data as $k => $val) {
//            $data[$k]['url']=C('URL_DOMAIN').'/m'.$val['sid'];
//        }exit;
//        $this->assign('dingyue', $data);
//        //我的收藏
//        $collect = D('UsersCollect')->getMyCollect($uid);
//        $this->assign('collect', $collect);
//        //我的评论
//        $comment = D('news_comment')->where(array('uid'=>$uid))->select();
//        $this->assign('comment', $comment);
//        //我的好友
//        $friends = D('users_fans')->where(array('uid'=>$uid))->select();
//        $this->assign('friends', $friends);
//        $collect = $c_model->
        //选中状态
        $this->assign('selected', 'sub');
        $seo = array(
            't' => '新闻王-会员中心',
            'k' => '新闻，新闻网，新闻中心，新闻报道，最新新闻，热门新闻，体育新闻，娱乐新闻，社会新闻。',
            'd' => '《新闻王》网罗天下事，每天24小时滚动报道最新鲜，最热门的国内新闻资讯，国际新闻资讯及社会新闻等。三位一体的阅读方式，让你在任何环境下都能一手掌握国内外最新动态。'
        );
        $this->assign('msg', $msg);
        $this->assign('seo', $seo);
        $this->display('info');
    }

    /**
     * 上传头像
     */
    public function head() {
        $user = $this->USER;
        $uid = $user['id'];
        if (!$uid) {
            redirect('/');
        }
        $user = M('Users')->where(array('id' => $uid))->find();
        cookie('user_info', $user);
        $this->assign('USER', $user);
        $seo = array(
            't' => '新闻王-会员中心',
            'k' => '新闻，新闻网，新闻中心，新闻报道，最新新闻，热门新闻，体育新闻，娱乐新闻，社会新闻。',
            'd' => '《新闻王》网罗天下事，每天24小时滚动报道最新鲜，最热门的国内新闻资讯，国际新闻资讯及社会新闻等。三位一体的阅读方式，让你在任何环境下都能一手掌握国内外最新动态。'
        );
//        $this->assign('msg', $msg);
        $this->assign('seo', $seo);
        $this->display();
    }

    final public function up_pwd() {
        $old_pwd = I('post.password');  //旧密码
        $news_pwd = I('post.new_password'); //新密码
        $sure_pwd = I('post.sure_password'); //新密码
        $user = $this->USER;
        $uid = $user['id'];
        $user = M('Users')->where(array('id' => $uid))->find();
        if ($user['password'] != md5($old_pwd)) {
            $this->ajaxReturn(array('code' => 0, 'msg' => '原密码输入不对'));
        }
        if ($news_pwd != $sure_pwd) {
            $this->ajaxReturn(array('code' => 0, 'msg' => '两次密码输入不对'));
        }
        $temp = M('Users')->data(array('password' => md5($news_pwd)))->where(array('id' => $uid))->save();
        if (!$temp && M('Users')->getDbError()) {
            $this->ajaxReturn(array('code' => 0, 'msg' => '密码修改失败'));
        } else {
            $this->ajaxReturn(array('code' => 1, 'msg' => '密码修改成功'));
        }
    }

    final public function reset_pwd() {
        $username = I('post.username');
        $userEmail = I('post.email');
        $where['username'] = array('eq', $username);
        $u = new UsersModel();
        $info = $u->getInfoByName($username);
        if (!$info) {
            $this->ajaxReturn(array('code' => 0, 'msg' => '用户名不存在'));
        }
        if ($info['email'] != $userEmail) {
            $this->ajaxReturn(array('code' => 0, 'msg' => '邮箱不存在'));
        }

        if ($userEmail) {
            $passwd = String::randString(6, 1);
            M('Users')->where($where)->save(array('password' => md5($passwd)));
            $mail = new \Port\Controller\MailController('smtp.126.com', 25, 'ydleternal@126.com', 'nwcziontig');
            $mail->isHTML();
            $mail_body = '<div style="width:680px;padding:0 10px;margin:0 auto;">'
                    . '<div style="line-height:1.5;font-size:14px;margin-bottom:25px;color:#4d4d4d;">'
                    . '<strong style="display:block;margin-bottom:15px;">亲爱的 ' . $username . ' 用户： 您好！'
                    . '</strong>'
                    . '<p>您的新密码为：' . $passwd . '，感谢您对新闻王的支持</p>';
            $mail->send('新闻王', 'ydleternal@126.com', $userEmail, '新闻王 - 更懂你', $mail_body);
        }
        $this->ajaxReturn(array('code' => 1, 'msg' => '修改成功'));
    }

    final public function add_fans() {
        $uid = $this->USER['id'];
        $fuid = I('post.fuid');
        $temp = M('UsersFans')->where(array('uid' => $uid, 'fuid' => $fuid))->find();
        if ($temp) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '你已有该好友'));
        }
        $data = array(
            'uid' => $uid,
            'fuid' => $fuid,
            'add_time' => time()
        );
        $temp = M('UsersFans')->data($data)->add();
        if (!$temp && M('UsersFans')->getDbError()) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '添加失败'));
        }
        $this->ajaxReturn(array('code' => 200, 'msg' => '添加成功'));
    }

    final public function del_fans() {
        $uid = $this->USER['id'];
        $fuid = I('post.fuid');
        if (!$uid) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '您未登录'));
        }
        $where = array(
            'uid' => $uid,
            'fuid' => $fuid
        );
        $temp = M('UsersFans')->where($where)->delete();
        if (!$temp && M('UsersFans')->getDbError()) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '删除失败'));
        }
        $this->ajaxReturn(array('code' => 200, 'msg' => '删除成功'));
    }

    /**
     * 修改签名
     */
    final public function up_sign() {
        $sign = I('post.sign');
        $user = $this->USER;
        $uid = $user['id'];
        if (!$uid) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '您未登录'));
        }
        $temp = M('Users')->data(array('singn' => $sign))->where(array('id' => $uid))->save();
        if (!$temp && M('Users')->getDbError()) {
            $this->ajaxReturn(array('code' => 0, 'msg' => '签名修改失败'));
        } else {
            $this->ajaxReturn(array('code' => 200, 'msg' => '签名修改成功'));
        }
    }

    /**
     * 取消订阅
     */
    final public function del_dy() {
        $user = $this->USER;
        $uid = $user['id'];
        if (!$uid) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '您未登录'));
        }
        $where = array(
            'sid' => intval(I('post.did')),
            'uid' => $uid
        );
        $temp = M('UsersDingyue')->where($where)->delete();
        if (!$temp && M('UsersDingyue')->getDbError()) {
            $this->ajaxReturn(array('code' => 0, 'msg' => '取消订阅失败'));
        } else {
            $this->ajaxReturn(array('code' => 200, 'msg' => '取消订阅成功'));
        }
    }

    /**
     * 订阅排序
     */
    final public function sort_dy() {
        $user = $this->USER;
        $uid = $user['id'];
        if (!$uid) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '您未登录'));
        }
        $sortId = intval(I('post.sort_id'));
        $id = intval(I('post.id'));
        $where = array(
            'sort_id' => array('gt', $sortId),
            'uid' => $uid
        );
        $n_dy = M('UsersDingyue')->field('id,sort_id')->where($where)->order('sort_id')->find();
        if (empty($n_dy)) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '已经是第一位'));
        }
        $temp = M('UsersDingyue')->where(array('id' => $id))->save(array('sort_id' => $n_dy['sort_id']));
        $temp1 = M('UsersDingyue')->where(array('id' => $n_dy['id']))->save(array('sort_id' => $sortId));
        if (!$temp && !$temp && M('UsersDingyue')->getDbError()) {
            $this->ajaxReturn(array('code' => 0, 'msg' => '操作失败'));
        } else {
            $this->ajaxReturn(array('code' => 200, 'msg' => '操作成功'));
        }
    }

    /**
     * 取消收藏
     */
    final public function del_col() {
        $user = $this->USER;
        $uid = $user['id'];
        if (!$uid) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '您未登录'));
        }
        $where = array(
            'id' => intval(I('post.id')),
            'uid' => $uid
        );
        $temp = M('UsersCollect')->where($where)->delete();
        if (!$temp && M('UsersCollect')->getDbError()) {
            $this->ajaxReturn(array('code' => 0, 'msg' => '取消收藏失败'));
        } else {
            $this->ajaxReturn(array('code' => 200, 'msg' => '取消收藏成功'));
        }
    }

    final public function getUserRecomend() {
        $page = max(I('request.p'), 1);
        $limit = I('request.l') ? I('request.l') : '6';
        $user = $this->USER;
        $start = ($page - 1) * $limit;
        $sql = 'select * from (select * from xw_users where id not in '
                . '(SELECT fuid from xw_users_fans where uid =' . $user['id'] . ')'
                . ' order by add_time desc) as u  limit ' . $start . ',' . $limit;
        $recomend = D('news_comment')->query($sql);

        if (empty($recomend)) {
            $sql = 'select * from (select * from xw_users where id not in '
                    . '(SELECT fuid from xw_users_fans where uid =' . $user['id'] . ')'
                    . ' order by add_time desc) as u  limit 0,' . $limit;
            $recomend = D('news_comment')->query($sql);
            $this->assign('recomend', $recomend);
            $html = $this->fetch('Widget:User/recomend');
            $this->ajaxReturn(array('code' => 201, 'html' => $html));
        } else {
            $this->assign('recomend', $recomend);
            $html = $this->fetch('Widget:User/recomend');
            $this->ajaxReturn(array('code' => 200, 'html' => $html));
        }
    }

    /**
     * 反馈信息
     */
    final public function feedback() {
        $user = $this->USER;
        $uid = $user['id'];
        if (!$uid) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '您未登录'));
        }
        $data = array(
            'contact' => I('post.contact'),
            'msg' => I('post.msg'),
            'add_time' => date('Y-m-d H:i:s'),
            'uid' => $uid
        );
        $temp = M('Feedback')->add($data);
        if (!$temp && M('Feedback')->getDbError()) {
            $this->ajaxReturn(array('code' => 0, 'msg' => '反馈失败'));
        } else {
            $this->ajaxReturn(array('code' => 200, 'msg' => '反馈成功'));
        }
    }

}