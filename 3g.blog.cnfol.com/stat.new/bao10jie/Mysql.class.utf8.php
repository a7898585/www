<?php
/*
	used:	website common file
	encode: utf-8  no bom
*/

/**
 * MySql
 * 
 * 小巧的mysql操作类，支持多个实例。
 * @author Camelot <camelot@gbmad.net>
 * @version 1.0
 * @package MySql
 */
class MySql {
	var $link;
	var $linktime;
	var $querynum = 0;

	/**
	 * Connect
	 * 
	 * 连接MySql数据库
	 * dbsn->数据库服务器地址
	 * dbun->登陆用户名
	 * dbpw->登陆密码
	 * dbname->数据库名字
	 */
	function Connect($dbsn, $dbun, $dbpw, $dbname) {
		$this->linktime = time();
		if ($this->link = @mysql_connect($dbsn, $dbun, $dbpw, true)) {
			if (floatval(mysql_get_server_info($this->link)) >= 4.1) {
				//$this->Query('SET NAMES utf8');
				#$this->Query('SET NAMES \'latin1\'');
			}
			$this->Select($dbname);
		} else {
			$this->halt('Can not connect to MySql server');
		}
	}

	/**
	 * Select
	 * 
	 * 选择使用数据库
	 * dbname->数据库名字
	 */
	function Select($dbname) {
		if (!mysql_select_db($dbname, $this->link)) {
			$this->halt('Can not Select DataBase');
		}
	}

	/**
	 * Query
	 * 
	 * 执行sql语句，返回对应的结果标识
	 * sql->sql语句
	 */
	function Query($sql) {
		if ((time() - $this->linktime) > 3) {
			$this->linktime = time();
			mysql_ping($this->link);
		}
		if ($query = mysql_query($sql, $this->link)) {
			$this->querynum++;
			return($query);
		} else {
			$this->querynum++;
			$this->halt('MySQL Query Error', $sql);
		}
	}

	/**
	 * Insert
	 * 
	 * 执行Insert Into语句，最后一个参数为真返回最后的insert操作所产生的自动增长的id
	 * table->要插入的表
	 * iarr->要插入的内容数组key=>value
	 * insertid->是否返回自动增长的id
	 */
	function Insert($table, $iarr, $insertid = false) {
		$value = $this->InsertSql($iarr);
		$query = $this->Query('INSERT INTO ' . $table . ' ' . $value);
		if ($insertid) {
			return(mysql_insert_id($this->link));
		} else {
			return($query);
		}
	}

	/**
	 * Update
	 * 
	 * 执行Update语句，最后一个参数为真返回最后的update操作所影响的行数
	 * table->要更新的表
	 * uarr->要更新的内容数组key=>value
	 * condition->更新条件
	 * affected->是否返回被更新的条数
	 */
	function Update($table, $uarr, $condition = '', $affected = false) {
		$value = $this->UpdateSql($uarr);
		if ($condition) {
			$condition = ' WHERE ' . $condition;
		}
		$query = $this->Query('UPDATE ' . $table . ' SET ' . $value . $condition);
		if ($affected) {
			return(mysql_affected_rows($this->link));
		} else {
			return($query);
		}
	}

	/**
	 * Delete
	 * 
	 * 执行Delete语句，最后一个参数为真返回最后的Delete操作所影响的行数
	 * table->要删除的表
	 * condition->删除条件
	 * affected->是否返回被删除的条数
	 */
	function Delete($table, $condition = '', $affected = false) {
		if ($condition) {
			$condition = ' WHERE ' . $condition;
		}
		$query = $this->Query('DELETE FROM `' . $table . '`' . $condition);
		if ($affected) {
			return(mysql_affected_rows($this->link));
		} else {
			return($query);
		}
	}
	/**
	*返回上次sql操作所插入的最后一个ID
	*/
	function InsertId() {
		return mysql_insert_id($this->link);
	}
	/**
	*返回上次insert,update,dellete所影响的记录数量
	*/	
	function AffectedRows() {
		return mysql_affected_rows($this->link);
	}
	/**
	 * EnCode
	 * 
	 * 将字符转为可以安全保存的mysql值，比如a'a转为a\'a
	 * str->要转换的字符
	 */
	function EnCode($str) {
		return(mysql_escape_string($str));
	}

	/**
	 * DeCode
	 * 
	 * 将可以安全保存的mysql值转为正常的值，比如a\'a转为a'a
	 * str->要转换的字符
	 */
	function DeCode($str) {
		return(str_replace('\\\'', '\'', $str));
	}

