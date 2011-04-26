<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Preferences extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
                $this->ion_auth->logged_in() or redirect('auth/login');
                $this->ion_auth->is_admin() or redirect('admin');
                $this->ion_auth->is_admin() or die(1);
                $this->load->library('form_validation');
                $this->viewdata['controller_title'] = "Preferences";
        }
        
		function authenticate()
		{}
		
		function generate_key()
		{}
		
		function generate_secret()
		{}
		
		function check_server_available()
		{}
		
		function send_files()
		{}
		
		function receive_files()
		{}
		
		function compare_files()
		{}
		
		function list_chapters()
		{}
}