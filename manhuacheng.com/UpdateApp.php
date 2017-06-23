<?php
define(MH_APP_PATH,'http://www.manhuacheng.com/UpdateFile/');
if($_GET['ParentID']){
	if($_GET['App'])header("location:".MH_APP_PATH.$_GET['ParentID'].'/'.$_GET['App']);
	else {header("Location:".MH_APP_PATH.'1/ManhuaCheng.exe');exit;}
}else {header("Location:".MH_APP_PATH.'1/ManhuaCheng.exe');exit;}
?>