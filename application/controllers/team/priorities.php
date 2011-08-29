<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Priorities extends Team_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->viewdata['controller_title'] = _("Series");
	}


	function index()
	{
		echo 'This is the priorities system';
	}

}