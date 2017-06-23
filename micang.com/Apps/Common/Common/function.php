<?php

/**
 * function.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-09-29
 */
function SendMail($address, $title, $message, $fromname = 'NONE') {
    $mail = new Common\Extend\PHPMailer\PHPMailer();
    $mail->IsSMTP();
    $mail->CharSet = C('MAIL_CONFIG.MAIL_CHARSET');
    $mail->AddAddress($address);
    $mail->Body = $message;
    $mail->From = C('MAIL_CONFIG.MAIL_ADDRESS');
    $mail->FromName = $fromname;
    $mail->Subject = $title;
    $mail->Host = C('MAIL_CONFIG.MAIL_SMTP');
    $mail->SMTPAuth = C('MAIL_CONFIG.MAIL_AUTH');
    $mail->Username = C('MAIL_CONFIG.MAIL_LOGINNAME');
    $mail->Password = C('MAIL_CONFIG.MAIL_PASSWORD');
    $mail->IsHTML(C('MAIL_CONFIG.MAIL_HTML'));
    if (!$mail->Send()) {
        $logFilePath = C('LOG_PATH') . 'mail_' . date('Ymd') . '.log';
        $message = "\n收件地址：" . $address . "\n";
        $message .= "报错信息：" . $mail->ErrorInfo . "\n";
        $message .= "发送时间：" . date('Y-m-d H:i:s') . "\n";
        $message .= "===============================================";
        \Think\Log::write($message, \Think\Log::INFO, '', $logFilePath);
        return false;
    }
    return true;
}

/*
 * 检测域名是否已经存在
 * return 1:已经存在 0:不存在 2：已经在您的账户
 */

function checkDomainexist($domain, $mid = false) {
    $where['domain'] = $domain;
    $result = M("MembersDomains")->where($where)->find();
    if ($result['mid'] == $mid) {
        return 2;
    }
    return $result ? 1 : 0;
}

/**
 * 检测是否手机号码
 *
 * @param
 *            $value
 * @return bool
 */
function isMobile($value) {
    $reg = '/^13[0-9]{1}[0-9]{8}$|14[5|7][0-9]{8}$|15[0|1|2|3|5|6|7|8|9][0-9]{8}$|17[00|05|09]{2}[0-9]{7}$|18[0-9]{1}[0-9]{8}$/';
    return preg_match($reg, $value) ? true : false;
}

/**
 * 上传图片
 *
 * @param type $file            
 * @param type $rootPath            
 * @return type
 */
