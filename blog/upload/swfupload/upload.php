<?php

$uploaddir = 'upload/';
$filename = date("Ymdhis") . rand(100, 999);
$uploadfile = $uploaddir . $filename . substr($_FILES['Filedata']["name"], strrpos($_FILES['Filedata']["name"], "."));
$temploadfile = $_FILES['Filedata']['tmp_name'];
move_uploaded_file($temploadfile, $uploadfile);

//返回数据  在页面上js做处理
$filedata = array(
    'result' => 'true',
    'name' => $_FILES['Filedata']["name"],
    'filepath' => 'http:/new.blog.cnfol.com/upload/swfupload/'.$uploadfile,
);
echo json_encode($filedata);
exit;