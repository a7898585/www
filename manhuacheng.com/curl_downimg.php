<?php
	$image_urls = $urls = $row = array();
	$id = $str = $ext = $filename = $result = $server_path = $path = "";
	/*连接数据库*/
	$con = mysql_connect("127.0.0.1","db_manhuacheng","fyh123");
	//$con = mysql_connect("203.110.169.80","zhangmingqun","!@#$%");
	if (!$con){
	  die('数据库连接失败: ' . mysql_error());
	}
	mysql_select_db("db_manhuacheng", $con);
	$result = mysql_query("SELECT id,CartoonImage FROM tt_CartoonDetail_data Where is_down='0' AND is_new='1' AND flag = '0' limit 1");
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$id = $row['id'];
	mysql_query("UPDATE tt_CartoonDetail_data SET flag = '1' WHERE id = '".$id."' AND flag = '0'");
	if(mysql_affected_rows() == 0){
		mysql_close($con);
		continue;
	}else{
		mysql_close($con);
	}
	$server_path = '/opt/webroot/manhuacheng.com';
	$path = 'uploadfile/'.date("Y",time()).'/'.date("md",time());
	set_time_limit (0);
	/*Curl下载图片到本地*/
	if(empty($id)){
		sleep(60);
		continue;
	}else{
		if(!empty($row['CartoonImage'])){
			$str = $row['CartoonImage'];
			eval("\$urls = ".$str.'; ');
			$urls = string2array($str);
			if(!file_exists($server_path."/".$path."/m".$id)){
				mkdir($server_path."/".$path."/m".$id,0755,1);
				exec("chown www:www -R ".$server_path."/".$path."/m".$id);
				exec("chown www:www -R ".$server_path."/".$path);
				chmod($server_path."/".$path."/m".$id,0755);
			}
			$i = 1;
			foreach($urls as $key => $url){
				$img_url = $url['url'];
				$curl = curl_init($img_url);
				$ext=substr(strrchr($img_url,"."),1); 
				$filename = date("YmdHis",time()).$i.".".$ext;
				curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
				$imageData = curl_exec($curl);
				curl_close($curl);
				$tp = @fopen($server_path."/".$path."/m".$id."/".$filename, "a");
				fwrite($tp, $imageData);
				fclose($tp);
				chmod($server_path."/".$path."/m".$id."/".$filename,0755);
				$image_urls[$key]['url'] = "/".$path."/m".$id."/".$filename;
				$image_urls[$key]['alt'] = $url['alt'];
				$i++;
				sleep(1);
			}
		}else{
			echo "下载失败,无需下载的图片数据";
		}
		if(!empty($image_urls) && !empty($id)){
			$image_urls = array2string($image_urls);
			//$con = mysql_connect("203.110.169.80","zhangmingqun","!@#$%");
			$con = mysql_connect("127.0.0.1","db_manhuacheng","fyh123");
			mysql_select_db("db_manhuacheng", $con);
			mysql_query("UPDATE tt_CartoonDetail_data SET CartoonImage = '".$image_urls."' , is_down = '1',flag = '0' WHERE id = ".$id);
			if(mysql_affected_rows() == 0){
				echo "更新到数据库失败,请重试";
			}else{
				mysql_query("UPDATE tt_CartoonDetail AS cd ,tt_Cartoon AS c SET cd.manhuaid = c.id WHERE cd.web_url=c.web_url AND cd.web_url != '' AND cd.id=".$id);
				echo "图片下载并更新数据成功";
			}
			mysql_close($con);
		}else{
			echo "无需更新的数据";	
		}
	/*-------end------------*/
	}


function string2array($data) {
	if($data == '') return array();
	eval("\$array = $data;");
	return $array;
}
function array2string($data, $isformdata = 1) {
	if($data == '') return '';
	if($isformdata) $data = new_stripslashes($data);
	return addslashes(var_export($data, TRUE));
}
function new_stripslashes($string) {
	if(!is_array($string)) return stripslashes($string);
	foreach($string as $key => $val) $string[$key] = new_stripslashes($val);
	return $string;
}
?>
