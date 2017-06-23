<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| Mysqli 扩展库  0.1
|--------------------------------------------------------------------------
|
| 此文件放于system/libraries/下
|
| 使用方法:
| $db = $this->load->cnfol_database('test');
| $sql = 'select * from test limit 4';
| $rs = $db->GetRow($sql);
|--------------------------------------------------------------------------
*/
if(!class_exists('Mysqli'))
{
    show_error("Unable to load the requested base class: Mysqli");
}

class CI_Mysqli
{

    var $conn_id;
    var $result;
    var $configs;
    var $_escape_char = '`';
    var $_debug = false;
    var $_log = 'base/mysqli.log';

    function CI_Mysqli($db_params)
    {
        # 取得配置信息
        $this->configs = $db_params;
        $this->_debug = $this->configs['db_debug'];
        return $this;
    }

    /**
     * 链接数据库,成功返回true,失败返回false
     */
    function connect()
    {
        $this->conn_id = new mysqli($this->configs['hostname'],$this->configs['username'],
            $this->configs['password'],$this->configs['database']);
        if(mysqli_connect_errno())
        {
            log_write("connect fail host为{$this->configs['server']['host']}". mysqli_connect_error(),$this->_log,__METHOD__ ,'ERROR');

            if($this->_debug)
            {
                $this->debug('connect fail,database info ='.print_r($this->configs,true));
            }

            return false;
        }
        $this->db_set_charset($this->configs['char_set'],$this->configs['dbcollat']);
        return true;
    }

    /**
     * 选择数据库,成功返回true,失败返回false
     */
    function db_select($dbname)
    {
        $rs = $this->conn_id->select_db($dbname);
        if($rs === false)
        {
            log_write("选择数据库{$dbname}失败",$this->_log,__METHOD__ ,'ERROR');

            if($this->_debug)
            {
                $this->debug('选择数据库{$dbname}失败');
            }
        }
        return $rs;
    }

    /**
     * 查询获得多行数据
     * 即取第多条记录;出错返回false;有数据则返回一个二维数组,没数据则返回NULL
     */
    function query($sql)
    {
        $this->result= $this->conn_id->query($sql);
        if($this->result === false)
        {
            log_write("SQL=={$sql}",$this->_log,__METHOD__ ,'ERROR');

            if($this->_debug)
            {
                $this->debug(__METHOD__.' SQL == '.$sql);
            }

            return false;
        }else{
            $rows = null;
            while ($row = $this->result->fetch_assoc())
            {
                $rows[] = $row;
            }
            $this->result->close();
            return $rows;
        }
    }

    /**
     * 取单个字段
     * 即取出第一条记录的第一个字段的值,出错则返回false，没数据返回NULL
     */

    function GetOne($sql)
    {
        $this->result= $this->conn_id->query($sql);
        if($this->result === false)
        {
            log_write("SQL=={$sql}",$this->_log,__METHOD__ ,'ERROR');

            if($this->_debug)
            {
                $this->debug(__METHOD__.' SQL == '.$sql);
            }

            return false;
        }else{
            $row = $this->result->fetch_array(MYSQLI_NUM);
            $this->result->close();
        }
        return $row[0];
    }


    /**
     * 取单字段多条记录
     * 即取出第一个字段的多条记录的值,并返回一个数组,出错则返回false，没数据返回NULL
     */
    function GetCol($sql)
    {
        $this->result= $this->conn_id->query($sql);
        if($this->result === false)
        {
            log_write("SQL=={$sql}",$this->_log,__METHOD__ ,'ERROR');

            if($this->_debug)
            {
                $this->debug(__METHOD__.' SQL == '.$sql);
            }

            return false;
        }else{
            $rows = null;

            while ($row = $this->result->fetch_row())
            {
                $rows[] = $row[0];
            }
            $this->result->close();
            return $rows;
        }
    }

    /**
     * 取单条记录
     * 即取第一条记录;出错返回false，有数据则返回一个数组,没数据则返回NULL
     */

    function GetRow($sql)
    {
        $this->result= $this->conn_id->query($sql);
        if($this->result === false)
        {
            log_write("SQL=={$sql}",$this->_log,__METHOD__ ,'ERROR');

            if($this->_debug)
            {
                $this->debug(__METHOD__.' SQL == '.$sql);
            }

            return false;
        }else{
            $row = $this->result->fetch_assoc();
            $this->result->close();
        }
        return $row;
    }


    /**
     * 可执行更新,插入或删除语句,成功返回true,失败返回false
     */
    function execute($sql)
    {
        $this->result= $this->conn_id->query($sql);
        if($this->result === false)
        {
            log_write("SQL=={$sql}",$this->_log,__METHOD__ ,'ERROR');

            if($this->_debug)
            {
                $this->debug(__METHOD__.' SQL == '.$sql);
            }

            return false;
        }
        return true;
    }

