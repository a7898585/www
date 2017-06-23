<?php
    define('ROOT',	dirname(__FILE__));
	require_once ROOT.'/inc/config_hot.inc.php';
	//require_once ROOT.'/cls/CMemcache.php';
	require_once ROOT.'/cls/CSocket.php';
	require_once ROOT.'/func/socket.func.php';
	require_once ROOT.'/func/commen.func.php';
	header('Content-Type: text/html; charset=utf-8');
	
    //热门博客，热门文章统计读取
	$file='/home/www/html/logs/hotBlogArticle_'.date("Y-m-d H").'.log';
	
	$socket = new CSocket();
	
	if(file_exists($file))
	{
		$file=file_get_contents($file);
		
		
		$file=trim($file,'$$--**>>');
		$hotBlogArticle=explode('$$--**>>',$file);
		if(!empty($hotBlogArticle))
		{
			
			
			
			$type	= 'A001';

			
			$hotBlogArticle=array_count_values($hotBlogArticle);
			//print_r($hotBlogArticle);
		
			
			foreach($hotBlogArticle as $key=>$value)
			{
				
				$article=explode(',',$key);
				
				$Cnt=($article['3']=='1')?'UserCnt':'GuestCnt';
				$arry_a=array('DomainName','Timestp','ArticleID',$Cnt);
			
				
				if(!empty($key))
				{
					array_push($article,$value);
					unset($article['3']);
					$array_ab=array_combine($arry_a,$article);
					print_r($array_ab);
					
					$rs = $socket->senddata($type, $array_ab);
					//$rs = checkrs($rs);
					//$rs = (isset($rs['Record']))? $rs['Record']:array(); 
					echo($rs['Status']['Code'].'<br />');
					
				}
			}
			
		}
		else
		{
			echo('no hotBlogArticleFile');
		}
	}
	else
	{
		echo('no hotBlogArticleFile');
	}
	
	unset($file);
	
	
	
	 //用户访问记录存储
	$file='/home/www/html/logs/hotBlogUser_'.date("Y-m-d H").'.log';
	
	if(file_exists($file))
	{
		$file=file_get_contents($file);
		
		
		$file=trim($file,'$$--**>>');
		$hotBlogArticle=explode('$$--**>>',$file);
		if(!empty($hotBlogArticle))
		{
			
			$type	= 'A005';

			//print_r($hotBlogArticle);
			
			foreach($hotBlogArticle as $value)
			{
				$hotBlogArray=explode(',',$value);
			    $url=$config['newblog'].$hotBlogArray['1'].'/article/'.$hotBlogArray['2'].'-'.$hotBlogArray['3'].'.html';
			    array_splice($hotBlogArray,1,3,$url);
			    array_splice($hotBlogArray,2,1,date("Y-m-d H-i-s",$hotBlogArray['2']));
				$arry_a=array('UserID','Url','AccessTime','HoldSecs','UserIP');
				
				$array_ab=array_combine($arry_a,$hotBlogArray);
				//print_r($array_ab);
				
				$rs = $socket->senddata($type, $array_ab);
				//$rs = checkrs($rs);
				//$rs = (isset($rs['Record']))? $rs['Record']:array(); 
				echo($rs['Status']['Code'].'<br />');
				
				
			}
			
			
		}
		else
		{
			echo('no hotBlogUserFile');
		}
	}
	else
	{
		echo('no hotBlogUserFile');
	}
	
	
	
	
	
	//热门标签统计读取
	$file='/home/www/html/logs/hotBlogTag_'.date("Y-m-d H").'.log';
	
	if(file_exists($file))
	{
		$file=file_get_contents($file);
		
		
		$file=trim($file,'$$--**>>');
		$hotBlogArticle=explode('$$--**>>',$file);
		
		if(!empty($hotBlogArticle))
		{
			
			$type	= 'A010';
			//print_r($hotBlogArticle);
		
			$hotBlogNew=array();
			foreach($hotBlogArticle as $key=>$value)
			{
				//echo(count($hotBlogArticle));
				
				//echo($value.'......');
				
				//$value=explode(',,**,,',$value);
				if($hotBlogNew[$value])
				{
						
					continue;
				}
				
				if(count($hotBlogArticle)==0)
				{
					continue;
				}
				
				$i=0;
				foreach($hotBlogArticle as $key_2=>$value_2)
				{
					
					if($value==$value_2)
					{
						unset($hotBlogArticle[$key_2]);
						$i++;
						
					}
					
				}
				
				$hotBlogNew[$value]=$i;
			}
			
			//print_r($hotBlogNew);
			
			foreach($hotBlogNew as $key=>$value)
			{
				$hotBlogArray=array($key,$value);
				
				$arry_a=array('KeyWord','Cnt');
				$array_ab=array_combine($arry_a,$hotBlogArray);
				$rs = $socket->senddata($type, $array_ab);
				echo($rs['Status']['Code'].'<br />');
				
			}
		}
		else
		{
			echo('no hotBlogTagFile');
		}
	}
	else
	{
		echo('no hotBlogTagFile');
	}
?>
