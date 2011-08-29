<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

if (!function_exists('set_notice'))
{
	/*
	 * Sets a notice in the currently loading page. Can be used for multiple notices
	 * Notice types: error, warn, notice
	 * 
	 * @author Woxxy
	 */
	function set_notice($type, $message)
	{
		$CI = & get_instance();
		$CI->notices[] = array("type" => $type, "message" => $message);
	}


}

if (!function_exists('flash_notice'))
{
	/*
	 * Sets a notice in the next loaded page. Can be used for multiple notices
	 * Notice types: error, warn, notice
	 * 
	 * @author Woxxy
	 */
	function flash_notice($type, $message)
	{
		$CI = & get_instance();
		$CI->flash_notice_data[] = array('type' => $type, 'message' => $message);
		$CI->session->set_flashdata('notices', $CI->flash_notice_data);
	}


}