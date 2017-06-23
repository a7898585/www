<?php
/**
 * 统一的header头信息输出
 * @author Jansen<6206574@qq.com>
 * @copyright 258.com
 */
namespace Common\Behavior;
use Think\Behavior;
final class CopyrightBehavior extends Behavior {
    public function run(&$params) {
        header('X-Copyright:2014-'.date('Y').' 258 Group Co.LTD');
        header('X-Author-Name:Jansen');
        header('X-Author-Email:6206574@qq.com');
        header('X-Author-QQ:6206574');
        header('X-Author-Website:http://www.iweiwen.net');
        header('X-Powered-By:IWEIWEN.NET');
    }
}

?>