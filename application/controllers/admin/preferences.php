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
            
            
            $form = array();


            $form[] = array(
                'Site title',
                array(
                     'type'        => 'input',
                     'name'        => 'fs_gen_site_title',
                     'id'          => 'site_title',
                     'maxlength'   => '200',
                     'placeholder' => 'manga reader'
                )
            );

            $form[] = array(
                'Back URL',
                array(
                     'type'        => 'input',
                     'name'        => 'fs_gen_back_url',
                     'id'          => 'back_url',
                     'maxlength'   => '200',
                     'placeholder' => 'http://'
                )
            );

            $form[] = array(
                'Default team',
                array(
                     'type'        => 'input',
                     'name'        => 'fs_gen_default_team',
                     'id'          => 'default_team',
                     'maxlength'   => '200',
                     'placeholder' => 'Anonymous'
                )
            );

            $form[] = array(
                'Show Anonymous as team?',
                array(
                     'type'        => 'checkbox',
                     'name'        => 'fs_gen_anon_team_show',
                     'id'          => 'anon_team_show',
                     'placeholder' => ''
                )
            );

            $form[] = array(
                "",
                array(
                     'type'        => 'submit',
                     'name'        => 'submit',
                     'id'          => 'submit',
                     'value' => 'Save'
                )
            );
            
            if($post = $this->input->post())
            {
                $this->_submit($post);
            }
            
            $table = tabler($form, FALSE);

            $data['table'] = $table;
            
            
            $this->viewdata["main_content_view"] = $this->load->view("admin/preferences/general.php", $data, TRUE);
            $this->load->view("admin/default.php", $this->viewdata);
        }
        
        function _submit($post)
        {
            foreach($post as $key => $item)
            {
                $this->db->update('preferences', array('value' => $item), array('name' => $key));
            }
        }
        
        
}