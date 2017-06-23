<?php
class amct_mysql_base {
	public static $connects = array();
	public static $config = array();
	public static function __close(){
		if(count(self::$connects)){
			foreach(self::$connects as $connectId => $link){
				if($link){
					if(self::$config[$connectId]['engine'] == 'mysql'){
						mysql_close(self::$connects[$connectId]);
						unset(self::$connects[$connectId]);
					}else{
						self::$connects[$connectId]->close();
						unset(self::$connects[$connectId]);
					}
				}
			}
		}
	}
}