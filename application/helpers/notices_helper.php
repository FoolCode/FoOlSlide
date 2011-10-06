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
	function set_notice($type, $message, $data = FALSE)
	{
		if ($type == 'warn')
			$type = 'warning';
		if ($type == 'notice')
			$type = 'success';
		
		$CI = & get_instance();
		$CI->notices[] = array("type" => $type, "message" => $message, "data" => $data);
		
		if($CI->input->is_cli_request())
		{
			echo '['.$type.'] '.$message.PHP_EOL;
		}
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
		if ($type == 'warn')
			$type = 'warning';
		if ($type == 'notice')
			$type = 'success';
		
		$CI = & get_instance();
		$CI->flash_notice_data[] = array('type' => $type, 'message' => $message);
		$CI->session->set_flashdata('notices', $CI->flash_notice_data);
	}


}