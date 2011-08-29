<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

// it's not a draft of a controller... we're going to call the controller Draft
class Draft extends Team_Controller
{
	/**
	 * If we're here it means a team is selected and that 
	 * the current team's DataMapper object is in $this->teamc->team
	 * 
	 * No team-less people can be here, so don't worry about security that has
	 * been taken care of in the controller!
	 */
	function __construct()
	{
		parent::__construct();
		$this->viewdata['controller_title'] = _("Draft");
	}


	/**
	 * Draft index: shows general data on the currently selected draft
	 * 
	 * @param int $id draft id
	 */
	function index($id, $derp)
	{
		$this->output->set_output($id);
	}
	
	/**
	 * Translation and proofreading must be done on the same page
	 * 
	 * We have this just to initialize the page: GET, PUT and POST will be from the API
	 * 
	 * @param int $id draft id
	 */
	function script($id)
	{
		
	}

}