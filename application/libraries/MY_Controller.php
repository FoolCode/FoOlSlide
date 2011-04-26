<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

     function __construct()
     {
        parent::__construct();
		
		if(!$this->session->userdata('nation'))
		{
			// If the user doesn't have a nation set, let's grab it
			//
			require_once("assets/geolite/GeoIP.php");
			$gi = geoip_open("assets/geolite/GeoIP.dat",GEOIP_STANDARD);
			$nation = geoip_country_code_by_addr($gi, $_SERVER['REMOTE_ADDR']);
			geoip_close($gi);
			$this->session->set_userdata('nation', $nation);
		}
		
     }
}