<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	function __construct() {
		parent::__construct();
		if (file_exists(FCPATH . "config.php")) {
			$this->load->database();
			$this->load->library('session');
			$this->load->library('tank_auth');
			$this->load->library('datamapper');

			if (!$this->session->userdata('nation')) {
				// If the user doesn't have a nation set, let's grab it
				//
				require_once("assets/geolite/GeoIP.php");
				$gi = geoip_open("assets/geolite/GeoIP.dat", GEOIP_STANDARD);
				$remote_addr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR']: '127.0.0.1';
				$nation = geoip_country_code_by_addr($gi, $remote_addr);
				geoip_close($gi);
				$this->session->set_userdata('nation', $nation);
			}
			
			// loads variables from database for get_setting()
			load_settings();
		}
	}

}