<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class User_profile extends DataMapper {

	var $has_one = array('user_profile');
	var $has_many = array();
	var $validation = array(
		'user_id' => array(
			'rules' => array(),
			'label' => 'User ID'
		),
		'group_id' => array(
			'rules' => array(),
			'label' => 'Group ID'
		)
	);
	
	function __construct($id = NULL) {		
		parent::__construct($id);
	}

	function post_model_init($from_cache = FALSE) {
		
	}
}