<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// adm/shop_admin/configupdate.php HOOK
add_event('shop_config_update', 'adm_shop_config_update', G5_HOOK_DEFAULT_PRIORITY, 1);
function adm_shop_config_update($post){

	global $g5;

	$check_sanitize_keys = array(
		'de_nice_mid', // NICEPAY MID
		'de_nice_key' // NICEPAY KEY
	);

	foreach( $check_sanitize_keys as $key ) :
		$$key = isset($post[$key]) ? clean_xss_tags($post[$key], 1, 1) : '';
	endforeach;

	$sql = " update `{$g5['g5_shop_default_table']}`
				set `de_nice_mid` = '{$post['de_nice_mid']}'
				,`de_nice_key` = '{$post['de_nice_key']}'
	";
	sql_query($sql);

}
