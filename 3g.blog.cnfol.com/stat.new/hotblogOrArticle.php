<?php
    define('ROOT',	dirname(__FILE__));
	require_once ROOT.'/inc/config_hot.inc.php';
	//require_once ROOT.'/cls/CMemcache.php';
	require_once ROOT.'/cls/CSocket.php';
	require_once ROOT.'/func/socket.func.php';
	require_once ROOT.'/func/commen.func.php';
	header('Content-Type: text/html; charset=utf-8');
	
	$socket = new CSocket();
	$base="http://blog.cnfol.com/";
	$html='.html';
	
    $type	= 'A002';
    
    $articlelinetype=empty($_POST['articlelinetype'])?0:$_POST['articlelinetype'];
    $articleline=empty($_POST['articleline'])?0:$_POST['articleline'];
    $articlecnt=empty($_POST['articlecnt'])?10:$_POST['articlecnt'];
    
    echo('<form method="post">');
    echo('排行类型:<input type="text" name="articlelinetype" value="'.$articlelinetype.'" />(0:博客排行, 1:文章排行)<br />');
    echo('排行周期:<input type="text" name="articleline" value="'.$articleline.'" />(0:日排行, 1:周排行, 2:月排行)<br />');
    echo('显示条数:<input type="text" name="articlecnt" value="'.$articlecnt.'" />(默认取10笔)<br />');
    echo('<input type="submit" value="提交" />');
    echo('</form>');
    
    
    $data['Type']=$articlelinetype;//0:博客排行, 1:文章排行   默认0
    $data['Cycle']=$articleline;//0:日排行, 1:周排行, 2:月排行 默认0
    $data['QryCnt']=$articlecnt;//默认取10笔，逆序排列
    $rs = $socket->senddata($type, $data);
    
    $string='';
    echo('博客文章访问统计表');
    echo('<table><tr><td>排序</td><td>页面地址</td><td>PV</td></tr>');
    
    if($rs['Status']['TtlRecords']>0)
    {
    	if($rs['Status']['TtlRecords']==1)
    	{
    		
    		$rs['Records']['Record']=array('0'=>$rs['Records']['Record']);
    	}
    	
    	foreach($rs['Records']['Record'] as $value)
    	{
    		$articlehtml='';
    		if($articlelinetype!=0)
    		{
    			$articlehtml='/'.$value['Timestp'].'-'.$value['ArticleID'].$html;
    		}
    		$string.='<tr><td>'.$value['Order'].'</td><td>'.$base.$value['DomainName'].$articlehtml.'</td><td>'.$value['PV'].'</td></tr>';
    	
    	}
    }
    if($string=='')
    {
    	$string='暂无数据';
    }
    echo($string.'</table><br />');
    
    echo('-------------------');
    $type	= 'A006';
    
    $username=$_POST['username'];
    $userviewcnt=empty($_POST['userviewcnt'])?10:$_POST['userviewcnt'];
    echo('<form method="post">');
    echo('用户ID:<input type="text" name="username" value="'.$username.'" /><br />');
    echo('显示条数:<input type="text" name="userviewcnt" value="'.$userviewcnt.'" />');
    echo('<input type="submit" value="提交" />');
    echo('</form>');
    
    
    $param = array('UserName'=>$username);
	$user_info = $socket->senddata('U005', $param);
    $UserID=$user_info['Records']['Record']['UserID'];
	//echo($UserID);
	
    
    $data['UserID']=$username;//用户ID
    $data['QryCnt']=$userviewcnt;//选填，[默认取50笔，逆序排列]
    $rs = $socket->senddata($type, $data);
    //print_r($rs['TtlRecords']);
    
    $string='';
    $i=1;
    echo('<br />用户访问详情统计表');
    echo('<table><tr><td>序号</td><td style="width:100px;">用户id</td><td style="width:550px;">页面地址</td><td style="width:180px;">访问时间</td><td style="width:100px;">停留时间</td><td>访问地IP</td></tr>');
    if($rs['Status']['TtlRecords']>0)
    {
    	if($rs['Status']['TtlRecords']==1)
    	{
    		
    		$rs['Records']['Record']=array('0'=>$rs['Records']['Record']);
    	}
    	
   		foreach($rs['Records']['Record'] as $value)
    	{
    		$string.='<tr><td>'.$i.'</td><td>'.$data['UserID'].'</td><td>'.$value['Url'].'</td><td>'.$value['AccessTime'].'</td><td>'.$value['HoldSecs'].'</td><td>'.$value['UserIP'].'</td></tr>';
    		$i++;
    	}
    }
    if($string=='')
    {
    	$string='暂无数据';
    }
    echo($string.'</table><br />');
    
    echo('-------------------');
    $type	= 'A011';
    
    $keywordstime=empty($_POST['keywordstime'])?0:$_POST['keywordstime'];
    $keywordviewcnt=empty($_POST['keywordviewcnt'])?10:$_POST['keywordviewcnt'];
    
    echo('<form method="post">');
    echo('排行周期:<input type="text" name="keywordstime" value="'.$keywordstime.'" />0:日排行, 1:周排行, 2:月排行<br />');
    echo('显示条数:<input type="text" name="keywordviewcnt" value="'.$keywordviewcnt.'" />');
    echo('<input type="submit" value="提交" />');
    echo('</form>');
    
    
    $data['Cycle']=$keywordstime;//必填, [0:日排行, 1:周排行, 2:月排行默认0] 
    $data['QryCnt']=$keywordviewcnt;//选填，[默认取10笔，逆序排列]
    $rs = $socket->senddata($type, $data);
    //print_r($rs['Records']['Record']);
    
    $string='';
    echo('<br />关键字统计表');
    echo('<table><tr><td>序号</td><td style="width:270px;">关键词</td><td style="width:550px;">统计次数</td></tr>');
    if($rs['Status']['TtlRecords']>0)
    {
    	if($rs['Status']['TtlRecords']==1)
    	{
    		
    		$rs['Records']['Record']=array('0'=>$rs['Records']['Record']);
    	}
    	
   		foreach($rs['Records']['Record'] as $value)
    	{
    		$string.='<tr><td>'.$value['OrderNum'].'</td><td>'.$value['KeyWord'].'</td><td>'.$value['Cnt'].'</td></tr>';
    	}
    }
    if($string=='')
    {
    	$string='暂无数据';
    }
    echo($string.'</table><br />');
?>