    /**
     * 执行insert、update和delete影响到行数
     */

    function affected_rows()
    {
        return $this->conn_id->affected_rows;
    }

    /**
     * 插入功能
     *
     * @param  $table     string  表名
     * @param  $data      array   插入数据  格式：array('a'=>1,'b'=>'string')
     * @return            boolean  成功返回true,失败返回false
     */
    function insert($table, $data)
    {
        $fields = array();
        $values = array();

        foreach($data as $key => $val)
        {
            $fields[] = $key;
            $values[] = $this->escape($val);
        }

        $sql = "INSERT INTO ".$this->_escape_table($table)." (".implode(', ', $fields).") VALUES (".implode(', ', $values).")";

         return $this->execute($sql);
    }

    /**
     * 取得执行插入语句后,自增id
     */

    function insert_id()
    {
        return $this->conn_id->insert_id;
    }

    /**
     * 可执行更新语句,成功返回true,失败返回false
     *
     * @param  $table     string  表名
     * @param  $data      array   更新数据  格式：array('a'=>1,'b'=>'string')
     * @param  $where     string  条件      格式：id<100 and age>10
     * @param  $orderby   string  排序
     * @param  $limit     string  限制条数，false表示不限制
     * @return            boolean   成功返回true,失败返回false
     */
    function update($table, $data =array(), $where, $orderby = array(), $limit = FALSE)
    {
        foreach($data as $key => $val)
        {
            $valstr[] = $key." = ".$val;
        }

        $limit = ( ! $limit) ? '' : ' LIMIT '.$limit;

        $orderby = (count($orderby) >= 1)?' ORDER BY '.implode(", ", $orderby):'';

        $sql = "UPDATE ".$this->_escape_table($table)." SET ".implode(', ', $valstr);
        $sql .= ($where != '') ? " WHERE ".$where: '';
        $sql .= $orderby.$limit;

         return $this->execute($sql);
    }


    /**
     * 删除功能
     *
     * @param  $table  string  表名
     * @param  $where     string  条件      格式：id<100 and age>10
     * @param  $limit string  限制条数，false表示不限制
     * @return  boolean   成功返回true,失败返回false
     */
    function delete($table, $where = '', $limit = FALSE)
    {
        $conditions = '';

        if ($where !='')
        {
            $conditions = " WHERE ".$where;
        }

        $limit = ( ! $limit) ? '' : ' LIMIT '.$limit;

        $sql = "DELETE FROM ".$table.$conditions.$limit;

        $this->execute($sql);

		if($this->affected_rows()>=1)
			return true;
		else
			return false;
    }



    /**
     * 设置 client 字符集
     *
     * @param  $charset    string  编码
     * @param  $collation  string  字符集
     * @return  boolean   成功返回true,失败返回false
     */
    function db_set_charset($charset, $collation)
    {
         #$sql = "SET NAMES '".$this->escape_str($charset)."' COLLATE '".$this->escape_str($collation)."'";
         #return $this->execute($sql);
        return $this->conn_id->set_charset($charset);
    }


    /**
     * 给表名添加"`"字符
     *
     * @param  $table  string  表名
     * @return  string
     */
    function _escape_table($table)
    {
        if (strpos($table, '.') !== FALSE)
        {
            $table = '`' . str_replace('.', '`.`', $table) . '`';
        }

        return $table;
    }

    /**
     * 格式化数据
     *
     * @param  $str  string 字符串
     * @return  mix
     */
    function escape($str)
    {
        switch (gettype($str))
        {
            case 'string'   :
                $str = "'".$this->escape_str($str)."'";
            break;
            case 'boolean'  :
                $str = ($str === FALSE) ? 0 : 1;
            break;
            default         :
                $str = ($str === NULL) ? 'NULL' : $str;
            break;
        }
        return $str;
    }

    /**
     * 转义字符串方法
     *
     * @param  $str  string 字符串
     * @return  string 字符串
     */
    function escape_str($str)
    {
		if (!get_magic_quotes_gpc()) 
		{

			if (function_exists('mysqli_real_escape_string') AND is_object($this->conn_id))
			{
				return mysqli_real_escape_string($this->conn_id, $str);
			}
			elseif (function_exists('mysql_escape_string'))
			{
				return mysql_escape_string($str);
			}
			else
			{
				return addslashes($str);
			}
		}
		else
		{
			return $str;
		}
    }

    /**
     * 版本
     */

    function version()
    {
        return $this->conn_id->server_version;
    }

    /**
     * 关闭链接
     */

    function close()
    {
        if (is_resource($this->conn_id) or is_object($this->conn_id))
        {
            return $this->conn_id->close();
        }
        return true;
    }

    /**
     * DEBUG
     */
    function debug($error = '')
    {
         echo $error.'<br />';
         exit;
    }

}

// END Mysqli Class

/* End of file Mysqli.php */
/* Location: ./system/libraries/Mysqli.php */
