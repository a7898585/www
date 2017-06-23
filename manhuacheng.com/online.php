<?php
require_once 'MHinclude/common.inc.php';
switch ($a){
	case 'i':
		$sql="SELECT count(distinct(IP)) as num FROM mh_loginlog where LoginCount=1";
		$db = new db ( $db_config );
		$install=$db->GetOne($sql);
		echo "document.write(".$install['num'].")";
		break;
	default:
		break;
}
?>