<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

if($default['de_card_test'] || $default['de_nice_mid'] == "" || $default['de_nice_key'] == ""){												//테스트라면 또는 값이 없다면
	$default['de_nice_mid'] = "nicepay00m";
	$default['de_nice_key'] = "EYzu8jGGMfqaDEp76gSckuvnaHHu+bC4opsSN6lHv3b2lurNYkVXrZ7Z1AoqQnXI3eLuaUFyoRNC6FkrzVjceg==";
}

if( get_session('ss_personalpay_id') ) {
	$returnURL = G5_HTTPS_MSHOP_URL.'/personalpayformupdate.php';
}else {
	$returnURL = G5_HTTPS_MSHOP_URL.'/orderformupdate.php';
}
