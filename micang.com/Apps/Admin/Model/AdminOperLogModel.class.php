<?php

namespace Common\Model;

use Think\Model;

final class AdminOperLogModel extends Model {

    /**
     * 添加日志
     * @param type $mid
     * @param type $type  类型 0  实名认证   1  代购  2 中介 3 提现  4  域名后缀  5 模板 6 域名价格 7友情链接   8  图片   9资讯 10 管理员账号
     * @param type $title
     * @param type $content
     * @return boolean
     */
    final public function addLog($mid, $type, $title, $content, $oper_id = '', $oper_remark = '') {
        $data['admin_id'] = $mid;
        $data['type'] = $type;
        $data['title'] = $title;
        $data['note'] = $content;
        $data['ip'] = get_client_ip();
        $data['url'] = $_SERVER['REQUEST_URI'];
        $data['addtime'] = time();
        $data['oper_id'] = $oper_id;
        $data['oper_remark'] = $oper_remark;
        $result = $this->add($data);
        if (!$result || $this->getDbError()) {
            return false;
        }
        return $result;
    }

}