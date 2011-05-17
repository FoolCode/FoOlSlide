<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * Function to get single options from the preferences database
 * 
 * @param string $option the code of the option
 * @author Woxxy
 * @return string the option
 */
if (!function_exists('get_setting')) {

	function get_setting($option) {
		$CI = & get_instance();
		$array = $CI->fs_options;
		return $array[$option];
	}

}

/**
 * Caches in a variable and returns the home team's object
 * 
 * @author Woxxy
 * @return object home team
 */
if (!function_exists('get_home_team')) {

	function get_home_team() {
		$CI = & get_instance();
		if (isset($CI->fs_loaded->home_team))
			return $CI->fs_loaded->home_team;
		$hometeam = get_setting('fs_gen_default_team');
		$team = new Team();
		$team->where('name', $hometeam)->limit(1)->get();
		if ($team->result_count() < 1)
			return false;

		return $team;
	}

}

if (!function_exists('parse_irc')) {

	function parse_irc($string) {
		if (substr($string, 0, 1) == '#') {
			$echo = 'irc://';
			$at = strpos($string, '@');
			$echo .= substr($string, $at + 1);
			$echo .= '/' . substr($string, 1, $at - 1);
			return $echo;
		}
		return $string;
	}

}

/**
 * Checks that the call is made from Ajax
 * 
 * @author Woxxy
 * @return bool true if ajax request
 */
function isAjax() {
	return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
	($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
}

function current_url_real() {
	$pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
	}
	else {
		$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $s Size in pixels, defaults to 80px [ 1 - 512 ]
 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @param boole $img True to return a complete IMG tag False for just the URL
 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 * @source http://gravatar.com/site/implement/images/php/
 */
function get_gravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array()) {
	$url = 'http://www.gravatar.com/avatar/';
	$url .= md5(strtolower(trim($email)));
	$url .= "?s=$s&d=$d&r=$r";
	if ($img) {
		$url = '<img src="' . $url . '"';
		foreach ($atts as $key => $val)
			$url .= ' ' . $key . '="' . $val . '"';
		$url .= ' />';
	}
	return $url;
}

/**
 * Future function for load balancing the source of the images
 * 
 * @param string $string the url of the image
 * @return string the base url for the image server
 */
function balance_url($string = '') {
	return site_url($string);
}