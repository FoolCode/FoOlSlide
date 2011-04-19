<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function load_defaults()
{
    $CI =& get_instance();
    $array = $CI->db->get('preferences')->result_array();
	$result = array();
    foreach($array as $item)
	{
		$result[$item['name']] = $item['value'];
	}
	$CI->fs_options = $result;
    //var_dump($CI->fs_options);
    
}