<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

if (!function_exists('get_setting')) {

	function get_setting($option) {
		$CI = & get_instance();
		$array = $CI->fs_options;
		return $array[$option];
	}

}

function isAjax() {
	return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
	($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
}