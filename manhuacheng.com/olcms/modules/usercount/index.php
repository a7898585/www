<?php
/**
 * 用户安装卸载统计
 */
defined ( 'IN_OLCMS' ) or exit ( 'No permission resources.' );
pc_base::load_app_class('admin','admin',0);
class index extends admin{
	private $count_db,$s,$e,$canal;
	function __construct() {
		pc_base::load_sys_class('form', '', 0);
	//$this->db = pc_base::load_model('safeuninstall_model');
		$this->canal=array(1=>'漫画城',2=>'360',3=>'漫画城积分活动',4=>'1616',5=>'多特2345',6=>'金山',7=>'QQ');
		$this->s=isset($_GET['start_time'])?strtotime($_GET['start_time'].' 00:00:00'):mktime(0, 0, 0, date("m"), date("d"),date("Y"));
		$this->e=isset($_GET['end_time'])?strtotime($_GET['end_time'].' 23:59:59'):time();
		require_once OLCMS_PATH.'MHinclude/common.inc.php';
		$this->count_db=new db($db_config);
		
	}
	public function init() {
		$canal=$this->canal;
		$initsql="SELECT ParentID,count(*) as num2 FROM mh_loginlog where loginCount=1 and unix_timestamp(LoginTime) >=".$this->s." and ".$this->e.">=unix_timestamp(LoginTime) group by ParentID";
		$initsqlnum="SELECT count(*) as num FROM mh_loginlog where loginCount=1 and unix_timestamp(LoginTime) >=".$this->s." and ".$this->e.">=unix_timestamp(LoginTime)";
		
		$this->count_db->Execute('rc',$initsql);

		while($r=$this->count_db->GetArray('rc')){
			$initrc[]=$r;
		}
		$c=$this->count_db->GetOne($initsqlnum);
		//安装
		$rbcsql='SELECT parentID,count(distinct(IP)) as num FROM mh_loginlog where LoginCount=1 group by ParentID';
		$rbcsqlnum='SELECT count(distinct(IP)) as num FROM mh_loginlog where LoginCount=1';
		$m=$this->count_db->GetOne($rbcsqlnum);
		$this->count_db->Execute('rbc',$rbcsql);
	  	while($r=$this->count_db->GetArray('rbc')){
	  		$rbc[]=$r;
	  	}
	  	//卸载
		$rbcsql1='SELECT parentID,count(*) as num1 FROM mh_setuplog where Type=1 group by ParentID';
		$rbcsql1num='SELECT count(*) as num1 FROM mh_setuplog where Type=1';

		$n=$this->count_db->GetOne($rbcsql1num);
		$this->count_db->Execute('rbc1',$rbcsql1);
	  	while($r=$this->count_db->GetArray('rbc1')){
	  		$rbc1[]=$r;
	  	}
		foreach ($rbc as $k=>$v){
			foreach ($rbc1 as $k1=>$v1){
				if($v1['parentID']==$v['parentID']){
					$rbc[$k]=array_merge($rbc[$k],$rbc1[$k1]);
				}
			}
		}
		foreach ($rbc as $k=>$v){
			foreach ($initrc as $k1=>$v1){
				if($v1['ParentID']==$v['parentID']){
					$rbc[$k]=array_merge($rbc[$k],$initrc[$k1]);
				}
			}
		}
		include $this->admin_tpl('usercount_list');

	}



