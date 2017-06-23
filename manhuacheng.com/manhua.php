<?php
/*
 * 漫画详细信息接口
 * @pram int id
 */
require_once 'MHinclude/common.inc.php';
$msg='<?xml version="1.0" encoding="utf-8"?>';
if($id){
	$db = new db($db_config);
	$strsql="select ID,CartoonName,CartoonAreaID,Author,UpdateTime,CartoonState,CartoonImage,Synopsis from mh_cartoon where ID='$id' limit 1";
	
	$manhuares=$db->GetOne($strsql);
	
		if($manhuares){
			$areasql="select ID,AreaName from mh_cartoonArea where ID='".$manhuares['CartoonAreaID']."'";
			$areares=$db->GetOne($areasql);
					$chaptersql="select mh_chapter.ID,mh_chapter.Name,mh_chapter.Url,mh_chapter.UpdateTime from mh_cartoondetail,mh_chapter where MHid='$id'AND mh_chapter.MHDetailID=mh_cartoondetail.ID order by mh_chapter.ID DESC";
					$db->Execute('chapterrs',$chaptersql);
					while($row=$db->GetArray('chapterrs')){
						$chapterrsult[]=$row;
					}
			
		}else {
			$xml=$msg.'<Book Count="0"></Book>';
			echo $xml;
			$db->Close ();
			exit;
			
		}
		$list.='<Book id="'.$manhuares['ID'].'" Name="'.$manhuares['CartoonName'].'" Author="'.$manhuares['Author'].'" UpdateTime="'.$manhuares['UpdateTime'].'" State="'.$manhuares['CartoonState'].'" Image="'.$manhuares['CartoonImage'].'" Rem="'.$manhuares['Synopsis'].'" Area="'.$areares['AreaName'].'">';                                                              
	    
		if($chapterrsult){
	    	foreach ($chapterrsult as $v){
						$list.='<Chapter id="'.$v['ID'].'" Name="'.$v['Name'].'" UpdateTime="'.$v['UpdateTime'].'" Url="'.$v['Url'].'"/>';
					
				}
	    }
	    
	    $list.='</Book>';
	    
	    $xml=$msg.$list;
	    
	    echo $xml;
	    $db->Close ();
	    unset ( $db, $manhuares, $chapterrsult, $list, $xml );
	    
}else {
	$xml='<Book></Book>';
	echo $xml;
}
?>