<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Public_Controller extends MY_Controller {

	public function __construct() {
		parent::__construct();
		
		$this->load->library('template');
		$this->template->append_metadata('<script src="' . site_url() . 'assets/js/jquery.js"></script>');
		$this->template->set_theme('default');
		if ($this->agent->is_mobile()) {
			$this->reurl_mobile();
			$this->template->append_metadata('<script src="' . site_url() . 'assets/js/jquery.mobile.js"></script>');
			$this->template->append_metadata('<script src="' . site_url() . 'assets/js/jquery.mobile.plugins.js"></script>');
			$this->template->set_theme('mobile');
		}
	}

	public function reurl_mobile()
	{
		if (current_url() != current_url_real()) redirect('/reader/');
		if ($this->uri->segment(2) !== FALSE && !isAjax())
		redirect('/reader'.str_replace('/reader/', '#', $this->uri->uri_string()), 'refresh');
	}

}