	//SELECT parentID,count(*) FROM mh_loginlog where Type=0 and  group by ParentID
	public function statbytime(){
	  $canal=$this->canal;
		//安装
		$rbcsql='SELECT parentID,count(distinct(IP)) as num FROM mh_loginlog where loginCount=1 and unix_timestamp(LoginTime) >='.$this->s.' and '.$this->e.'>=unix_timestamp(LoginTime) group by ParentID';
//		echo $rbcsql;
		$rbcsqlnum='SELECT count(distinct(IP)) as num FROM mh_loginlog where loginCount=1 and unix_timestamp(LoginTime) >='.$this->s.' and '.$this->e.'>=unix_timestamp(LoginTime)';
		$m=$this->count_db->GetOne($rbcsqlnum);
		$this->count_db->Execute('rbc',$rbcsql);
	  	while($r=$this->count_db->GetArray('rbc')){
	  		$rbc[]=$r;
	  	}
	  	//卸载
		$rbcsql1='SELECT parentID,count(*) as num1 FROM mh_setuplog where Type=1 and unix_timestamp(LogTime) >='.$this->s.' and '.$this->e.'>=unix_timestamp(LogTime) group by ParentID';
//		echo $rbcsql1;
		$rbcsql1num='SELECT count(*) as num FROM mh_setuplog where Type=1 and unix_timestamp(LogTime) >='.$this->s.' and '.$this->e.'>=unix_timestamp(LogTime)';
		
		$n=$this->count_db->GetOne($rbcsql1num);
		$this->count_db->Execute('rbc1',$rbcsql1);
	  	while($r=$this->count_db->GetArray('rbc1')){
	  		$rbc1[]=$r;
	  	}
	  	$n=$this->count_db->GetOne($rbcsql1num);
		foreach ($rbc as $k=>$v){
				foreach ($rbc1 as $k1=>$v1){
					if($v1['parentID']==$v['parentID']){
						$rbc[$k]=array_merge($rbc[$k],$rbc1[$k1]);
					}
				}
		}
		include $this->admin_tpl('uc_condition');
	}
	public function onlinetoday(){
		$otsql="select count(*) as num from mh_loginlog where Type=0 and unix_timestamp(LoginTime)>=".mktime(0, 0, 0, date("m"), date("d"),date("Y"))." and unix_timestamp(LoginTime)<=".mktime(23, 59, 59, date("m"), date("d"),date("Y"));
		$ot=$this->count_db->GetOne($otsql);
		echo '今天活跃数：'.$ot['num'];
		
		$otsql1="select count(*) as num from mh_loginlog where Type=0 and unix_timestamp(LoginTime)>=".mktime(0, 0, 0, date("m"), date("d")-1,date("Y"))." and unix_timestamp(LoginTime)<=".mktime(23, 59, 59, date("m"), date("d")-1,date("Y"));
		$ot1=$this->count_db->GetOne($otsql1);
		
		echo '<br/>昨日活跃数：'.$ot1['num'];
		
		
		$otsql3="select count(*) as num from mh_loginlog where Type=0 and unix_timestamp(LoginTime)>=".mktime(0, 0, 0, date("m"), date("d"),date("Y"))." and unix_timestamp(LoginTime)<=".mktime(23, 59, 59, date("m"), date("d"),date("Y"))." group by IP";
		$this->count_db->Execute('res',$otsql3);
		while($r=$this->count_db->GetArray('res')){
	  		$res1[]=$r;
	  	}
	  	
		echo '<br/>今日单IP活跃数：'.count($res1);
		
		$otsql2="select count(*) as num from mh_loginlog where Type=0 and unix_timestamp(LoginTime)>=".mktime(0, 0, 0, date("m"), date("d")-1,date("Y"))." and unix_timestamp(LoginTime)<=".mktime(23, 59, 59, date("m"), date("d")-1,date("Y"))." group by IP";
		$this->count_db->Execute('res',$otsql2);
		while($r=$this->count_db->GetArray('res')){
	  		$res[]=$r;
	  	}
		echo '<br/>昨日单IP活跃数：'.count($res);
		
		$ot1sql="select count(*) as num from mh_loginlog where Type=1 and unix_timestamp(LoginTime)>=".mktime(0, 0, 0, date("m"), date("d"),date("Y"))." and unix_timestamp(LoginTime)<=".mktime(23, 59, 59, date("m"), date("d"),date("Y"));
		$ot1=$this->count_db->GetOne($ot1sql);
		//
		echo '<br/>当前在线人数:'.($ot['num']-$ot1['num']);
	}
	public function onlinebytime(){
		$otsql="select count(*) as num from mh_loginlog where Type=0 and unix_timestamp(LoginTime)>=".$this->s." and unix_timestamp(LoginTime)<=".$this->e;
//		echo $otsql;
		$ot=$this->count_db->GetOne($otsql);
		echo $_GET['start_time'].'活跃数：'.$ot['num'];
//		$ot1sql="select count(*) as num from mh_loginlog where Type=1 and unix_timestamp(LoginTime)>=".mktime(0, 0, 0, date("m"), date("d"),date("Y"))." and unix_timestamp(LoginTime)<=".mktime(23, 59, 59, date("m"), date("d"),date("Y"));
//		$ot1=$this->count_db->GetOne($ot1sql);
//		echo '<br/>当前在线人数:'.($ot['num']-$ot1['num']);
	}
	
	
	
}
?>