<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Profile extends Account_Controller
{
	function __construct()
	{
		parent::__construct();
	}


	function index()
	{
		// get the data to save. low on security because the user can only save to himself from here
		if ($this->input->post())
		{
			$profile = new Profile($this->tank_auth->get_user_id());
			// use the from_array to be sure what's being inputted
			$profile->from_array($this->input->post(), array('display_name', 'twitter', 'bio'), TRUE);
		}

		$user = new User($this->tank_auth->get_user_id());
		$profile = new Profile($this->tank_auth->get_user_id());
		
		$data["user_id"] = $user->id;
		$data["user_name"] = $user->username;
		$data["user_display_name"] = $profile->display_name;
		$data["user_twitter"] = $profile->twitter;
		$data["user_bio"] = $profile->bio;
	}


}

