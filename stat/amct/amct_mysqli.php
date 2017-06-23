<?php
/*
 * amct_mysql for php_mysqli扩展 version: 1.0 beta author: wushuang(204966@qq.com)
 * modify: 2011-09-03
 */
include_once dirname(__FILE__) . '/amct_mysql_base.php';
abstract class amct_mysqli extends amct_mysql_base {
	const version = '1.0 beta';
	protected $connectId = null;
	protected $link = null;
	private $currentConfig = array();
	final public function __construct(){
		$this->__config();
		$this->currentConfig = self::$config[$this->connectId];
		self::$config[$this->connectId]['engine'] = 'mysqli';
		$this->connect();
	}
	abstract protected function __config();
	public function connect(){
		if(isset(self::$connects[$this->connectId])){
			return $this->reconnect();
		}
		$config = self::$config[$this->connectId];
		$this->link = new mysqli($this->currentConfig['host'], $this->currentConfig['user'], $this->currentConfig['password'], $this->currentConfig['database'], $this->currentConfig['port']);
		if($this->link->connect_errno){
			$this->halt();
			return false;
		}
		if(! $this->link->set_charset($this->currentConfig['charset'])){
			$this->halt();
			return false;
		}
		self::$connects[$this->connectId] = $this->link;
		return $this->link;
	}
	public function __destruct(){
		$this->close();
	}
	public function close(){
		if($this->link){
			if(self::$connects[$this->connectId])
				self::$connects[$this->connectId]->close();
			$this->link = null;
			unset(self::$connects[$this->connectId]);
		}
	}
	protected function halt(){
		if($this->link->connect_errno)
			throw new amct_database_exception($this->link->connect_error, $this->link->connect_errno);
		elseif($this->link->errno)
			throw new amct_database_exception($this->link->error, $this->link->errno);
	}
	public function reconnect(){
		if(isset(self::$connects[$this->connectId])){
			if(! self::$connects[$this->connectId]->ping()){
				self::$connects[$this->connectId]->close();
				unset(self::$connects[$this->connectId]);
				$this->link = new mysqli($this->currentConfig['host'], $this->currentConfig['user'], $this->currentConfig['password'], $this->currentConfig['database'], $this->currentConfig['port']);
				if($this->link->connect_errno){
					$this->halt();
					return false;
				}
				if(! $this->link->set_charset($this->currentConfig['charset'])){
					$this->halt();
					return false;
				}
				self::$connects[$this->connectId] = $this->link;
			}else{
				$this->link = self::$connects[$this->connectId];
			}
		}else{
			return $this->connect();
		}
		return $this->link;
	}
	public function select_db($dbName = null, $charset = 'utf8'){
		if($dbname){
			$this->currentConfig['dbname'] = $dbName;
			$this->currentConfig['charset'] = $charset;
		}
		if(! $this->link->select_db($this->currentConfig['dbname'])){
			$this->halt();
			return false;
		}
		if(! $this->link->set_charset($this->currentConfig['charset'])){
			$this->halt();
			return false;
		}
		return true;
	}
	private function query($sql){
		if($this->currentConfig['autoReconnect']){
			$this->reconnect();
		}
		$result = $this->link->query($sql);
		if($this->link->errno){
			$this->halt();
			return false;
		}
		return $result;
	}
	private function fetchAll($result){
		if($result){
			if(function_exists('mysqli_fetch_all')){
				$data = $result->fetch_all(MYSQLI_ASSOC);
				$result->free();
			}else{
				while($row = $result->fetch_assoc()){
					$data[] = $row;
				}
				$result->free();
			}
			return $data;
		}else
			return null;
	}
	private function fetch($result){
		if($result){
			$data = $result->fetch_assoc();
			$result->free();
			return $data;
		}else
			return null;
	}
	public function autocommit($open = true){
		return $this->link->autocommit($open);
	}
	public function commit(){
		return $this->link->commit();
	}
	public function rollback(){
		return $this->link->rollback();
	}
	/**
	 * 获取当前操作影响了多少行数据记录.
	 * 
	 * @return int
	 */
	public function affected_rows(){
		return $this->link->affected_rows;
	}
	/**
	 * 执行sql返回所有符合条件的数据记录.
	 * 
	 * @param $sql string       	
	 * @return Ambigous <boolean, NULL, multitype:>
	 */
	public function getAll($sql){
		$sql = $this->safe_sql(func_num_args(), func_get_args());
		if(! $result = $this->query($sql))
			return false;
		return $this->fetchAll($result);
	}
	/**
	 * 执行sql返回符合条件的第一条数据记录.
	 * 
	 * @param $sql string       	
	 * @return Ambigous <boolean, NULL, multitype:>
	 */
	public function getOne($sql){
		$sql = $this->safe_sql(func_num_args(), func_get_args());
		$sql_lower = strtolower($sql);
		if(substr($sql_lower, 0, 6) == 'select' && ! strstr($sql_lower, 'limit')){
			$sql .= ' limit 1';
		}
		if(! $result = $this->query($sql))
			return false;
		return $this->fetch($result);
	}
	/**
	 * 执行插入的sql语句操作
	 * 
	 * @param $sql string       	
	 * @return boolean
	 */
	public function insert($sql){
		$sql = $this->safe_sql(func_num_args(), func_get_args());
		return $this->query($sql);
	}
	/**
	 * 获得最后插入ID,须数据库支持
	 *
	 * @return int
	 */
	public function last_insert_id(){
		return $this->link->insert_id;
	}
	/**
	 * 执行删除的sql语句操作
	 * 
	 * @param $sql string       	
	 * @return boolean
	 */
	public function delete($sql){
		$sql = $this->safe_sql(func_num_args(), func_get_args());
		return $this->query($sql);
	}
	/**
	 * 执行更新的sql语句操作
	 * 
	 * @param $sql string       	
	 * @return boolean
	 */
	public function update($sql){
		$sql = $this->safe_sql(func_num_args(), func_get_args());
		return $this->query($sql);
	}
	/**
	 * 插入数组数据到数据表
	 *
	 * @param $tablename string       	
	 * @param $arrData array       	
	 * @param $get_last_insertid booleans       	
	 * @return int
	 */
	public function insertArray($tablename, $arrData){
		if(! $tablename || ! is_array($arrData) || ! count($arrData)){
			return false;
		}
		$sql_field_names = $sql_field_values = null;
		foreach($arrData as $fieldName => $fieldValue){
			$sql_field_names .= '`' . $fieldName . '`,';
			if(is_numeric($fieldValue))
				$sql_field_values .= "'" . $fieldValue . "',";
			else
				$sql_field_values .= "'" . $this->link->real_escape_string($fieldValue) . "',";
		}
		$sql_field_names = substr($sql_field_names, 0, - 1);
		$sql_field_values = substr($sql_field_values, 0, - 1);
		echo $sql = 'INSERT INTO `' . $tablename . '`(' . $sql_field_names . ') VALUES(' . $sql_field_values . ')';
		return $this->insert($sql);
	}
	/*
	 * public function insertArray($tableName, $arrData){ if(! $tableName || !
	 * is_array($arrData) || ! count($arrData)){ return false; } echo
	 * 111111111111; $number = count($arrData); $sql_fields = ''; $sql_values =
	 * ''; for($i = 0; $i < $number; $i ++){ $sql_fields .= '`%s`' . ($i <
	 * $number - 1 ? ',' : ''); $sql_values .= '\'%s\'' . ($i < $number - 1 ?
	 * ',' : ''); } $sql = 'insert into `' . $tableName . '`(' . $sql_fields .
	 * ') values(' . $sql_values . ')'; $params[] = $sql; $params =
	 * array_merge($params, array_keys($arrData, true),
	 * array_map('mysqli_real_escape_string', $this->link,
	 * array_values($arrData))); print_r($params); $sql =
	 * call_user_func_array('sprintf', $params); return $this->insert($sql); }
	 */
	public function replaceArray($tablename, $arrData){
		if(! $tablename || ! is_array($arrData) || ! count($arrData)){
			return false;
		}
		$sql_field_names = $sql_field_values = null;
		foreach($arrData as $fieldname => $fieldvalue){
			$sql_field_names .= '`' . $fieldname . '`,';
			$sql_field_values .= '\'' . $this->link->real_escape_string($fieldvalue) . '\',';
		}
		$sql_field_names = substr($sql_field_names, 0, - 1);
		$sql_field_values = substr($sql_field_values, 0, - 1);
		$sql = "REPLACE INTO `{$tablename}`({$sql_field_names}) VALUES({$sql_field_values})";
		return $this->insert($sql);
	}
	public function replaceWhere($table, $arrData, $where){
		if(! $where)
			return false;
		$sql = "SELECT * FROM `{$table}` WHERE " . $where;
		$rs = $this->getOne($sql);
		if($rs){
			return $this->updateWhere($table, $arrData, $where);
		}else{
			return $this->insertArray($table, $arrData);
		}
	}
	public function updateWhere($tablename, $arrData, $where = ''){
		if(! $tablename || ! is_array($arrData) || ! count($arrData)){
			return false;
		}
		if($where)
			$where = 'WHERE ' . $where;
		else
			$where = '';
		$sql_ext = null;
		foreach($arrData as $fieldname => $fieldvalue){
			if($fieldvalue === null){
				$sql_ext .= ($sql_ext ? "," : "") . "`{$fieldname}` = null";
			}else{
				$sql_ext .= ($sql_ext ? "," : "") . "`{$fieldname}` = '" . $this->link->real_escape_string($fieldvalue) . "'";
			}
		}
		$sql = "UPDATE `{$tablename}` SET {$sql_ext} {$where}";
		$result = $this->update($sql);
		return $result;
	}
	/**
	 * 执行数组形式的更新
	 *
	 * @param $tablename string
	 *       	 表名
	 * @param $arrData array       	
	 * @param $filters array
	 *       	 条件语句
	 * @return int 成功将返回更新的记录数
	 */
	public function updateArray($tablename, $arrData, $filters = array()){
		if(! $tablename || ! is_array($arrData) || ! count($arrData) || ! is_array($filters) || ! count($filters)){
			return false;
		}
		if(count($filters))
			$where = $this->Filters($filters);
		else
			$where = '';
		$sql_ext = null;
		foreach($arrData as $fieldname => $fieldvalue){
			if($fieldvalue === null){
				$sql_ext .= ($sql_ext ? "," : "") . "`{$fieldname}` = null";
			}else{
				$sql_ext .= ($sql_ext ? "," : "") . "`{$fieldname}` = '" . $this->link->real_escape_string($fieldvalue) . "'";
			}
		}
		$sql = "UPDATE `$tablename` SET {$sql_ext} {$where}";
		$result = $this->update($sql);
		return $result;
	}
	/**
	 * 自动生成条件语句
	 *
	 * @param $filters array       	
	 * @return string
	 */
	public function filters($filters){
		$sql_where = '';
		if(is_array($filters)){
			foreach($filters as $f => $v){
				$f_type = gettype($v);
				if($f_type == 'array'){
					$sql_where .= ($sql_where ? " AND " : "") . "(`{$f}` " . $v['operator'] . " '" . $v['value'] . "')";
				}else{
					$sql_where .= ($sql_where ? " AND " : "") . "(`{$f}` = '{$v}')";
				}
			}
		}elseif(strlen($filters)){
			$sql_where = $filters;
		}else
			return '';
		$sql_where = $sql_where ? " WHERE " . $sql_where : '';
		return $sql_where;
	}
	public function datagrid($countSql, $dataSql, $page, $pageSize){
		$page = max(1, $page);
		if($countSqlresult = $this->getOne($countSql))
			$result['recordCount'] = reset($countSqlresult);
		else
			$result['recordCount'] = 0;
		if(! $result['recordCount']){
			$result['page'] = $page;
			$result['pageSize'] = $pageSize;
			$result['pageCount'] = 0;
			$result['data'] = null;
			$result['nextPage'] = $result['backPage'] = 0;
			return $result;
		}
		$result['pageCount'] = ceil($result['recordCount'] / $pageSize);
		$page = min($result['pageCount'], $page);
		$offset = ($page - 1) * $pageSize;
		if($page < $result['pageCount'])
			$result['nextPage'] = $page + 1;
		if($page > 1)
			$result['backPage'] = $page - 1;
		$result['page'] = $page;
		$result['pageSize'] = $pageSize;
		$result['data'] = $this->getAll($dataSql . ' limit ' . $offset . ',' . $pageSize);
		$result['pageSize'] = min(count($result['data']), $pageSize);
		return $result;
	}
	public function safeSQL($sql){
		$sql = $this->safe_sql(func_num_args(), func_get_args());
		return $sql;
	}
	private function safe_sql($numParams, $params){
		if($numParams > 1){
			if($numParams == 2 && is_array($params[1])){
				$i = 1;
				foreach($params[1] as $value){
					$params[$i ++] = $value;
				}
			}else{
				for($i = 1; $i < $numParams; $i ++){
					$params[$i] = $this->link->real_escape_string($params[$i]);
				}
			}
			$sql = call_user_func_array('sprintf', $params);
		}else{
			$sql = $params[0];
		}
		return $sql;
	}
}
