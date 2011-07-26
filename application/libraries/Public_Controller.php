<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Public_Controller extends MY_Controller {

	public function __construct() {
		parent::__construct();
		
		// We need to set some theme stuff, so let's load the template system
		$this->load->library('template');
		// everything works via jQuery!
		$this->template->append_metadata('<script src="' . site_url() . 'assets/js/jquery.js"></script>');
		
		// Set theme by using the theme variable
		$this->template->set_theme((get_setting('fs_theme_dir')?get_setting('fs_theme_dir'):'default'));
		
		// mobile theme variant
		if ($this->agent->is_mobile()) {
			$this->reurl_mobile();
			
			// jquery mobile requirements
			$this->template->append_metadata('<script src="' . site_url() . 'assets/js/jquery.mobile.js"></script>');
			$this->template->append_metadata('<script src="' . site_url() . 'assets/js/jquery.mobile.plugins.js"></script>');
			$this->template->set_theme('mobile');
		}
	}

	/*
	 * jQuery mobile url slash fix
	 * 
	 * @author Woxxy
	 */
	public function reurl_mobile()
	{
		if (current_url() != current_url_real()) redirect('/reader/');
		if ($this->uri->segment(2) !== FALSE && !isAjax())
		redirect('/reader'.str_replace('/reader/', '#', $this->uri->uri_string()), 'refresh');
	}

}