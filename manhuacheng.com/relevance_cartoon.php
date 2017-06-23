<?php
while(true){
	set_time_limit (0);
	$row = array();
	$id = "";
	/*连接数据库*/
	$con = mysql_connect("127.0.0.1","db_manhuacheng","fyh123");
	//$con = mysql_connect("203.110.169.59","zhangmingqun","mq123");
	//mysql_set_charset('utf8', $conn);
	//mysql_query("set names uft8");
	if (!$con){
	  die('数据库连接失败: ' . mysql_error());
	}
	mysql_select_db("db_manhuacheng", $con);
	$result = mysql_query("SELECT id FROM tt_dongmanDetail Where `CartoonID` = '0' limit 1");
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$id = $row['id'];
	if(empty($id)){
		mysql_close($con);
		//echo "查无需关联数据";
		sleep(3600);
		continue;
	}else{
		mysql_query("UPDATE tt_dongmanDetail AS dd ,tt_dongman AS d SET dd.CartoonID = d.id WHERE dd.web_url=d.web_url AND dd.web_url != '' AND dd.id=$id ");
		mysql_close($con);
		//echo "关联成功";
	}

}
?> 