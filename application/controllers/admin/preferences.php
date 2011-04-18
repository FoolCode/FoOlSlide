<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
        
        function index()
        {
            redirect('/admin/preferences/general');
        }
        
        
        function general()
        {
            $this->viewdata["function_title"] = "General";
            
            $this->viewdata["main_content_view"] = $this->load->view("admin/preferences/general.php", NULL, TRUE);
            $this->load->view("admin/default.php", $this->viewdata);
        }
        
        function submit()
        {
            $post = $this->input->post();
            var_dump($this->input->post());
            $goto = $post['goto'];
            unset($post['goto']); /*
            foreach($post as $key => $item)
            {
                $this->update('preferences', array('value' => $value), array('name', $name));
            }
            //redirect($goto);
            */
        }
        
        
}