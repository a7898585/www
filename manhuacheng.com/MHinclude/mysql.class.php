<?php
/*
 * mysql操作类
 */
class db{
	var $linkID;
	var $host;
	var $user;
	var $pwd;
	var $name;
	var $result;

	function __construct($db_config){
		$this->linkID = 0;
		$this->host = $db_config["hostname"];
		$this->user = $db_config["username"];
		$this->pwd  = $db_config["password"];
		$this->name = $db_config["database"];
		$this->result["me"] = 0;
		$this->Open();
	}

	//
	//连接数据库
	//
	function Open(){
		//连接数据库
		$this->linkID = @mysql_connect($this->host,$this->user,$this->pwd);

		//处理错误，成功连接则选择数据库
		if(!$this->linkID){
			$this->DisplayError("Connect Database Server False!");
			return false;
		}
		else{ @mysql_select_db($this->name); }
		@mysql_query("SET NAMES UTF8;",$this->linkID);
		return true;
	}
	//
	//获得错误描述
	//
	function GetError(){
		$str = ereg_replace("'|\"","`",mysql_error());
		return $str;
	}
	//
	//关闭数据库
	//
	function Close(){
		@mysql_close($this->linkID);
		$this->FreeResultAll();
	}
	//
	//关闭指定的数据库连接
	//
	function CloseLink($dblink){
		@mysql_close($dblink);
	}
	//
	//执行一个不返回结果的SQL语句，如update,delete,insert等
	//
	function ExecuteNoneQuery($sql=""){
		return mysql_query($sql,$this->linkID);
	}
	function ExecNoneQuery($sql=""){
		return $this->ExecuteNoneQuery($sql);
	}
	//
	//执行一个带返回结果的SQL语句，如SELECT，SHOW等
	//
	function Execute($id="me",$sql=""){
		$this->result[$id] = @mysql_query($sql,$this->linkID);
		if(!$this->result[$id]){
			$this->DisplayError(mysql_error()." - Execute Query False!");
		}
	}
	function Query($id="me",$sql=""){
		$this->Execute($id,$sql);
	}
	//
	//执行一个SQL语句,返回前一条记录或仅返回一条记录
	//
	function GetOne($sql=""){
		$this->Execute("one",$sql);
		$arr = $this->GetArray("one");
		
		if(!is_array($arr)) return("");
		else { @mysql_free_result($this->result["one"]); return($arr);}
	}
	//
	//移动指针
	//
	function Seek($id="me",$num){
		mysql_data_seek($this->result[$id],$num);
	}
	//
	//执行一个不与任何表名有关的SQL语句,Create等
	//
	function ExecuteSafeQuery($sql){
		@mysql_query($sql,$this->linkID);
	}
	//
	//返回当前的一条记录并把游标移向下一记录
	//
	function GetArray($id="me"){
		if($this->result[$id]==0) return false;
		else return @mysql_fetch_array($this->result[$id]);
	}
	function GetObject($id="me"){
		if($this->result[$id]==0) return false;
		else return mysql_fetch_object($this->result[$id]);
	}
	//
	//检测是否存在某数据表
	//
	function IsTable($tbname){
		$this->result = mysql_list_tables($this->name,$this->linkID);

		while ($row = mysql_fetch_array($this->result)){
			if($row[0]==$tbname){
				mysql_freeresult($this->result);
				return true;
			}
		}
		mysql_freeresult($this->result);
		return false;
	}
	//
	//获得MySql的版本号
	//
	function GetVersion(){
		$rs = mysql_query("SELECT VERSION();",$this->linkID);
		$row = mysql_fetch_array($rs);
		$mysql_version = $row[0];
		mysql_free_result($rs);
		return $mysql_version;
	}
	//
	//获取特定表的信息
	//
	function GetTableFields($tbname,$id="me"){
		$this->result[$id] = mysql_list_fields($this->dbName,$tbname,$this->linkID);
	}
	//
	//获取字段详细信息
	//
	function GetFieldObject($id="me"){
		return mysql_fetch_field($this->result[$id]);
	}
	//
	//获得查询的总记录数
	//
	function GetTotalRow($id="me"){
		if($this->result[$id]==0) return -1;
		else return mysql_num_rows($this->result[$id]);
	}
	//
	//获取上一步INSERT操作产生的ID
	//
	function GetLastID(){
		//如果 AUTO_INCREMENT 的列的类型是 BIGINT，则 mysql_insert_id() 返回的值将不正确。
		//可以在 SQL 查询中用 MySQL 内部的 SQL 函数 LAST_INSERT_ID() 来替代。
		//$rs = mysql_query("Select LAST_INSERT_ID() as lid",$this->linkID);
		//$row = mysql_fetch_array($rs);
		//return $row["lid"];
		return mysql_insert_id($this->linkID);
	}
	//
	//释放记录集占用的资源
	//
	function FreeResult($id="me"){
		@mysql_free_result($this->result[$id]);
	}
	function FreeResultAll(){
		if(!is_array($this->result)) return "";
		foreach($this->result as $kk => $vv){
			if($vv) @mysql_free_result($vv);
		}
	}

	//
	//显示数据链接错误信息
	//
	function DisplayError($msg){
		echo "<html>\r\n";
		echo "<head>\r\n";
		echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>\r\n";
		echo "<title>DB Error</title>\r\n";
		echo "</head>\r\n";
		echo "<body>\r\n<p style='line-helght:150%;font-size:10pt'>\r\n";
		echo $msg;
		echo "<br/><br/>";
		echo "</p>\r\n</body>\r\n";
		echo "</html>";
		exit();
	}
}
?>