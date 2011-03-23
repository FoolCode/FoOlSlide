<?php

class tools
{
        public function rule_stub($object, $field, $param = '')
        {
            $object->$field = strtolower(str_replace(" ", "_", $object->$field));
            $object->$field = preg_replace('/[^a-z0-9_]/i', '', $object->$field);
        }

        public function logged_id()
        {
            $CI =& get_instance();
            return $CI->ion_auth->get_user()->id;
        }
}