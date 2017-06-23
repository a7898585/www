<?php
defined ( 'IN_OLCMS' ) or exit ( 'No permission resources.' );
/**
 * 点击统计
 */
$action = (isset ( $_GET ['action'] ) && ! empty ( $_GET ['action'] )) ? trim ( $_GET ['action'] ) : 'show';
$db = '';
if ($action == 'show') {
	$db = pc_base::load_model ( 'hits_model' );
	if ($_GET ['modelid'] && $_GET ['id']) {
		$modelid = intval ( $_GET ['modelid'] );
		$id = intval ( $_GET ['id'] );
		$type = intval ( $_GET ['type'] );
		$contentdb = pc_base::load_model ( 'content_model' );
		$contentdb->set_model ( $modelid );
		$delayviewcount = pc_base::load_config ( 'system', 'delayviewcount' );
		if ($delayviewcount == 1) {
			$logfile = './caches/caches_content/caches_data/' . $modelid . '.log';
			if (substr ( SYS_TIME, - 2 ) == '00') {
				$viewlog = $viewarray = array ();
				$newlog = OLCMS_PATH . $logfile . random ( 6 );
				if (@rename ( OLCMS_PATH . $logfile, $newlog )) {
					$viewlog = file ( $newlog );
					@unlink ( $newlog );
					if (is_array ( $viewlog ) && ! empty ( $viewlog )) {
						$viewlog = array_count_values ( $viewlog );
						foreach ( $viewlog as $contentid => $views ) {
							$viewarray [$views] .= ($contentid > 0) ? ',' . intval ( $contentid ) : '';
						}
						foreach ( $viewarray as $views => $ids ) {
							$contentdb->update ( array ('views' => '+=' . $views ), 'id IN (0' . $ids . ')' );
						}
					}
				}
			}
			if (@$fp = fopen ( OLCMS_PATH . $logfile, 'a' )) {
				fwrite ( $fp, "$id\n" );
				fclose ( $fp );
			}
		} else {
			$contentdb->update ( array ('views' => '+=1' ), array ('id' => $id ) );
		}
		$model_arr = getcache ( 'model', 'commons' );
		//生成静态网页时在页面实时更新统计,ps:type=0:动态页,1:静态页,2外部链接,3内容生成详细统计,4静态页详细统计
		if ($type == 1 || $type == 4) {
			$viewss = $contentdb->get_one ( array ('id' => $id ), 'views' );
			
			echo '$("#hits").html(' . $viewss ['views'] . ');';
		} elseif ($model_arr [$modelid] ['tablename'] == 'download') {
			$hitsid = 'c-' . $modelid . '-' . $id;
			$r = get_count ( $hitsid );
			if (! $r)
				exit ();
			extract ( $r );
			echo "\$('#todaydowns').html('$dayviews');";
			echo "\$('#weekdowns').html('$weekviews');";
			echo "\$('#monthdowns').html('$monthviews');";
		}
		//内容生成昨天，每天，没月统计
		if ($type == 3 || $type == 4) {
			$hitsid = 'c-' . $modelid . '-' . intval ( $_GET ['id'] );
			hits ( $hitsid );
		}
	} elseif ($_GET ['module'] && $_GET ['id']) {
		$module = $_GET ['module'];
		if ((preg_match ( '/([^a-z0-9_\-]+)/i', $module )))
			exit ( '1' );
		$hitsid = $module . '-' . intval ( $_GET ['id'] );
		$r = get_count ( $hitsid );
		if (! $r)
			exit ();
		extract ( $r );
		hits ( $hitsid );
		echo '$("#hits").html(' . $views . ');';
		
	}
} elseif ($action == 'tag' && $_GET ['id']) {
	$id = intval ( $_GET ['id'] );
	$db = pc_base::load_model ( 'tags_model' );
	$delayviewcount = pc_base::load_config ( 'system', 'delayviewcount' );
	if ($delayviewcount == 1) {
		$logfile = './caches/caches_content/caches_data/tags.log';
		if (substr ( SYS_TIME, - 2 ) == '55') {
			$viewlog = $viewarray = array ();
			$newlog = OLCMS_PATH . $logfile . random ( 6 );
			if (@rename ( OLCMS_PATH . $logfile, $newlog )) {
				$viewlog = file ( $newlog );
				unlink ( $newlog );
				if (is_array ( $viewlog ) && ! empty ( $viewlog )) {
					$viewlog = array_count_values ( $viewlog );
					foreach ( $viewlog as $contentid => $views ) {
						$viewarray [$views] .= ($contentid > 0) ? ',' . intval ( $contentid ) : '';
					}
					foreach ( $viewarray as $views => $ids ) {
						$db->update ( array ('hits' => '+=' . $views ), 'id IN (0' . $ids . ')' );
					}
				}
			}
		}
		if (@$fp = fopen ( OLCMS_PATH . $logfile, 'a' )) {
			fwrite ( $fp, "$id\n" );
			fclose ( $fp );
		}
	} else {
		$db->update ( array ('hits' => '+=1' ), array ('id' => $id ) );
	}
	exit ();
} else {
	exit ();
}
/**
 * 获取点击数量
 * @param $hitsid
 */
function get_count($hitsid) {
	global $db;
	$r = $db->get_one ( array ('hitsid' => $hitsid ) );
	if (! $r)
		return false;
	return $r;
}
/**
 * 点击次数统计
 * @param $contentid
 */
function hits($hitsid) {
	global $db;
	$r = $db->get_one ( array ('hitsid' => $hitsid ) );
	if (! $r)
		return false;
	$views = $r ['views'] + 1;
	$yesterdayviews = (date ( 'Ymd', $r ['updatetime'] ) == date ( 'Ymd', strtotime ( '-1 day' ) )) ? $r ['dayviews'] : $r ['yesterdayviews'];
	$dayviews = (date ( 'Ymd', $r ['updatetime'] ) == date ( 'Ymd', SYS_TIME )) ? ($r ['dayviews'] + 1) : 1;
	$weekviews = (date ( 'YW', $r ['updatetime'] ) == date ( 'YW', SYS_TIME )) ? ($r ['weekviews'] + 1) : 1;
	$monthviews = (date ( 'Ym', $r ['updatetime'] ) == date ( 'Ym', SYS_TIME )) ? ($r ['monthviews'] + 1) : 1;
	$sql = array ('views' => $views, 'yesterdayviews' => $yesterdayviews, 'dayviews' => $dayviews, 'weekviews' => $weekviews, 'monthviews' => $monthviews, 'updatetime' => SYS_TIME );
	return $db->update ( $sql, array ('hitsid' => $hitsid ) );
}

?>
