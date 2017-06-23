<?php

/*
 * amct_mysql for php_mysql扩展 version: 1.0 beta author: wushuang(204966@qq.com)
 * modify: 2011-09-03
 */
include_once dirname(__FILE__) . '/amct_mysql_base.php';

abstract class amct_mysql extends amct_mysql_base {

    const version = '1.0 beta';

    protected $connectId = null;
    protected $link = null;
    private $currentConfig = array();
    private $cache = '';

    public function __construct() {
        $this->__config();
        self::$config[$this->connectId]['engine'] = 'mysql';
        if (self::$config[$this->connectId]['port'])
            self::$config[$this->connectId]['host'] = self::$config[$this->connectId]['host'] . ':' . self::$config[$this->connectId]['port'];
        $this->currentConfig = self::$config[$this->connectId];
        $this->connect();
    }

    abstract protected function __config();

    /**
     * 是否启用sql语句缓存
     * @param bool $cache
     * @return bool
     */
    public function enableCache($cache=true) {
        return $this->cache = $cache;
    }

    public function connect() {
        if (isset(self::$connects[$this->connectId])) {
            return $this->reconnect();
        }
        if (!self::$connects[$this->connectId] = mysql_connect($this->currentConfig['host'], $this->currentConfig['user'], $this->currentConfig['password'])) {
            $this->halt();
            return false;
        }
        $this->link = self::$connects[$this->connectId];
        if ($this->currentConfig['database'])
            $this->select_db();
        return $this->link;
    }

    public function close() {
        if (self::$connects[$this->connectId]) {
            mysql_close(self::$connects[$this->connectId]);
            unset(self::$connects[$this->connectId]);
            $this->link = null;
        }
    }

    protected function halt($sql = null) {
        throw new amct_database_exception(mysql_error() . ($sql ? '[SQL]' . $sql : ''), mysql_errno());
    }

    public function reconnect() {
        if (isset(self::$connects[$this->connectId])) {
            if (!mysql_ping(self::$connects[$this->connectId])) {
                mysql_close(self::$connects[$this->connectId]);
                unset(self::$connects[$this->connectId]);
                if (!self::$connects[$this->connectId] = mysql_connect($this->currentConfig['host'], $this->currentConfig['user'], $this->currentConfig['password'])) {
                    $this->halt();
                    return false;
                } else {
                    $this->link = self::$connects[$this->connectId];
                }
            } else {
                $this->link = self::$connects[$this->connectId];
            }
        } else {
            return $this->connect();
        }
        if ($this->currentConfig['database'])
            $this->select_db();
        return $this->link;
    }

    public function select_db($dbName = null, $charset = 'utf8') {
        if ($dbName) {
            $this->currentConfig['database'] = $dbName;
            $this->currentConfig['charset'] = $charset;
        }
        if (!mysql_select_db($this->currentConfig['database'], $this->link)) {
            $this->halt();
            return false;
        }
        if ($this->currentConfig['charset']) {
            if (!mysql_set_charset($this->currentConfig['charset'], $this->link)) {
                $this->halt();
                return false;
            }
        }
        return true;
    }

    private function query($sql) {
        if ($this->currentConfig['autoReconnect']) {
            $this->reconnect();
        }
        /* $stime=microtime(true);
          echo("<div >".$sql."<br></div>");
          $result = mysql_query($sql, $this->link);
          echo("<div>".(microtime(true)-$stime)."<br></div>"); */
        $result = mysql_query($sql, $this->link);
        if (!$result)
            $this->halt($sql);
        return $result;
    }

    private function fetchAll($result) {
        if ($result) {
            $data = array();
            while ($row = mysql_fetch_assoc($result)) {
                $data[] = $row;
            }
            $this->free_result($result);
            return $data;
        }else
            return null;
    }

    private function fetch($result) {
        if ($result) {
            $data = mysql_fetch_assoc($result);
            $this->free_result($result);
            return $data;
        }else
            return null;
    }

    private function free_result($result) {
        return mysql_free_result($result);
    }

