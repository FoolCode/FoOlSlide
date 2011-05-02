<?php
class Migrate extends CI_Controller
{
	function __construct()
	{
		parent::Controller();
		
		$this->iontank_auth_auth->logged_in() or redirect('/admin/auth/login');
		$this->tank_auth->is_admin() or redirect('admin');
		$this->tank_auth->is_admin() or die(1);
		
		$this->load->library('migrations');

		$this->migrations->set_verbose(TRUE);

		/** VERY IMPORTANT - only turn this on when you need it. */
		show_error('Access to this controller is blocked, turn me on when you need me.');
	}

	// Install up to the most up-to-date version.
	function install()
	{
		if ( ! $this->migrations->install())
		{
			show_error($this->migrations->error);
			exit;
		}

		echo "<br />Migration Successful<br />";
	}

	// This will migrate up to the configed migration version
	function version($id = NULL)
	{
		// No $id supplied? Use the config version
		$id OR $id = $this->config->item('migrations_version');

		if ( ! $this->migrations->version($id))
		{
			show_error($this->migrations->error);
			exit;
		}

		echo "<br />Migration Successful<br />";
	}
}
