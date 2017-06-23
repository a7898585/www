<?php
$conn = fsockopen('127.0.0.1',8001);
if (!$conn) {
   die("Con error");
}
fwrite($conn,'reload_nginx');
while (!feof($conn))
   $buf .= fgets($conn,1024);
fclose($conn);
var_dump($buf);
?>
