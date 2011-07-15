<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	function __construct() {
		parent::__construct();
		if (!file_exists(FCPATH . "config.php")) {
			show_error("If you are here, and have no clue why FoOlSlide is not working, start by reading the <a href='http://trac.foolrulez.com/foolslide/wiki/installation_guide'>installation manual</a>.");
		} else {
			$this->load->database();
			$this->load->library('session');
			$this->load->library('tank_auth');
			$this->load->library('datamapper');

			if (!$this->session->userdata('nation')) {
				// If the user doesn't have a nation set, let's grab it
				//
				require_once("assets/geolite/GeoIP.php");
				$gi = geoip_open("assets/geolite/GeoIP.dat", GEOIP_STANDARD);
				$remote_addr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
				$nation = geoip_country_code_by_addr($gi, $remote_addr);
				geoip_close($gi);
				$this->session->set_userdata('nation', $nation);
			}

			// loads variables from database for get_setting()
			load_settings();
			
			// This is the first chance we get to load the right translation file
			if (get_setting('fs_gen_lang')) {
				$locale = get_setting('fs_gen_lang');
				putenv("LANG=$locale");
				setlocale(LC_ALL, $locale);
				bindtextdomain("default", FCPATH . "assets/locale");
				textdomain("default");
			}

			$this->config->config['tank_auth']['allow_registration'] = !get_setting('fs_reg_disabled');

			$this->config->config['tank_auth']['email_activation'] = ((get_setting('fs_reg_email_disabled')) ? FALSE : TRUE);

			$captcha_public = get_setting('fs_reg_recaptcha_public');
			if ($captcha_public != "") {
				$captcha_secret = get_setting('fs_reg_recaptcha_secret');
				if ($captcha_secret != "") {
					$this->config->config['tank_auth']['use_recaptcha'] = TRUE;
					$this->config->config['tank_auth']['recaptcha_public_key'] = $captcha_public;
					$this->config->config['tank_auth']['recaptcha_secret_key'] = $captcha_secret;
				}
			}
		}
	}

}