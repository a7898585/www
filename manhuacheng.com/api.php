<?php 
/**
 *  index.php API 入口
 *
 * @copyright			(C) 2005-2010 OLCMS
 * @license				http://www.olcms.cn/license/
 * @lastmodify			2010-7-26
 */
define('OLCMS_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
include OLCMS_PATH.'olcms/base.php';
$param = pc_base::load_sys_class('param');

$op = isset($_GET['op']) && trim($_GET['op']) ? trim($_GET['op']) : exit('Operation can not be empty');
if (!preg_match('/([^a-z_]+)/i',$op) && file_exists(OLCMS_PATH.'api/'.$op.'.php')) {
	include OLCMS_PATH.'api/'.$op.'.php';
} else {
	exit('API handler does not exist');
}
?>