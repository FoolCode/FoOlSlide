<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Preferences extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
                $this->tank_auth->is_logged_in() or redirect('/admin/auth/login');
                $this->tank_auth->is_admin() or redirect('admin');
                $this->tank_auth->is_admin() or die(1);
                $this->viewdata['controller_title'] = "Load balancer";
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