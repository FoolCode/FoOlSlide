<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
                $this->ion_auth->logged_in() or redirect('auth/login');
                $this->load->library('pagination');
                $this->viewdata['controller_title'] = 'Dashboard';
        }

	function index()
	{
            $this->viewdata['main_content_view'] = $this->load->view('admin/body', NULL, TRUE);
            $this->load->view("admin/default", $this->viewdata);
        }

}

/* End of file index.php */
/* Location: ./application/controllers/admin/index.php */