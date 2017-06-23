<?php
require 'config.php';

//ԭ�ļ���Ŀ���ַ
$desc_file = $argv[1];
$source_file = $argv[2];
if(!file_exists($source_file))
{
    die('file not exists!');
}

$log_filename = '/home/www/html/logs/upload_'.date('Ymd').'.log';

error_log(date('Y-m-d H:i:s').' upload log!!!'."\r\n\t".$source_file.'=>'.$desc_file."\r\n", 3, $log_filename);

$tmpstr1 = dirname($desc_file);
$tmpstr2 = basename($desc_file);
$tmparr  = explode('/', $tmpstr1);

$ftp_server     = '172.20.1.195';
$ftp_port       = '21';
$ftp_user_name  = 'fol_blogimg';
$ftp_user_pass  = 'n+MkiLn_5E5=';

$conn_id        = @ftp_connect($ftp_server, $ftp_port);

$login_result   = @ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

if ((!$conn_id) || (!$login_result))
{
    error_log("\tconnect failed \r\n", 3, $log_filename);
    exit;
}
else
{
    error_log("\tconnect success\r\n", 3, $log_filename);
}

// mkdir
$str  = '';
foreach($tmparr as $key=>$val)
{
    if(trim($val) != '')
    {
        $str .= '/'.$val;
        $t = @ftp_mkdir($conn_id, $str);
		//error_log($str.'||'.$t.PHP_EOL,3, $log_filename);
    }
}

$destination_file = $str.'/'.$tmpstr2;
$upload = @ftp_put($conn_id, $destination_file, $source_file, FTP_BINARY);

/* 
����ͬ���ű����ӳ٣������ͨ�û����ϴ����ܵ�һʱ�俴��ͼƬ�����ﲻ��ͬ����������������ͼƬ������ͬһ̨������
Ŀ¼��/html/cnfol/blog.img.cnfol.com/upload
*/
if($upload)
{
	//$s = base64_encode('/'.$destination_file); 
    //$k = substr(md5($attachConfig['sync_caches_key']),5,20);
	//$url = $attachConfig['sync_upload_url']."?s=".$s."&k=".$k;
    //$handle = @file_get_contents($url,"r");
	//error_log("\t".$url." | $handle",3,$log_filename);
    error_log("\tUploaded ".$source_file." to ".$ftp_server." as ".$destination_file."\r\n\r\n",3,$log_filename);
}
else
{
    error_log("\tFTP upload has failed!"."\r\n\r\n", 3, $log_filename);
}

ftp_close($conn_id);
?>
