<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Public_Controller extends MY_Controller {

        public function __construct()
	{
            parent::__construct();
            $this->load->library('template');
            $this->template->append_metadata('<script src="assets/js/jquery.js"></script>');
	    $this->template->set_theme('default');
            }
        
}