<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Index extends Account_Controller
{
	function __construct()
	{
		parent::__construct();
		if (!$this->tank_auth->is_logged_in())
			redirect('/account/auth/login/');
		if($this->uri->segment(2) == 'index')
				redirect('/account/'.$this->uri->segment(3));
		$this->load->library('form_validation');
		$this->_navbar();
	}
	
	function _navbar() {
		$echo = "";
		$array = array(
			'profile' => _('Profile'),
			'teams' => _('Teams'),
		);
		
		foreach($array as $key => $item)
		{
			$echo .= '<a href="'.site_url('/account/'.$key.'/').'"';
			if ($this->uri->segment(2) == $key) $echo .= ' class="active" ';
			$echo .= '>'.$item.'</a>';
		}
		$this->viewdata["navbar"] = $echo;
	}


	function index()
	{
		redirect('/account/profile/');
	}


	function profile()
	{
		// get the data to save. low on security because the user can only save to himself from here
		if ($this->input->post())
		{
			$this->form_validation->set_rules('display_name', _('Display Name'), 'trim|max_length[30]|xss_clean');
			$this->form_validation->set_rules('twitter', _('Twitter username'), 'trim|max_length[20]|xss_clean');
			$this->form_validation->set_rules('bio', _('Bio'), 'trim|max_length[140]|xss_clean');

			if ($this->form_validation->run())
			{
				$profile = new Profile($this->tank_auth->get_user_id());
				// use the from_array to be sure what's being inputted
				$profile->display_name = $this->form_validation->set_value('display_name');
				$profile->twitter = $this->form_validation->set_value('twitter');
				$profile->bio = $this->form_validation->set_value('bio');
				$profile->save();
			}
		}
		$user = new User($this->tank_auth->get_user_id());
		$profile = new Profile($this->tank_auth->get_user_id());

		$data["user_id"] = $user->id;
		$data["user_name"] = $user->username;
		$data["user_email"] = $user->email;
		$data["user_display_name"] = $profile->display_name;
		$data["user_twitter"] = $profile->twitter;
		$data["user_bio"] = $profile->bio;

		$this->viewdata["function_title"] = _("Your profile");
		$this->viewdata["main_content_view"] = $this->load->view('account/profile/profile', $data, TRUE);
		$this->load->view("account/default.php", $this->viewdata);
	}
	
	function teams()
	{
		// this is a datamapper object
		$teams = $this->tank_auth->is_team();
		$data["teams"] = $teams->all_to_array(array('name', 'stub'));
		
		$this->viewdata["function_title"] = _("Your teams");
		$this->viewdata["main_content_view"] = $this->load->view('account/profile/teams', $data, TRUE);
		$this->load->view("account/default.php", $this->viewdata);
	}


}

/* End of file index.php */
/* Location: ./application/controllers/admin/index.php */