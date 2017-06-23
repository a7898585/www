<?php
require 'config.php';


$log_filename2 = '/home/www/html/logs/ftpupload_'.date('Ymd').'.log';

$source_file = $argv[1];

$tmpstr1 = dirname($source_file);
$tmpstr2 = basename($source_file);
$tmparr  = explode('/', $tmpstr1);

for($i=1; $i<6; $i++)
{
    array_shift($tmparr);
}

// set del basic connection
$ftp_server     = $attachConfig['ftp_server'];
$ftp_port       = $attachConfig['ftp_port'];
$ftp_user_name  = $attachConfig['ftp_user'];
$ftp_user_pass  = $attachConfig['ftp_pass'];
$conn_id        = @ftp_connect($ftp_server, $ftp_port);

// login with username and password
$login_result   = @ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

// check connection
if ((!$conn_id) || (!$login_result))
{
    error_log(date('Ymd H:i:s',time())." FTP connection has failed!"."\r\n",3,$log_filename2);
    error_log(date('Ymd H:i:s',time())." Attempted to connect to ".$ftp_server." for user ".$ftp_user_name."\r\n",3,$log_filename2);
    exit;
}
else
{
    error_log("Connected to ".$ftp_server.", for user ".$ftp_user_name."\r\n",3,$log_filename2);
}

$destination_file = $attachConfig['ftp_path'].'/'.join('/', $tmparr).'/'.$tmpstr2;

if (@ftp_delete($conn_id, $destination_file))
{
    error_log(date('Ymd H:i:s',time())." Deleted ".$destination_file."\r\n",3,$log_filename2);
}
else
{
    error_log(date('Ymd H:i:s',time())." FTP delete has failed!"."\r\n",3,$log_filename2);
}

// close the FTP stream
ftp_close($conn_id);

@unlink($source_file);
?>