    public function autocommit($open = true) {
        if ($open) {
            $sql = 'SET AUTOCOMMIT=1';
        }else
            $sql = 'SET AUTOCOMMIT=0';
        return $this->query($sql);
    }

    /*
     * 执行数据提交
     */

    public function commit() {
        $result = $this->query('COMMIT');
        return $result;
    }

    /*
     * 执行数据回滚
     */

    public function rollback() {
        $result = $this->query('ROLLBACK');
        return $result;
    }

    /*
     * 开启commit 将autocommit设置为0
     */

    public function beginCommit() {
        return $this->autocommit(false);
    }

    /*
     * 结束commit 将autocommit设置为1
     */

    public function endCommit() {
        return $this->autocommit(true);
    }

    /**
     * 获取当前操作影响了多少行数据记录.
     *
     * @return int
     */
    public function affected_rows() {
        return mysql_affected_rows($this->link);
    }

    /**
     * 执行sql返回所有符合条件的数据记录.
     *
     * @param $sql string
     * @return Ambigous <boolean, NULL, multitype:>
     */
    public function getAll($sql) {
        $sql = $this->safe_sql(func_num_args(), func_get_args());
        if ($this->cache) {
            $cacheId = md5($sql);
            $data = amct_cache::cache($cacheId);
            if (!$data || $this->cache_refresh) {
                $data = $this->fetchAll($this->query($sql));
                amct_cache::cache($cacheId, $data, 300);
            }
            return $data;
        } else {
            return $this->fetchAll($this->query($sql));
        }
    }

    public function getAllToKeySingle($sql, $keyFieldName, $valueFieldName) {
        $result = $this->query($sql);
        if ($result) {
            while ($row = mysql_fetch_assoc($result)) {
                $data[$row[$keyFieldName]] = $row[$valueFieldName];
            }
            $this->free_result($result);
            return $data;
        }else
            return null;
    }

    public function getOneFieldToArray($sql, $fieldName) {
        $result = $this->query($sql);
        if ($result) {
            while ($row = mysql_fetch_assoc($result)) {
                $data[] = $row[$fieldName];
            }
            $this->free_result($result);
            return $data;
        }else
            return null;
    }

    public function getAllToKeyByField($sql, $fieldName) {
        $result = $this->query($sql);
        if ($result) {
            while ($row = mysql_fetch_assoc($result)) {
                $data[$row[$fieldName]] = $row;
                unset($data[$row[$fieldName]][$fieldName]);
            }
            $this->free_result($result);
            return $data;
        }else
            return null;
    }

    public function getAllToKeyByFieldMuti($sql, $fieldName, $subFieldName) {
        $result = $this->query($sql);
        if ($result) {
            while ($row = mysql_fetch_assoc($result)) {
                $data[$row[$fieldName]][$row[$subFieldName]] = $row;
                unset($data[$row[$fieldName]][$fieldName]);
            }
            $this->free_result($result);
            return $data;
        }else
            return null;
    }

    public function Select($sql, $limit_row_offset = null, $limit_row_count = null, $rowid = false) {
        $limit_row_offset = intval($limit_row_offset);
        $limit_row_count = intval($limit_row_count);
        if (!strstr($sql, 'limit') && $limit_row_count) {
            $sql .= " LIMIT " . $limit_row_offset . "," . $limit_row_count;
        }
        if ($this->cache) {
            $cacheId = md5($sql);
            $data = amct_cache::cache($cacheId);
            if (!$data || $this->cache_refresh) {
                $data = $this->fetchAll($this->query($sql));
                amct_cache::cache($cacheId, $data, 300);
            }
            return $data;
        } else {
            return $this->fetchAll($this->query($sql));
        }
    }

    /**
     * 执行sql返回符合条件的第一条数据记录.
     *
     * @param $sql string
     * @return Ambigous <boolean, NULL, multitype:>
     */
    public function getOne($sql) {
        $sql = $this->safe_sql(func_num_args(), func_get_args());
        $sql_lower = strtolower($sql);
        if (substr($sql_lower, 0, 6) == 'select' && !strstr($sql_lower, 'limit')) {
            $sql .= ' limit 1';
        }
        return $this->fetch($this->query($sql));
    }

