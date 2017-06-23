<?php 
/*
 * ==========================
 * Author : modify by Rlby
 * Date   : 2007-9-21
 * Role   : RSS文件生成类
 * ==========================
 */

if (defined('_CLASS_RSS_PHP')) return; 
define('_CLASS_RSS_PHP',1); 

class CI_Rss { 
	//public 
	var $rss_ver = "2.0"; 
	var $channel_title = ''; 
	var $channel_link = ''; 
	var $channel_description = ''; 
	var $language = 'zh-CN'; 
	var $copyright = ''; 
	var $webMaster = ''; 
	var $pubDate = ''; 
	var $lastBuildDate = ''; 
	var $generator = 'Cnfol RSS Generator'; 

	var $content = ''; 
	var $items = array(); 

	function CI_Rss() { 
	  
	} 
	
	function SetChannelTitle($title){
		$this->channel_title = $title; 
	}

	function SetChannelLink($link){
		$this->channel_link = $link;
	}

	function SetChannelDesc($description){
		$this->channel_description = $description;    
	}
	
	function AddItem($title, $link, $description ,$pubDate) { 
		$this->items[] = array('title' => $title , 
				   'link' => $link, 
				   'description' => $description, 
				   'pubDate' => $pubDate); 
	} 

	function BuildRSS() { 
		
		$s  = '';
		$s .= "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n<?xml-stylesheet type=\"text/css\" href=\"http://img.cnfol.com/blog/rssstyle.css\"?>\n<rss version=\"2.0\">\n"; 
		$s .= "<channel>\n"; 

		$s .= "<title><![CDATA[{$this->channel_title}]]></title>\n"; 
		$s .= "<link><![CDATA[{$this->channel_link}]]></link>\n"; 
		$s .= "<description><![CDATA[{$this->channel_description}]]></description>\n"; 
		$s .= "<language>{$this->language}</language>\n"; 
		if (!empty($this->copyright)) { 
			$s .= "<copyright><![CDATA[{$this->copyright}]]></copyright>\n"; 
		} 
		if (!empty($this->webMaster)) { 
			$s .= "<webMaster><![CDATA[{$this->webMaster}]]></webMaster>\n"; 
		} 
		if (!empty($this->pubDate)) { 
			$s .= "<pubDate>" . $this -> FormatDateTime($this->pubDate) . "</pubDate>\n"; 
		} 

		if (!empty($this->lastBuildDate)) { 
			$s .= "<lastBuildDate>{$this->lastBuildDate}</lastBuildDate>\n"; 
		} 

		if (!empty($this->generator)) { 
			$s .= "<generator>{$this->generator}</generator>\n"; 
		}	 
	  // start items 
		for ($i=0;$i<count($this->items);$i++) { 
			$description .= $this->items[$i]['description'] == "" ? '' : $this->items[$i]['description'].'......';
			$s .= "<item>\n"; 
			$s .= "<title><![CDATA[{$this->items[$i]['title']}]]></title>\n"; 
			$s .= "<link><![CDATA[{$this->items[$i]['link']}]]></link>\n"; 
			$s .= "<description><![CDATA[{$description}]]></description>\n"; 
			$s .= "<pubDate>" . $this -> FormatDateTime($this->items[$i]['pubDate']) . "</pubDate>\n";
			$s .= "<guid>{$this->items[$i]['link']}</guid>\n";          
			$s .= "</item>\n"; 
		} 
	 // close channel 
		$s .= "</channel></rss>";
		$this->content = $s; 
	} 

	function Show() { 
		if (empty($this->content)) 
		{
			$this->BuildRSS(); 
		}
		header("Content-Type:text/xml");
		echo $this->content; 
	} 

	function SaveToFile($fname) { 
		$handle = fopen($fname, 'wb'); 
		if ($handle === false)  return false; 
		fwrite($handle, $this->content); 
		fclose($handle); 
	}

	function FormatDateTime($time=0)
	{
		if(!$time)
		{
		  $timestamp = time();
		}else
		{
		  $timestamp = strtotime($time);
		}
		$newTime = date('Y-m-d H:i:s', $timestamp);
		return $newTime;
	}
} 

?>
