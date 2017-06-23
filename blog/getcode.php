<?php

define('BBSTHREAD', 'http://3g.blog.cnfol.com');
include('./phpqrcode/phpqrcode.php');

// 二维码数据

$data = $_GET['url'] ? $_GET['url'] : BBSTHREAD;



// 纠错级别：L、M、Q、H
$errorCorrectionLevel = 'L';
// 点的大小：1到10
$matrixPointSize = 5;
// 生成的文件名
$filename = $errorCorrectionLevel . '|' . $matrixPointSize . '.png';
// QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);

QRcode::png($data, false, '', $matrixPointSize);