	/**
	 * InsertSql
	 * 
	 * 将对应的列和值生成对应的insert语句，如：array('id' => 1, 'name' => 'name')返回([id], [name]) VALUES (1, 'name')
	 * iarr->要转换的数组
	 */
	function InsertSql($iarr) {
		if (is_array($iarr)) {
			$fstr = '';
			$vstr = '';
			foreach ($iarr as $key => $val) {
				$fstr .= '`' . $key . '`, ';
				$vstr .= '\'' . $val . '\', ';
			}
			if ($fstr) {
				$fstr = '(' . substr($fstr, 0, -2) . ')';
				$vstr = '(' . substr($vstr, 0, -2) . ')';
				return($fstr . ' VALUES ' . $vstr);
			} else {
				return('');
			}
		} else {
			return('');
		}
	}

	
	/**
	 * UpdateSql
	 * 
	 * 将对应的列和值生成对应的insert语句，如：array('id' => 1, 'name' => 'name')返回[id] = 1, [name] = 'name'
	 * uarr->要转换的数组
	 */
	function UpdateSql($uarr) {
		if (is_array($uarr)) {
			$ustr = '';
			foreach ($uarr as $key => $val) {
				$ustr .= '`' . $key . '` = \'' . $val . '\', ';
			}
			if ($ustr) {
				return(substr($ustr, 0, -2));
			} else {
				return('');
			}
		} else {
			return('');
		}
	}

	/**
	 * GetRow
	 * 
	 * 返回对应的查询标识的结果的一行
	 * query->查询的识标
	 * result_type->返回结果类型
	 */
	function GetRow($query, $result_type = MYSQL_ASSOC) {
		return(mysql_fetch_array($query, $result_type));
	}

	/**
	 * Clear
	 * 
	 * 清空查询结果所占用的内存资源
	 * query->查询的识标
	 */
	function Clear($query) {
		return(mysql_free_result($query));
	}

	/**
	 * Close
	 * 
	 * 关闭数据库
	 */
	function Close() {
		return(mysql_close($this->link));
	}

	/**
	 * halt
	 * 
	 * 查询错误，终止信息
	 */
	function halt($message = '', $sql = '') {
		$message .= '<br />MySql Error:' . mysql_error();
		if ($sql) {
			$sql = '<br />sql:' . $sql;
		}
		exit('DataBase Error.<br />Message: ' . $message . $sql);
	}


	function get_values($tbname,$cell=1,$where=1,$limit=0)
	{
		$this->get_val=$this->get_array=array(); 
		unset($sql);

		/*
		//表单里的项	
		switch($cell)
		{
			case 1: $select = '*'; break;
			default: $select = $cell;
		}

		//条件		
		switch($where)
		{
			case 1: $w = ''; break;
			default: $w = ' where '.$where;
		}

		//数量
		switch($limit)
		{
			case 0 : $l = ''; break;
			default: $l = 'limit 0,'.$limit;
		}
		
		//sql语句
		$sql = $sql="select ".$select." from `".$tbname."`".$w.$l;
		

		$re= mysql_query($sql);//or error_log("Invalid query: " . mysql_error(),3,'mysql_error'.date('Y-m-d').'.log');

		if($re)
		{
			$field = $this->mysql_field_array($re);
			while($row=mysql_fetch_row($re))
			{
				foreach($row as $key=>$val)
				{
					$this->get_val[$field[$key]][] = $val;
				}
			}
			if($this->get_val) return $this->get_val;
			//else error_log($sql."\r\n",3,'/var/tmp/mysql_error'.date('Y-m-d').'.log');
		}
		*/
		$sql=$tbname;
		$re= mysql_query($sql);
		
		if($re)
		{
			$field = $this->mysql_field_array($re);
			
			$i=0;
			while($row=mysql_fetch_row($re))
			{
				
				foreach($row as $key=>$val)
				{
					$this->get_val[$field[$key]] = $val;
				}
				if($cell=='idstr')
				{
					$this->get_val['idstr']=sprintf('%d_%d_%d_%d_%d_%d_%d_%d_%d_%d_%d',$this->get_val['ArticleID'],strtotime($this->get_val['AppearTime']),$this->get_val['UserID'],$this->get_val['MemberID'],$this->get_val['SysTagID'],$this->get_val['IsTop'],$this->get_val['IsUsed'],$this->get_val['Prime'],$this->get_val['Recommend'],$this->get_val['IsDel'],$this->get_val['Status']);
				}
				$this->get_array[]=$this->get_val;
				$this->get_val=array();
				$i++;
			}
			if($this->get_array)
			{
				//if($i==1)
				//{
				//	return array('0'=>$this->get_array);
				//}
				return $this->get_array;
			}
			else
			{
				return false;
			}
			
		}
		else
		{
			return false;
		}
		
	}



	   function mysql_field_array( $query ) {
    
        $field = mysql_num_fields( $query );
    
        for ( $i = 0; $i < $field; $i++ ) {
        
            $names[] = mysql_field_name( $query, $i );
        
        }
        
        return $names;
    
    }


}
?>