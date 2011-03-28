<?php

    if (!function_exists('set_notice'))
    {
        function set_notice($type, $message)
        {
            $CI =& get_instance();
            $CI->notices[] = array("type" => $type, "message" => $message);
        }
    }
    
    if (!function_exists('flash_notice'))
    {
        function flash_notice($type, $message)
        {
            $CI =& get_instance();
            $CI->flash_notice_data[] = array('type'=> $type, 'message' => $message);
            $CI->session->set_flashdata('notices', $CI->flash_notice_data);
        }
    }

    if (!function_exists('is_logged'))
    {
        function logged_in()
        {
            $CI =& get_instance();
            return $CI->ion_auth->logged_in();
        }
    }