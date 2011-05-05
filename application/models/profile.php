<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Profile extends DataMapper {

	var $has_one = array('user');
	var $has_many = array();
	var $validation = array(
		'user_id' => array(
			'rules' => array(),
			'label' => 'User ID'
		),
		'group_id' => array(
			'rules' => array(),
			'label' => 'Group ID',			
		),
		'display_name' => array(
			'rules' => array(),
			'label' => 'Publicly displayed username',
			'type'	=> 'input'
		),
		'twitter' => array(
			'rules' => array(),
			'label' => 'Twitter username',
			'type'	=> 'input'
		),
		'bio' => array(
			'rules' => array(),
			'label' => 'Bio',
			'type'	=> 'textarea'
		)
	);
	
	function __construct($id = NULL) {		
		parent::__construct($id);
	}

	function post_model_init($from_cache = FALSE) {
		
	}
}