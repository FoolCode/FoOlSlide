<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');


class Chapter extends DataMapper {

	
	var $has_one = array('comic');

	var $has_many = array();

	
	
	var $validation = array(
		'name' => array(
			'rules' => array('max_length' => 256),
			'label' => 'Name',
			'type' => 'input'
		),
		'comic_id' => array(
			'rules' => array('is_int', 'required', 'max_length' => 256),
			'label' => 'Comic ID',
			'type' => 'hidden'
		),
		'team_id' => array(
			'rules' => array('is_int', 'max_length' => 256),
			'label' => 'Team ID'
		),
		'joint_id' => array(
			'rules' => array('is_int', 'max_length' => 256),
			'label' => 'Joint ID'
		),
		'stub' => array(
			'rules' => array('stub', 'required', 'max_length' => 256),
			'label' => 'Stub'
		),
		'chapter' => array(
			'rules' => array('is_int', 'required'),
			'label' => 'Chapter number',
			'type' => 'input',
			'placeholder' => 'required'
		),
		'subchapter' => array(
			'rules' => array('is_int'),
			'label' => 'Subchapter number',
			'type' => 'input'
		),
		'volume' => array(
			'rules' => array('is_int'),
			'label' => 'Volume number',
			'type' => 'input'
		),
		'language' => array(
			'rules' => array(),
			'label' => 'Language',
			'type' => 'language'
		),
		'uniqid' => array(
			'rules' => array('required', 'max_length' => 256),
			'label' => 'Uniqid'
		),
		'hidden' => array(
			'rules' => array('is_int'),
			'label' => 'Hidden',
			'type' => 'checkbox'
		),
		'description' => array(
			'rules' => array(),
			'label' => 'Description',
			'type' => 'textarea'
		),
		'thumbnail' => array(
			'rules' => array('max_length' => 512),
			'label' => 'Thumbnail'
		),
		'lastseen' => array(
			'rules' => array(),
			'label' => 'Lastseen'
		),
		'creator' => array(
			'rules' => array('required'),
			'label' => 'Creator'
		),
		'editor' => array(
			'rules' => array('required'),
			'label' => 'Editor'
		)
	);

	
	
	function __construct($id = NULL) {
		parent::__construct($id);
	}
	
	function post_model_init($from_cache = FALSE) {
		
	}
}

/* End of file license.php */
/* Location: ./application/models/license.php */
