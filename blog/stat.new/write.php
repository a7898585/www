<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <?php
        define('ROOT', dirname(__FILE__));
        require_once ROOT . '/inc/config.inc.php';
        require_once ROOT . '/cls/CMemcache.php';
        require_once ROOT . '/cls/CSocket.php';
        require_once ROOT . '/func/socket.func.php';
        require_once ROOT . '/func/commen.func.php';

        $socket = new CSocket();
        $type = 'B517';
        $rs = $socket->senddata($type, $data);
        $rs['type'] = $type;
        $rs['args'] = $data;
        $rs = checkrs($rs);

        $str = '';
        if ($rs['RetRecords'] > 0 && count($rs['Record']) > 0) {
            foreach ($rs['Record'] as $value) {
                $value['Name'] = iconv('UTF-8', 'GB2312', $value['Name']);
                $str.=$value['Name'] . ",";
            }
            error_log($str, 3, '/home/www/html/newestblog/f/dededic.txt');
        }
        //error_log("ʲ 4 i \r\n"."ִ 4 i \r\n"." 4 i \r\n", 3 ,ROOT.'/dededic.txt');
        ?>

    </body>
</html>