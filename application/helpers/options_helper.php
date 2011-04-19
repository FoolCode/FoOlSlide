<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('get_setting'))
{
    function get_setting($option)
    {
		$CI =& get_instance();
		$array = $CI->fs_options;
		return $array[$option]; 
    }
}