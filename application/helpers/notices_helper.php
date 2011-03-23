<?php

    if (!function_exists('set_notice'))
    {
        function set_notice($type, $message)
        {
            $CI =& get_instance();
            $CI->notices[] = array("type" => $type, "message" => $message);
        }
    }