function uploadPhoto($file, $rootPath = 'Uploads') {
    $root_path = './' . $rootPath . '/';
    $config = array('rootPath' => $root_path, // 保存根路径
        'mimes' => array('application/octet-stream', 'image/gif', 'image/jpeg', 'image/png'), // 允许上传的文件MiMe类型
        'maxSize' => 2097152, // 上传的文件大小限制 (0-不做限制)
        'exts' => array(), // 允许上传的文件后缀
        'saveName' => array('getPicName', array('10000', '99999')), // 上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'replace' => true);
    $mm = new \Think\Upload($config);
    $m = $mm->uploadOne($file);
    if ($m) {
        return array('status' => 1, 'url' => '/' . $rootPath . '/' . $m['savepath'] . $m['savename']);
    } else {
        return array('status' => 0, 'msg' => $mm->getError());
    }
}

/**
 * 设置图片名称
 *
 * @param type $min            
 * @param type $max            
 * @return type
 */
function getPicName($min, $max) {
    return date('YmdHis') . rand($min, $max);
}

/**
 * 生成upaiyun上传相关参数
 */
function getUpaiyunConfig($name = 'idcard') {
    $upaiyun = C('UPAIYUN_CONFIG.UPYUN_UPLOAD_CONFIG');
    $upload['bucket'] = $upaiyun['buckets'];
    $upload['expiration'] = time() + $upaiyun['expired'];
    $upload['save-key'] = '/' . $name . '/' . date('Ym') . '/{random}{.suffix}';
    $upload['allow-file-type'] = $upaiyun['suffix'];
    $data['policy'] = base64_encode(json_encode($upload));
    $data['signature'] = md5(implode('&', array($data['policy'], $upaiyun['secret_key'])));
    return $data;
}

/**
 * 发送忘记密码邮件
 *
 * @param type $email            
 */
function send_forget_email($email) {
    $code = md5(md5($email . date('Y-m-d')));
    $search = array('%ACTIVATE_URL%', '%DATE%', '%SYSTEM_URL%', '%ACTIVATE_EMAIL%');
    $activateUrl = 'http://' . I('server.HTTP_HOST') . '/public/active_password?code=' . $code . '&email=' . $email;
    $replace = array($activateUrl, date('Y-m-d'), 'http://' . I('server.HTTP_HOST'), $email);
    $mailContent = str_replace($search, $replace, C('MAIL_CONFIG.ACTIVE_PASSWORD_EMAIL_CONTENT'));
    $result = SendMail($email, C('MAIL_CONFIG.ACTIVE_PASSWORD_EMAIL_TITLE'), $mailContent, '米仓网客户服务中心');
    if ($result) {
        $result = D('MobileCodes')->addEmailSms($email, $code, date('YmdHis') . rand(1000, 9999));
    }
    return $result;
}

/*
 * 检查安全码是否到期
 * 1:可用
 * 0:已经超时
 */

function isSeccodeTimeout($historyBack = true) {
    if (intval(session('MEMBER_SECCODE_TIMEOUT')) > time()) {
        return 1;
    }
    if ($historyBack) {
        echo '<script type="text/javascript" src="/Public/Common/js/jquery-migrate-1.2.1.min.js"></script>';
        echo '<script type="text/javascript" src="/Public/Common/js/layer/layer.js"></script>';
        echo '<scritp>layer.alert("安全码失效，请重新输入",function(){history.back();});</script>';
    }
    return 0;
}

/*
 * 手续续费计算
 * 百分比的手续费最少一元
 */

function Fee($money, $id) {
    $m = M("Config")->find('trade_fee_' . $id);
    if (!$m) {
        return 0;
    }
    if ($m['type'] == 'absolute') {
        return $m['value'];
    }
    return max(100, intval($money) * $m['value']);
}

/*
 * Percent Fee ,获取手续费百分比
 */

function FeePercent($id) {
    $m = M("Config")->find('trade_fee_' . $id);
    if (!$m) {
        return 0;
    }
    if ($m['type'] == 'absolute') {
        return $m['value'] / 100;
    }
    return ($m['value'] * 100) . "%";
}

/*
 * 金钱数字格式化显示
 */

function MoneyFormat($money) {
    $money = Fen2Yuan($money);
    if ($money < 10000) {
        return $money . "元";
    }
    $wan = intval($money / 10000);
    $else = $money % 10000;
    if ($else)
        return $wan . "万" . $else . "元";
    return $wan . "万";
}

/*
 * 用户账户总金额
 */

function UserTotalMoney($mid) {
    return M("MembersMoney")->where(array('mid' => $mid))->getField("total_money");
}

/*
 * 拍卖出价
 */

function ChuJia($sale_id, $price, $mid, $memo) {
    // 检测是否开启了事务
    if (!isTransactionBegin())
        return 0;
    $saleDetail = M("MembersDomainSales")->find($sale_id);
    if (!$saleDetail) {
        return "拍卖信息获取失败";
    }
    //必须高于保留价
    if (!$saleDetail['buyer_mid']) {
        //还没人出价,必须大于起拍价
        if ($saleDetail['seller_price'] > $price) {
            return "起拍价是" . Fen2Yuan($saleDetail['seller_price']) . "元";
        }
    }
    if ($saleDetail['buyer_mid'] == $mid) {
        return "当前已经领先，无需再出价";
    }
    // 检查用户账户是有足够款冻结
    $nowlockMoney = Bond($price);
    if (UserTotalMoney($mid) < $nowlockMoney) {
        return '账户可用余额不足';
    }
    $auction_log['pid'] = $sale_id;
    $auction_log['uid'] = $mid;
    $auction_log['price'] = $price;
    $auction_log['lock_money'] = $nowlockMoney;
    $auction_log['update_time'] = time();
    $auction_log['memo'] = $memo;
    $result = M("DomainAuctionLog")->data($auction_log)->add();
    if ($result) {
        // 解锁上一个用户的钱
        $where['pid'] = $sale_id;
        $where['uid'] = $saleDetail['buyer_mid'];
        $where['lock_money'] = array('gt', '0');
        $lockMoney = M("DomainAuctionLog")->where($where)->find();
        if ($lockMoney) {
            $unLockStatus = LockUnLockMoney($saleDetail['buyer_mid'], $lockMoney['lock_money'], 'unlock');
            if ($unLockStatus == 1) {
                $lockMoney['lock_money'] = 0;
                $result = M("DomainAuctionLog")->data($lockMoney)->save();
                if (!$result) {
                    return "竞价日志更新失败，error:001";
                }
            } else {
                return $unLockStatus;
            }
        }
        // 锁定当前用户的钱
        $lockStatus = LockUnLockMoney($mid, $auction_log['lock_money']);
        if ($lockStatus != '1') {
            return "竞价日志更新失败，error:003";
        }
        $data = array('lock_to' => $nowlockMoney, 'buyer_price' => $auction_log['price'], 'buyer_mid' => $mid);
        // 标记当前领先,以及冻结款
        $result = M("MembersDomainSales")->where(array('id' => $saleDetail['id']))->data($data)->save();
        if (!$result) {
            return "竞价日志更新失败，error:004";
        }
        // 出价次数标记+1
        $result = IncreaseTimes($sale_id);
        if (!$result) {
            return "竞价日志更新失败，error:005";
        }
        return 1;
    } else {
        return "竞价日志更新失败，error:006";
    }
}

/*
 * 保证金计算
 * 传入单位：分
 * 返回默认是 分
 * return传入 1，返回为元
 */

function Bond($money, $return = 100) {
    $i = Fen2Yuan($money);
    if ($i < 200) {
        return 20 * $return;
    }
    if ($i < 1000) {
        return 50 * $return;
    }
    if ($i < 5000) {
        return 100 * $return;
    }
    if ($i < 20000) {
        return 500 * $return;
    }
    if ($i < 50000) {
        return 1000 * $return;
    }
    if ($i < 100000) {
        return 2000 * $return;
    }
    if ($i < 500000) {
        return 5000 * $return;
    }
    return 10000 * $return;
}

/*
 * 加价幅度计算
 * 传入单位：分
 * 返回默认是 分
 * return传入 1，返回为元
 */

function IncreaseRange($money, $return = 100) {
    $i = Fen2Yuan($money);
    if ($i < 50) {
        return 1 * $return;
    }
    if ($i < 500) {
        return 10 * $return;
    }
    if ($i < 1000) {
        return 50 * $return;
    }
    if ($i < 5000) {
        return 100 * $return;
    }
    if ($i < 10000) {
        return 200 * $return;
    }
    if ($i < 30000) {
        return 500 * $return;
    }
    if ($i < 100000) {
        return 1000 * $return;
    }
    if ($i < 300000) {
        return 2000 * $return;
    }
    return 5000 * $return;
}

/*
 * 锁钱，返钱
 * 参数：uid
 * 金钱：money
 * 类型:lock ,unlock
 * 必须启动事务
 */

function LockUnLockMoney($uid, $money, $type = 'lock') {
    // 检测是否开启了事务
    if (!isTransactionBegin())
        return 0;
    $memberMoney = M("MembersMoney")->find($uid);
    // print_r($uid);exit();
    if ($type == 'unlock') {

        // 解锁
        if ($memberMoney['lock_money'] < $money) {
            return '冻结金额解锁错误,error:001'; // 账户锁定金额小于
        }
        $memberMoney['total_money'] = $memberMoney['total_money'] + $money;
        $memberMoney['lock_money'] = $memberMoney['lock_money'] - $money;
        // print_r($memberMoney);
        $result = M("MembersMoney")->data($memberMoney)->save();
        if ($result) {
            return 1;
        }
    } else {
        // 上锁
        if ($memberMoney['total_money'] < $money) {
            return '账户可用余额不足'; // 账户可用金额小于锁定金额
        }
        $memberMoney['total_money'] = $memberMoney['total_money'] - $money;
        $memberMoney['lock_money'] = $memberMoney['lock_money'] + $money;
        $result = M("MembersMoney")->data($memberMoney)->save();
        if ($result) {
            return 1;
        }
    }
    return '系统出错';
}

/*
 * 分变成元
 */

function Fen2Yuan($fen) {
    return $fen / 100;
}

/*
 * 结束时间转换为 *时*分*秒
 */

function EndTime2Str($end_time) {
    if (!$end_time) {
        return '--';
    }
    $second = $end_time - time();
    if ($second <= 0) {
        return '结束';
    }
    $str = null;
    $day = floor($second / (3600 * 24));
    if ($day) {
        $str .= $day . '天';
    }
    $second = $second % (3600 * 24); // 除去整天之后剩余的时间
    $hour = floor($second / 3600);
    if ($hour) {
        $str .= $hour . '小时';
    }
    $second = $second % 3600; // 除去整小时之后剩余的时间
    $minute = floor($second / 60);
    if ($minute) {
        $str .= $minute . '分';
    }
    $second = $second % 60; // 除去整分钟之后剩余的时间
    if ($second) {
        $str .= $second . '秒';
    }
    // 返回字符串
    return $str;
}

/**
 * 域名过户,只过户不改whois
 *
 * @param number $type
 *            过户类型 1:PUSH 2:一口价 3:竞价 4:询价
 * @param array $domain
 *            待转移域名
 * @param number $from
 *            域名所属用户ID
 * @param number $to
 *            转移到用户ID
 * @param number $money
 *            金额,单位:分
 * @param string $remark
 *            用于生成消费和收入日志的备注信息,一般填写关联的ID
 * @return number 1 过户成功
 *         0 未开启事务
 *         -1 余额不足
 *         -2 修改域名表的域名状态失败
 *         -3 付款失败
 *         -4 收款失败
 *         -5 生成消费日志失败
 *         -6 生成收入日志失败
 */
function domainTransfer($type, array $domain, $from, $to, $money, $remark) {
    // 检测是否开启了事务
    if (!isTransactionBegin())
        return 0;
    $transferType = array(1 => 'push', 2 => 'price', 3 => 'bidding', 4 => 'inquiry');
    // 检测余额是否足够
    if ($money > 0) {
        $memberMoney = M('MembersMoney')->where(array('mid' => $to))->find();
        if ($memberMoney['total_money'] < $money)
            return -1;
    }
    // 转移域名并且变更域名状态
    unset($where);
    $data['status'] = '100';
    $data['mid'] = $to;
    $where['domain'] = array('IN', $domain);
    $where['mid'] = $from;
    $result = M('MembersDomains')->where($where)->data($data)->save();
    if (!$result) {
        M('MembersDomains')->rollback();
        return -2;
    }
    if ($money > 0) {
        // 付钱
        unset($where, $result, $data);
        $data['mid'] = $to;
        $data['total_money'] = array('exp', 'total_money-' . $money);
        if ($money <= $memberMoney['recharge_money']) {
            $data['recharge_money'] = array('exp', 'recharge_money-' . $money);
        } elseif ($money > $memberMoney['recharge_money'] && $memberMoney['recharge_money'] > 0) {
            // 充值余额字段大于0,但余额小于域名价格
            $tradeMoney = $money - $memberMoney['recharge_money'];
            $data['recharge_money'] = 0;
            $data['trade_money'] = array('exp', 'trade_money-' . $tradeMoney);
        } else {
            $data['trade_money'] = array('exp', 'trade_money-' . $money);
        }
        $result = M('MembersMoney')->data($data)->save();
        if (!$result) {
            M('MembersDomains')->rollback();
            return -3;
        }
        // 收钱
        unset($where, $data, $result);
        // 系统手续费收入
        $fee = Fee($money, $type);
        if ($fee) {
            unset($where, $data, $result);
            $result = PlatformIncome($fee, $from, $domain, $type, $remark);
            if (!$result) {
                M('MembersDomains')->rollback();
                return -7;
            }
        }
        $realIncomeMoney = $money - $fee;
        $data['mid'] = $from;
        $data['total_money'] = array('exp', 'total_money+' . $realIncomeMoney);
        $data['trade_money'] = array('exp', 'trade_money+' . $realIncomeMoney);
        $result = M('MembersMoney')->data($data)->save();
        if (!$result) {
            M('MembersDomains')->rollback();
            return -4;
        }
    }
    // 生成消费日志
    unset($where, $data, $result);
    $data['mid'] = $to;
    $data['type'] = $transferType[$type];
    $data['from'] = 'micang'; // 域名注册商
    $data['time'] = time();
    $data['money'] = $money;
    $data['content'] = $remark;
    $result = M('MembersConsumeDetail')->data($data)->add();
    if (!$result) {
        M('MembersDomains')->rollback();
        return -5;
    }
    // 生成收入日志
    unset($where, $data, $result);
    $data['mid'] = $from;
    $data['type'] = $transferType[$type];
    $data['time'] = time();
    $data['money'] = $money - Fee($money, $type);
    $data['content'] = $remark;
    $result = M('MembersIncomeDetail')->data($data)->add();
    if (!$result) {
        M('MembersDomains')->rollback();
        return -6;
    }

    return 1;
}

/*
 * sale表删除，移动到sales_finish
 */

function DelDomainSales($sale) {
    if (is_numeric($sale)) {
        $data = M("MembersDomainSales")->find($sale);
    } else {
        $data = M("MembersDomainSales")->where(array('domain' => $sale))->find();
    }
    if (!$data) {
        return false;
    }
    $result = M("MembersDomainSales")->where(array('id' => $data['id']))->delete();
    if (!$result) {
        return false;
    }
    // 删除关注
    M("MembersDomainNotice")->where(array('sale_id' => $data['id']))->delete();
    $result = M("MembersDomainSalesFinish")->data($data)->add();
    if ($result) {
        return '1';
    }
    return false;
}

/*
 * 出价次数+1
 */

function IncreaseTimes($sale_id) {
    $saleDetail = M('MembersDomainSales')->find($sale_id);
    if ($saleDetail['end_time'] <= time() + 300 && $saleDetail['type'] != '3') {
        // 标记5分钟后超时
        $add_time = time() + 300 - $saleDetail['end_time'];
        M('MembersDomainSales')->where(array('id' => $sale_id))->setInc('end_time', $add_time);
    }
    return M('MembersDomainSales')->where(array('id' => $sale_id))->setInc('times', 1);
}

/*
 * 平台收入操作函数
 */

function PlatformIncome($income, $from_mid, $domain, $type, $memo) {
    // 检测是否开启了事务
    if (!isTransactionBegin())
        return 0;
    $data['income'] = $income;
    $data['from_mid'] = $from_mid;
    $data['domain'] = $domain;
    $data['type'] = $type;
    $data['memo'] = $memo;
    return M('PlatformIncome')->data($data)->add();
}

/*
 * 用户收入操作函数
 * 返回 1成功,-1收入表写入失败 ,-2 Money表写入失败
 */

function UserIncome($money, $mid, $type, $memo) {
    // 检测是否开启了事务
    if (!isTransactionBegin())
        return 0;
    $data['mid'] = $mid;
    $data['type'] = $type;
    $data['time'] = time();
    $data['money'] = $money;
    $data['content'] = $memo;
    $result = M("MembersIncomeDetail")->data($data)->add();
    unset($data);
    if (!$result) {
        return -1;
    }
    $memberMoney = M("MembersMoney")->find($mid);
    $memberMoney['total_money'] = $memberMoney['total_money'] + $money;
    $memberMoney['trade_money'] = $memberMoney['trade_money'] + $money;
    $result = M("MembersMoney")->data($memberMoney)->save();
    if ($result) {
        return 1;
    }
    return -2;
}

/*
 * 检查域名有接入到我们平台
 */

function isDomainTransfer($domain) {
    $result = M("MembersDomains")->where(array('domain' => $domain))->find();
    if ($result['is_transfer'] == '1') {
        return true;
    }
    return false;
}

/*
 * 交易解冻双方锁定的金额
 */

function TradeUnLockTwo($trade_id) {
    if (!isTransactionBegin())
        return 0;
    $tradeDetail = M("DomainTradeLog")->find($trade_id);
    if ($tradeDetail['lock_from']) {
        $result = TradeUnLockMoney($trade_id, 'from');
        // var_dump($result);
        if ($result != '1') {
            return $result;
        }
    }
    if ($tradeDetail['lock_to']) {
        $result = TradeUnLockMoney($trade_id, 'to');
        // var_dump($result);
        if ($result != '1') {
            return $result;
        }
    }
    return 1;
}

/*
 * 域名交易锁定钱
 * lcok: from锁定卖家 to锁定买家
 */

function TradeLockMoney($money, $trade_id, $lock = 'from') {
    if (!isTransactionBegin())
        return 0;
    $tradeDetail = M("DomainTradeLog")->find($trade_id);
    if ($lock == 'to') {
        $result = LockUnLockMoney($tradeDetail['to_mid'], $money);
        // var_dump($result);
        if ($result != '1') {
            return false;
        }
        $tradeDetail['lock_to'] = $tradeDetail['lock_to'] + $money;
    } else {
        $result = LockUnLockMoney($tradeDetail['from_mid'], $money);
        if ($result != '1') {
            return false;
        }
        $tradeDetail['lock_from'] = $tradeDetail['lock_from'] + $money;
    }
    $result = M("DomainTradeLog")->data($tradeDetail)->save();
    if ($result) {
        return 1;
    }
    return false;
}

/*
 * 域名交易解锁钱
 * lcok: from锁定卖家 to锁定买家
 */

function TradeUnLockMoney($trade_id, $lock = 'from') {
    if (!isTransactionBegin())
        return 0;
    $tradeDetail = M("DomainTradeLog")->find($trade_id);
    if ($lock == 'to') {
        if (!$tradeDetail['lock_to']) {
            return true;
        }
        $result = LockUnLockMoney($tradeDetail['to_mid'], $tradeDetail['lock_to'], 'unlock');
        if ($result != '1') {
            return false;
        }
        $tradeDetail['lock_to'] = 0;
    } else {
        if (!$tradeDetail['lock_from']) {
            return true;
        }
        $result = LockUnLockMoney($tradeDetail['from_mid'], $tradeDetail['lock_from'], 'unlock');
        if ($result != '1') {
            return false;
        }
        $tradeDetail['lock_from'] = 0;
    }
    return M("DomainTradeLog")->data($tradeDetail)->save();
}

/*
 * 用户扣款操作
 * 返回 1成功,-1余额不足 ,-2 Money表写入失败 -3 消费表写入失败
 */

function UserDebit($money, $mid, $type, $memo) {
    if (!isTransactionBegin())
        return 0;
    $memberMoney = M("MembersMoney")->find($mid);
    if ($memberMoney['total_money'] < $money) {
        return -1;
    }
    $memberMoney['total_money'] = $memberMoney['total_money'] - $money;
    if ($memberMoney['recharge_money']) {
        if ($memberMoney['recharge_money'] > $money) {
            $memberMoney['recharge_money'] = $memberMoney['recharge_money'] - $money;
        } else {
            $memberMoney['trade_money'] = $memberMoney['trade_money'] - ($money - $memberMoney['recharge_money']);
            $memberMoney['recharge_money'] = 0;
        }
    } else {
        $memberMoney['trade_money'] = $memberMoney['trade_money'] - $money;
    }
    $result = M("MembersMoney")->data($memberMoney)->save();
    if (!$result) {
        return -2;
    }
    unset($result);
    $data['mid'] = $mid;
    $data['type'] = $type;
    $data['from'] = 'micang';
    $data['time'] = time();
    $data['money'] = $money;
    $data['memo'] = $memo;
    $result = M('MembersConsumeDetail')->data($data)->add();
    if ($result) {
        return 1;
    }
    return -3;
}

/*
 * 违约金平分
 */

function WeiYue($trade_id, $user = 'from') {
    if (!isTransactionBegin())
        return 0;
    $tradeDetail = M('DomainTradeLog')->find($trade_id);
    $result = M("MembersDomains")->where(array('domain' => $tradeDetail['domain']))->data(array('status' => '100'))->save();
    if (!$result) {
        return "域名解除锁定失败，请稍后重试";
    }
    if ($tradeDetail['type'] == '2') {
        $typeName = "拍卖";
        if ($tradeDetail['status'] == '0' && $tradeDetail['lock_from'] == 0) {
            // 拍卖结拍，卖家反悔，又没有冻结款,属于站内域名，冻结10%给平台跟买家分
            $weiyue = min(500000, max(5000, $tradeDetail['money'] / 10));
            $result = TradeLockMoney($weiyue, $trade_id, 'from');
            if (!$result) {
                return "结束拍卖需要支付" . Fen2Yuan($weiyue) . "元违约金，当前账户余额不足";
            }
        }
    } elseif ($tradeDetail['type'] == '3') {
        $typeName = "出价";
        if ($tradeDetail['status'] == '0') {
            // 出价状态，第一步，卖家拒绝报价不违约
            // 标记卖家拒绝报价，域名继续冻结
            $result = M("MembersDomains")->where(array('domain' => $tradeDetail['domain']))->data(array('status' => '104'))->save();
            if (!$result) {
                return "域名冻结失败";
            }
            $result = M("DomainTradeLog")->where(array('id' => $trade_id))->data(array('status' => '5'))->save();
            if ($result) {
                return 1;
            } else {
                return "拒绝报价失败，请重试";
            }
        }
    }
    if ($user == 'from') {
        $tradeDetail = M('DomainTradeLog')->find($trade_id);
        // 解除买家冻结的钱
        $result = TradeUnLockMoney($trade_id, 'to');
        if (!$result) {
            return "买家冻结款解锁失败";
        }
        // 标记卖家反悔
        $result = M("DomainTradeLog")->where(array('id' => $trade_id))->data(array('status' => '4', 'update_time' => time()))->save();
        if (!$result) {
            return "买家冻结款解锁失败";
        }
        if (!$tradeDetail['lock_from']) {
            // 没有锁定钱，不用扣违约金
            return 1;
        }
        // 扣除卖家违约金
        $result = DebitTradeLockMoney($trade_id, 'from');
        if (!$result) {
            return "扣除冻结款失败，请稍后重试";
        }
        $memo = "{$tradeDetail['domain']}{$typeName}域名交易卖家违约金";
        $result = PlatformIncome($tradeDetail['lock_from'] / 2, $tradeDetail['from_mid'], $tradeDetail['domain'], 'penalty', $memo);
        if (!$result) {
            return "系统出错，请稍后重试,error:001";
        }
        unset($result);
        $result = UserIncome($tradeDetail['lock_from'] / 2, $tradeDetail['to_mid'], 'penalty', $memo);
        if (!$result) {
            return "违约金支付给买家失败，请稍后重试";
        }
    } else {
        $tradeDetail = M('DomainTradeLog')->find($trade_id);
        // 解除卖家冻结的钱
        $result = TradeUnLockMoney($trade_id, 'from');
        if (!$result) {
            return "卖家冻结款解锁失败";
        }
        // 标记买家反悔
        $result = M("DomainTradeLog")->where(array('id' => $trade_id))->data(array('status' => '3', 'update_time' => time()))->save();
        if (!$result) {
            return '买家反悔交易标记失败';
        }
        if (!$tradeDetail['lock_to']) {
            // 没有锁定钱，不用扣违约金
            return 1;
        }
        // 扣除买家违约金
        $result = DebitTradeLockMoney($trade_id, 'to');
        if (!$result) {
            return "扣除冻结款失败，请稍后重试";
        }
        $memo = "{$tradeDetail['domain']}{$typeName}域名交易买家违约金";
        $result = PlatformIncome($tradeDetail['lock_to'] / 2, $tradeDetail['to_mid'], $tradeDetail['domain'], 'penalty', $memo);
        if (!$result) {
            return "平台结算失败，请稍后重试，error:001";
        }
        unset($result);
        $result = UserIncome($tradeDetail['lock_to'] / 2, $tradeDetail['from_mid'], 'penalty', $memo);
        if (!$result) {
            return "违约金支付给卖家失败，请稍后重试";
        }
    }
    return 1;
}

/*
 * 统计消费关注人数
 */

function CountNotice($sale_id) {
    return M("MembersDomainNotice")->where(array("sale_id" => $sale_id))->count();
}

/*
 * 扣除用户冻结钱
 */

function DebitTradeLockMoney($trade_id, $user = 'from') {
    if (!isTransactionBegin())
        return 0;
    $tradeDetail = M('DomainTradeLog')->find($trade_id);

    if (!$tradeDetail) {
        return "系统出错,error:001";
    }
    if ($user == 'from') {
        if (!$tradeDetail['lock_from']) {
            return 1;
        }
        $memberMoney = M("MembersMoney")->find($tradeDetail['from_mid']);
        if ($memberMoney['lock_money'] < $tradeDetail['lock_from']) {
            return '系统异常，请稍后重试';
        }
        $memberMoney['lock_money'] = $memberMoney['lock_money'] - $tradeDetail['lock_from'];
        $memberMoney['total_money'] = $memberMoney['total_money'] + $tradeDetail['lock_from'];
        $where['mid'] = $tradeDetail['from_mid'];
        $result = M("MembersMoney")->where($where)->data(array('total_money' => $memberMoney['total_money'], 'lock_money' => $memberMoney['lock_money']))->save();
        if (!$result) {
            return '扣除冻结款失败，请稍后重试';
        }
        unset($result);
        $memo = "{$tradeDetail['domain']}域名交易卖家违约金";
        $result = UserDebit($tradeDetail['lock_from'], $tradeDetail['from_mid'], 'penalty', $memo);
        if ($result != '1') {
            return '卖家违约金消费日志写入失败，请稍后重试';
        }
    }
    if ($user == 'to') {
        if (!$tradeDetail['lock_to']) {
            return 1;
        }
        $memberMoney = M("MembersMoney")->find($tradeDetail['to_mid']);
        if ($memberMoney['lock_money'] < $tradeDetail['lock_to']) {
            return '系统异常，请稍后重试';
        }
        $memberMoney['lock_money'] = $memberMoney['lock_money'] - $tradeDetail['lock_to'];
        $memberMoney['total_money'] = $memberMoney['total_money'] + $tradeDetail['lock_to'];
        $where['mid'] = $tradeDetail['to_mid'];
        $result = M("MembersMoney")->where($where)->data(array('total_money' => $memberMoney['total_money'], 'lock_money' => $memberMoney['lock_money']))->save();
        if (!$result) {
            return '扣除冻结款失败，请稍后重试';
        }
        unset($result);
        $memo = "{$tradeDetail['domain']}域名交易买家违约金";
        $result = UserDebit($tradeDetail['lock_to'], $tradeDetail['to_mid'], 'penalty', $memo);
        if ($result != '1') {
            return '买家违约金消费日志写入失败，请稍后重试';
        }
    }
    return 1;
}

/*
 * 消费日志写入
 */

function ConsumeLog($mid, $type, $from, $money, $memo) {
    if (!isTransactionBegin())
        return 0;
    $data['mid'] = $mid;
    $data['type'] = $type;
    $data['registrar'] = $from;
    $data['money'] = $money;
    $data['content'] = $memo;
    return M("MembersConsumeDetail")->data($data)->add();
}

/**
 * 检测是否开启了事务
 *
 * @return bool
 */
function isTransactionBegin() {
    $refClass = new \ReflectionClass(get_class(M()->db()));
    $refProperty = $refClass->getProperty('transTimes');
    $refProperty->setAccessible(true);
    $transTimes = $refProperty->getValue(M()->db());
    return $transTimes > 0 ? true : false;
}

/**
 * 发送微信验证码= 验证码确认通知
 *
 * @param type $openId
 *            用户的微信openid = memebers 表weixn 字段
 * @param type $code
 *            确认的验证码
 * @param type $nickName
 *            昵称
 * @param type $note
 *            操作描述
 * @return boolean
 */
function weixin_confirm_code($openId, $code, $uesrname, $note) {
    $weixinConfig = C('WEIXIN_CONFIG');
    $weixin = new \Common\Extend\WeiXin($weixinConfig['APP_ID'], $weixinConfig['APP_SECRET'], $weixinConfig['TOKEN'], $weixinConfig['ASE_KEY']);
    if (!$weixin->getAccessToken())
        return false;
    if (!$openId)
        return false;
    $templateId = C('WEIXIN_TEMPLATE_ID.confirm_code');
    $url = 'http://www.micang.com';
    $data['first'] = array('value' => '您操作的账号' . $uesrname, 'color' => '#173177');
    $data['keyword1'] = array('value' => $note, 'color' => '#173177');
    $data['keyword2'] = array('value' => $code, 'color' => '#ff0000');
    $data['remark'] = array('value' => "若本次交易非本人发起，请速修改您的密码或联系客服人员。\r\n[发送时间 " . date('H:i') . "] 【米仓网】");
    // 'color' => '#DEB887'

    $result = $weixin->sendTemplateMessage($templateId, $openId, $url, $data);
    if (!$result)
        return false;
    return true;
}

/**
 * 发送微信验证码 = 验证码下发通知
 *
 * @param type $openId            
 * @param type $note
 *            操作描述
 *            内容详细：
 *            {{first.DATA}}，手机动态验证码为{{number.DATA}}
 *            {{remark.DATA}}
 */
function weixin_send_code($openId, $uesrname, $note = '') {
    $weixinConfig = C('WEIXIN_CONFIG');
    $weixin = new \Common\Extend\WeiXin($weixinConfig['APP_ID'], $weixinConfig['APP_SECRET'], $weixinConfig['TOKEN'], $weixinConfig['ASE_KEY']);
    if (!$weixin->getAccessToken())
        return false;
    if (!$openId)
        return false;
    $templateId = C('WEIXIN_TEMPLATE_ID.send_code');
    $url = 'http://www.micang.com';
    $data['first'] = array('value' => '尊敬的' . $uesrname . '用户,' . $note, 'color' => '#173177');
    $code = rand(100000, 999999);
    $data['number'] = array('value' => $code, 'color' => '#ff0000');
    $data['remark'] = array('value' => "有效时间为10分钟。若本次交易非本人发起，请速修改您的密码或联系客服人员。\r\n[发送时间 " . date('H:i') . "] 【米仓网】");
    // 'color' => '#9999FF'

    $res = D('MobileCodes')->addSms($openId, $code, date('YmdHis'));
    if (!$res)
        return false;
    $result = $weixin->sendTemplateMessage($templateId, $openId, $url, $data);
    if (!$result)
        return false;
    return true;
}

/**
 * 判断是否启用微信验证码 ,如果是发送微信验证码
 *
 * @param type $id
 *            用户id
 * @param type $note
 *            操作备注
 *            return false 发送失败
 */
function sendWeixinCode($id, $note = '') {
    if (!$id)
        return false;
    $info = M('Members')->field('username,code_status,weixin')->where(array('id' => $id))->find();
    if (empty($info)) {
        return false;
    }
    if (!$info['weixin'])
        return false;
    $res = weixin_send_code($info['weixin'], $info['username'], $note);
    if (!$res)
        return false;
    return true;
}

function checkWeiXinCode($id, $code) {
    if (!$id || !$code)
        return array('status' => 500, 'message' => '参数验证码不能为空。');
    $info = M('Members')->field('code_status,seccode,weixin')->where(array('id' => $id))->find();
    $r = D('MobileCodes')->validCheckCode($info['weixin'], $code);
    if ($r['status'] == 200) {
        return array('status' => 200, 'message' => '验证成功。');
    } elseif ($r['status'] == 300) {
        return array('status' => 300, 'message' => '您的验证码已经过期，请重新发送。');
    } elseif ($r['status'] == 400) {
        return array('status' => 400, 'message' => '请输入正确的验证码。');
    }
    return array('status' => 500, 'message' => '验证成功失败，请稍后重试。');
}

/**
 * 安全码验证
 *
 * @param type $id
 *            用户id
 * @param type $code
 *            加密前
 */
function validCode($id, $code) {
    if (!$id || !$code)
        return array('status' => 500, 'message' => '参数验证码不能为空。');
    $info = M('Members')->field('code_status,seccode,weixin')->where(array('id' => $id))->find();
    if ($info['code_status'] == '2') {
        $r = D('MobileCodes')->validCheckCode($info['weixin'], $code);
        if ($r['status'] == 200) {
            return array('status' => 200, 'message' => '验证成功。');
        } elseif ($r['status'] == 300) {
            return array('status' => 300, 'message' => '您的验证码已经过期，请重新发送。');
        } elseif ($r['status'] == 400) {
            return array('status' => 400, 'message' => '请输入正确的验证码。');
        }
    } elseif ($info['code_status'] == '1') {
        if ($info['seccode'] != md5($code))
            return array('status' => 500, 'message' => '安全码不对。');
    }
    return array('status' => 200, 'message' => '验证成功。');
}

/**
 * 获取域名
 *
 * @param type $pinyin            
 * @return type
 */
function getDoMain($pinyin = '', $suffix = '/') {
    if ($pinyin == '') {
        return "http://" . I("server.HTTP_HOST") . $suffix;
    } elseif ($pinyin == 'www' || $pinyin == 'member' || $pinyin == 'jansen') {
        $subdomain = subDomain(I("server.HTTP_HOST"));
        return "http://" . str_replace($subdomain, $pinyin, I("server.HTTP_HOST")) . $suffix;
    }
    return "http://" . I("server.HTTP_HOST") . $suffix;
}

/**
 * 获取域名前缀
 *
 * @param type $host            
 * @return type
 */
function subDomain($host) {
    $r = explode('.', $host);
    return $r[0];
}

/**
 * 获取域名数
 */
function countMemberDomain($mid) {
    if ($mid) {
        return 0;
    }
    return M('MembersDomains')->where(array('mid' => intval($mid)))->count();
}

/**
 * 获取账号
 */
function getUserNameByMid($mid) {
    if (!$mid) {
        return '';
    }
    return M('Members')->where(array('id' => intval($mid)))->getField('username');
}

/**
 * 获取账号实名认证状态
 */
function getUserAuthStatusByMid($mid) {
    if (!$mid) {
        return '';
    }
    $status = M('Members')->where(array('id' => intval($mid)))->getField('auth_status');
    if ($status == '0') {
        return '未提交';
    } elseif ($status == '1') {
        return '通过';
    } elseif ($status == '2') {
        return '审核中';
    } elseif ($status == '3') {
        return '未通过';
    }
    return '未提交';
}

/**
 * 查是否有出厂价格
 *
 * @param type $name            
 */
function getRegistrarPrice($name, $register) {
    if (!$name || !$register) {
        return false;
    }
    $register = ucfirst($register);
    $priceModel = D('DomainSuffixPrice' . $register);
    $name = $priceModel->where(array('name' => $name))->getField('name');
    $priceModel = D('MemberLevelPriceMap');
    $suffix = $priceModel->where(array('suffix' => $name))->getField('suffix');
    if ($name && $suffix) {
        return true;
    }
    return false;
}

/**
 * 查是否有会员价格
 *
 * @param type $suffix            
 */
function getMemberPriceMap($suffix) {
    if (!$suffix) {
        return false;
    }
    $priceModel = D('MemberLevelPriceMap');
    $suffix = $priceModel->where(array('suffix' => $suffix))->getField('suffix');
    return $suffix;
}

/**
 * 字符截取
 *
 * @param type $str            
 * @param type $start            
 * @param type $length            
 * @param type $charset            
 * @param type $suffix            
 * @return type
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = false) {
    $str = outspace($str);
    if (function_exists("mb_substr")) {
        if ($suffix)
            return mb_substr($str, $start, $length, $charset) . "...";
        else
            return mb_substr($str, $start, $length, $charset);
    }elseif (function_exists('iconv_substr')) {
        if ($suffix)
            return iconv_substr($str, $start, $length, $charset) . "...";
        else
            return iconv_substr($str, $start, $length, $charset);
    }
    $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("", array_slice($match[0], $start, $length));
    if ($suffix)
        return $slice . "…";
    return $slice;
}

/**
 * 去除空格
 *
 * @param type $con            
 * @return type
 */
function outspace($con) {
    $temp = trim(strip_tags(htmlspecialchars_decode(trim($con))));
    return str_replace(array(" ", "　", "\t", "\n", "\r", "&nbsp;", ">", "\r\n", "&gt;"), "", trim($temp));
}

/**
 * 获取最新域名
 */
function getNewMemberDomain($mid) {
    if ($mid) {
        return '-';
    }
    return M('MembersDomains')->where(array('mid' => intval($mid)))->order('id desc')->getField('domain');
}

/**
 * 获取委托状态
 * @param type $id
 */
function getPurchaseParam($id = null) {
    $param = array(
        '0' => '待经纪人审核',
        '1' => '洽谈中',
        '2' => '审核未通过',
        '3' => '待买家确认',
        '4' => '待卖家确认',
        '5' => '完成委托',
        '6' => '委托失败'
    );
    return is_null($id) ? $param : $param[$id];
}

/**
 * 获取中介域名状态
 * @param type $id
 */
function getAgencyParam($id = null) {
    $param = array(
        '0' => '等待中介双方确认',
        '1' => '待买家确认',
        '2' => '待卖家确认',
        '3' => '完成中介',
        '4' => '等待付款',
        '10'=>'	中介取消关闭'
    );
    return is_null($id) ? $param : $param[$id];
}

/**
 * 获取账号
 */
function getUserHeadByMid($mid) {
    if (!$mid) {
        return '/Public/Home/images/pic_user.jpg';
    }
    $head = M('MembersProfile')->where(array('mid' => intval($mid)))->getField('head_url');
    return $head ? $head : '/Public/Home/images/pic_user.jpg';
}

/**
 * 获取竞价次数
 * @param type $mid
 * @param type $pid
 */
function countTradeNum($mid, $pid) {
    if (!$mid || !$pid) {
        return '0';
    }
    return M("DomainAuctionLog")->where(array("pid" => $pid, 'uid' => $mid))->count();
}

/**
 * 获取总后台操作日志
 * @param type $id
 */
function getAdminLogType($id = null) {
    $param = array(
        '0' => '实名认证',
        '1' => '委托',
        '2' => '中介',
        '3' => '提现',
        '4' => '域名后缀',
        '5' => '模板',
        '6' => '域名价格',
        '7' => '友情链接',
        '8' => '图片',
        '9' => '资讯',
        '10' => '管理员账号',
    );
    return is_null($id) ? $param : $param[$id];
}

/**
 * 会员默认设置验证提醒
 */
function defaultMembersSet($mid) {
    $result = array();
    //安全设置模块  0 购买结算 1发起PUSH2接收PUSH3一口价4限时竞价5域名转出6域名解析7DNS设置8修改登录密码	9修改操作设置10修改账户资料
    $data['mid'] = $mid;
    $data['type'] = '0'; //设置类型0  安全设置  1  常用提醒  2  默认设置
    for ($index = 0; $index < 11; $index++) {
        $data['mode'] = $index;
        switch ($index) {
            case '0':
                $data['code'] = '0';
                break;
            case '10':
                $data['code'] = '0';
                break;
            default:
                $data['code'] = '1';
                break;
        }
        $data['email'] = '0';
        $data['mobile'] = '0';
        $result[] = $data;
        M('MembersSet')->add($data);
    }
    // 常用提醒模块 0 异常登录1一口价出价（买家）2一口价出价（卖家）3拍卖结拍（买家）	4拍卖结拍（卖家）5发起PUSH6接收PUSH7域名转出8密码修改9安全码修改10活动消息通知
    $data['type'] = '1';
    for ($index = 0; $index < 11; $index++) {
        $data['mode'] = $index;
        $data['code'] = '0';
        $data['email'] = '1';
        $data['mobile'] = '1';
        $result[] = $data;
        M('MembersSet')->add($data);
    }
    // 默认设置模块 0  DNS设置 1 注册模板 2 过户模板
    $data['type'] = '2';
    for ($index = 0; $index < 3; $index++) {
        $data['mode'] = $index;
        $result[] = $data;
        M('MembersSet')->add($data);
    }
    return $result;
}

/**
 * 生成随机数
 * @param type $length
 * @return string
 */
function randomkeys($length) {
    $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
    for ($i = 0; $i < $length; $i++) {
        $key .= $pattern{mt_rand(0, 35)}; //生成php随机数
    }
    return $key;
}