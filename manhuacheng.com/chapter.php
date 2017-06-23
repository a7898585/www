<?php
/*
 * 漫画章节信息
 * @pram int chapterid
 * 
 */
require_once 'MHinclude/common.inc.php';
$msg='<?xml version="1.0" encoding="utf-8"?>';
if($id){
	$db = new db($db_config);
	$strsql="select ID,MHDetailID,Name,Url,UpdateTime from mh_chapter where ID='$id' limit 1";
	
	$chapterres=$db->GetOne($strsql);
	
		if($chapterres){
					$pagesql="select ID,CID,ImageUrl,RefererUrl from mh_imagedetail where CID='$id'";
					
					$db->Execute('pagers',$pagesql);
					while($row=$db->GetArray('pagers')){
						$cresult[]=$row;
					}
			
		}else {
			$xml=$msg.'<Chapter Count="0"></Chapter>';
			echo $xml;
			$db->Close ();
			exit;
			
		}
		$list.='<Chapter id="'.$chapterres['ID'].'" Name="'.$chapterres['Name'].'" UpdateTime="'.$chapterres['UpdateTime'].'" Url="'.$chapterres['Url'].'">';                                                              
	    
		if($cresult){
	    	foreach ($cresult as $v){
						$list.='<Page id="'.$v['ID'].'" Url="'.$v['ImageUrl'].'" Referer="'.$v['RefererUrl'].'" />';
					
				}
	    }
	    
	    $list.='</Chapter>';
	    
	    $xml=$msg.$list;
	    echo $xml;
	    $db->Close ();
	    unset ( $db, $cresult, $chapterres, $list, $xml );
	    
}else{
	$xml='<Chapter></Chapter>';
	echo $xml;
}
?>