    /**
     * 执行插入的sql语句操作
     *
     * @param $sql string
     * @return boolean
     */
    public function insert($sql) {
        if (func_num_args() > 1)
            $sql = $this->safe_sql(func_num_args(), func_get_args());
        return $this->query($sql);
    }

    /**
     * 获得最后插入ID,须数据库支持
     *
     * @return int
     */
    public function last_insert_id() {
        return mysql_insert_id($this->link);
    }

    /**
     * 执行删除的sql语句操作
     *
     * @param $sql string
     * @return boolean
     */
    public function delete($sql) {
        if (func_num_args() > 1)
            $sql = $this->safe_sql(func_num_args(), func_get_args());
        return $this->query($sql);
    }

    /**
     * 执行更新的sql语句操作
     *
     * @param $sql string
     * @return boolean
     */
    public function update($sql) {
        if (func_num_args() > 1)
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
    public function insertArray($tablename, $arrData) {
        if (!$tablename || !is_array($arrData) || !count($arrData)) {
            return false;
        }
        $sql_field_names = $sql_field_values = null;
        foreach ($arrData as $fieldname => $fieldvalue) {
            $sql_field_names .= '`' . $fieldname . '`,';
            if (!is_string($fieldvalue))
                $sql_field_values .= $fieldvalue . ",";
            else
                $sql_field_values .= "'" . mysql_real_escape_string($fieldvalue) . "',";
        }
        $sql_field_names = substr($sql_field_names, 0, - 1);
        $sql_field_values = substr($sql_field_values, 0, - 1);
        $sql = "INSERT INTO `{$tablename}`({$sql_field_names}) VALUES({$sql_field_values})";
        return $this->insert($sql);
    }

    public function replaceArray($tablename, $arrData) {
        if (!$tablename || !is_array($arrData) || !count($arrData)) {
            return false;
        }
        $sql_field_names = $sql_field_values = null;
        foreach ($arrData as $fieldname => $fieldvalue) {
            $sql_field_names .= '`' . $fieldname . '`,';
            if (!is_string($fieldvalue))
                $sql_field_values .= $fieldvalue . ",";
            else
                $sql_field_values .= "'" . mysql_real_escape_string($fieldvalue) . "',";
        }
        $sql_field_names = substr($sql_field_names, 0, - 1);
        $sql_field_values = substr($sql_field_values, 0, - 1);
        $sql = "REPLACE INTO `{$tablename}`({$sql_field_names}) VALUES({$sql_field_values})";
        return $this->insert($sql);
    }

    public function replaceWhere($table, $arrData, $where) {
        if (!$where)
            return false;
        $sql = "SELECT * FROM `{$table}` WHERE " . $where;
        $rs = $this->getOne($sql);
        if ($rs) {
            return $this->updateWhere($table, $arrData, $where);
        } else {
            return $this->insertArray($table, $arrData);
        }
    }

    public function updateWhere($tablename, $arrData, $where = '') {
        if (!$tablename || !is_array($arrData) || !count($arrData)) {
            return false;
        }
        if ($where)
            $where = 'WHERE ' . $where;
        else
            $where = '';
        $sql_ext = null;
        foreach ($arrData as $fieldname => $fieldvalue) {
            if ($fieldvalue === null) {
                $sql_ext .= ($sql_ext ? "," : "") . "`{$fieldname}` = null";
            } else {
                if (is_string($fieldvalue))
                    $sql_ext .= ($sql_ext ? "," : "") . "`{$fieldname}` = '" . mysql_real_escape_string($fieldvalue) . "'";
                else
                    $sql_ext .= ($sql_ext ? "," : "") . "`{$fieldname}` = " . $fieldvalue;
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
     *        	表名
     * @param $arrData array
     * @param $filters array
     *        	条件语句
     * @return int 成功将返回更新的记录数
     */
    public function updateArray($tablename, $arrData, $filters = array()) {
        if (!$tablename || !is_array($arrData) || !count($arrData) || !is_array($filters) || !count($filters)) {
            return false;
        }
        if (count($filters))
            $where = $this->filters($filters);
        else
            $where = '';
        $sql_ext = null;
        foreach ($arrData as $fieldname => $fieldvalue) {
            if ($fieldvalue === null) {
                $sql_ext .= ($sql_ext ? "," : "") . "`{$fieldname}` = null";
            } else {
                if (is_string($fieldvalue))
                    $sql_ext .= ($sql_ext ? "," : "") . "`{$fieldname}` = '" . mysql_real_escape_string($fieldvalue) . "'";
                else
                    $sql_ext .= ($sql_ext ? "," : "") . "`{$fieldname}` = " . $fieldvalue;
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
    public function filters($filters) {
        $sql_where = '';
        if (is_array($filters)) {
            foreach ($filters as $f => $v) {
                $f_type = gettype($v);
                if ($f_type == 'array') {
                    $sql_where .= ($sql_where ? " AND " : "") . "(`{$f}` " . $v['operator'] . " '" . $v['value'] . "')";
                } else {
                    $sql_where .= ($sql_where ? " AND " : "") . "(`{$f}` = '{$v}')";
                }
            }
        } elseif (strlen($filters)) {
            $sql_where = $filters;
        }else
            return '';
        $sql_where = $sql_where ? " WHERE " . $sql_where : '';
        return $sql_where;
    }

    public function datagrid($countSql, $dataSql, $page, $pageSize) {
        $page = max(1, $page);
        if ($countSqlresult = $this->getOne($countSql))
            $result['recordCount'] = reset($countSqlresult);
        else
            $result['recordCount'] = 0;
        if (!$result['recordCount']) {
            $result['page'] = $page;
            $result['pageSize'] = $pageSize;
            $result['pageCount'] = 0;
            $result['data'] = null;
            $result['nextPage'] = $result['backPage'] = 0;
            return $result;
        }

        $result['pageCount'] = ceil($result['recordCount'] / $pageSize);
        //$page = min($result['pageCount'], $page);
        $offset = ($page - 1) * $pageSize;
        if ($page < $result['pageCount'])
            $result['nextPage'] = $page + 1;
        if ($page > 1)
            $result['backPage'] = $page - 1;
        $result['page'] = $page;
        $result['pageSize'] = $pageSize;
        $result['data'] = $this->getAll($dataSql . ' limit ' . $offset . ',' . $pageSize);
        $result['pageSize'] = min(count($result['data']), $pageSize);
        $result['paging'] = $this->paging($result['page'], $result['pageCount']);
        return $result;
    }

    public function paging($page, $pageCount, $pages = 10) {
        if ($pageCount <= 1)
            return null;
        $beginPage = $page - $pages > 0 ? $page - $pages : 1;
        $endPage = $beginPage + 8 < $pageCount ? $beginPage + 8 : $pageCount;
        if ($beginPage > 2)
            $data[] = 1;
        for ($i = $beginPage; $i <= $endPage; $i++) {
            $data[] = $i;
        }
        if ($endPage < $pageCount)
            $data[] = $pageCount;
        return $data;
    }

    public function safeSQL($sql) {
        $sql = $this->safe_sql(func_num_args(), func_get_args());
        return $sql;
    }

    private function safe_sql($numParams, $params) {
        if ($numParams > 1) {
            if ($numParams == 2 && is_array($params[1])) {
                $i = 1;
                foreach ($params[1] as $value) {
                    $params[$i++] = $value;
                }
            } else {
                for ($i = 1; $i < $numParams; $i++) {
                    $params[$i] = mysql_real_escape_string($params[$i]);
                }
            }
            $sql = call_user_func_array('sprintf', $params);
        } else {
            $sql = $params[0];
        }
        return $sql;
    }

}

