<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class License extends DataMapper {

	var $has_one = array('comic');
	var $has_many = array();
	var $validation = array(
		'comic_id' => array(
			'rules' => array(),
			'label' => 'Comic ID',
		),
		'nation' => array(
			'rules' => array(),
			'label' => 'nation',
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
