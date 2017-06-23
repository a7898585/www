<?php
//mysql类
define('ROOT', dirname(__FILE__));
include ROOT.'/config/config_bao10jie.inc.php';
include('Mysql.class.utf8.php');

//配置
$dbhost = $_config['hostname'];
$dbuser = $_config['username'];
$dbpw = $_config['password'];
$dbname = $_config['database'];
unset($_config);


$logFile = ROOT.'/bao10jie_cache.lock';

$db = new MySql;
$db->Connect($dbhost, $dbuser, $dbpw, $dbname);

$bao10jie = new Bao10jie();
$task = 'Purify';	//用户自定义，Purify/Feedback/getNotify/getIndexResult


//五分钟跑一次，与crontab脚本一致
$lasttime = time()-300;


//判断是否被重复运行
if(file_exists($logFile))
{
	$lastlock = file_get_contents($logFile);
	if ($lasttime < ($lastlock-60) ) {
		echo date('Y-m-d H:i',time())."重复运行了脚本.\r\n";
		$db->Close();
		exit;
	}
}
file_put_contents($logFile, time());

$t = 0;
$t_displayorder = array('0'=>'-1','1'=>'-2','2'=>'-3');
$p_invisible  = array('0'=>'0','1'=>'-1','2'=>'-2');
$last_dateline = time() - 400;
$end = time() - 60;
$log_file = dirname(__FILE__).'/bao10jie/Log/'.date('Y-m-d', time()).'.log';

$tres = $db->get_values('pre_forum_thread','tid,posttableid',"lastpost>='$last_dateline' AND isgroup = 0");
$to_post_arr = array();

foreach ($tres['posttableid'] as $key=>$val) {
	$to_post_arr[$val][] = $tres['tid'][$key];
}
unset($tres);
$cell = 'fid,pid,tid,authorid,author,subject,message,dateline,useip,first';
$tbname = 'pre_forum_post';
foreach ($to_post_arr as $k=>$v) {
	$posttable = $k == 0 ? '' : "_$k";
	$tids = join(',', $v);
	$where = "dateline>='$last_dateline' AND dateline<='$end' AND tid in ($tids)";
	$pres = array();
	$pres = $db->get_values($tbname.$posttable,$cell,$where);
	$data = swap($pres);
	unset($pres);
	foreach ($data as $d) {
		$b10j = $db->get_values('pre_b10j_post','textid',"textid='{$d['pid']}'");
		if (empty($b10j)) {
			$t++;

			$param = array();
			$param['isBatch'] = 0;
			$param['appType'] = "bbs";
			$param['textId'] = $d['pid'];
			$param['threadId'] = $d['tid'];
			$param['class'] = $d['fid'];
			$param['ip'] = $d['useip'];
			$param['userId'] = $d['authorid'];
			$param['author'] = $d['author'];
			$param['title']=$d['subject'];
			$param['text'] =$d['message'];
			$param['url'] ="http://bbs.cnfol.com/thread-{$d['tid']}-1-1.html";
			$param['pubDate'] = date('Y-m-d H:i:s', $d['dateline']);
			$param['authorEx'] = "";
			$param['contentEx'] = "";
			$param['structureEx'] = "";
			$param['rules'] = "";
			$test = $bao10jie->run($param, $task);
			//print_r($test);
			if ($test->getMarkResult()) {
				$sig = $test->getMarkResult()->flag;
				$invisible = $displayorder = 0;
				$invisible = $p_invisible[$sig];
				$db->Query("INSERT INTO `pre_b10j_post` (`textid`,`idtype`,`cidtype`,`classid`,`tid`,`userid`,`username`,`sig`,`sendtime`,`edittime`,`updatetime`,`status`) values ('{$d['pid']}','7','','{$d['fid']}','{$d['tid']}','{$d['authorid']}','{$d['author']}','$sig','{$d['dateline']}','0','{$d['dateline']}', '$invisible')");
				$db->Query("UPDATE pre_forum_post{$posttable} SET invisible = '{$invisible}' WHERE pid='{$d['pid']}'");
				if ($d['first']) {
					$displayorder = $t_displayorder[$sig];
					$db->Query("UPDATE pre_forum_thread SET displayorder = '{$displayorder}' WHERE tid='{$d['tid']}'");
				}

				echo "tid-{$d['tid']},pid-{$d['pid']}\n";

				unset($sql, $sig, $invisible, $displayorder);

			}
			else {
				error_log("pid: ".$d['pid']." bao10jie faild ".date('Y-m-d H:i:s', time())."\n",3, $log_file);
			}
			unset($param, $test);

		}
	}
	unset($posttable,$tids, $where, $data);
}


error_log(count($t)." end ".date('Y-m-d H:i:s', time())."\n",3, $log_file);

$db->Close();
/*
------------------------------
数组key对换
------------------------------
*/
function swap($str)
{
	if(is_array($str))
	{
		foreach($str as $key=>$value)
		{
			foreach($value as $k=>$v)
			{
				$result[$k][$key] = $v;
			}
		}
		return $result;
	}
	else return $array;
}
