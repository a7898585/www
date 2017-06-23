<?php 
	if (!defined('BASEPATH')) exit('No direct script access allowed');
	define('PINYIN_PATH', dirname(__FILE__).'/pinyin');
	
	/**
	 * tpye=1 中文第一个汉字首字母 =2中文每个汉字首字母 =3 全部
	 * @通过输入简繁体中文获取汉字的拼音
	 * @author leicc 
	**/
	function PinyinFromChNStr($chNStr, $type=1, $charSet='UTF-8')
	{
		require(PINYIN_PATH.'/class.Chinese.php');
		if($charSet == 'UTF-8')
			$chNStr	= @iconv('UTF-8','GB2312',$chNStr);

		$chs = new Chinese("GB2312", "PinYin", $chNStr);
		$pinyinStr = $chs->ConvertIT();
		if($type == 1)
		{
			return substr($pinyinStr, 0, 1);
		}
		else if($type == 2)
		{
			$ctstr = explode(' ', $pinyinStr);
			$crstr = '';
			foreach ($ctstr as $tstr)
			{
				$crstr .= substr($tstr, 0, 1);
			}
			return $crstr;	
		}
		else
		{
			return $pinyinStr;
		}
	}

?>