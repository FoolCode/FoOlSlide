<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Public_Controller extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		// if this is a load balancer FoOlSlide, disable the public interface
		if (get_setting('fs_balancer_master_url'))
		{
			show_404();
		}

		// We need to set some theme stuff, so let's load the template system
		$this->load->library('template');

		// Set theme by using the theme variable
		$this->template->set_theme((get_setting('fs_theme_dir') ? get_setting('fs_theme_dir') : 'default'));
	}

}
