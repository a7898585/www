<?php 
if(!$maxlength) $maxlength = 750;
$maxlength = min($maxlength, 750);
$fieldtype = $issystem ? 'CHAR' : 'VARCHAR';
$sql = "ALTER TABLE `$tablename` CHANGE `$field` `$field` $fieldtype( $maxlength ) NOT NULL DEFAULT '$defaultvalue'";
$db->query($sql);
?>