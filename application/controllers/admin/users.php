<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
                $this->ion_auth->logged_in() or redirect('auth/login');
                $this->ion_auth->is_admin() or redirect('admin');
                $this->ion_auth->is_admin() or die(1);
                $this->viewdata['controller_title'] = "Users";
        }
        
        function index()
        {
            redirect('/admin/users/users');
        }
        
        function usersa()
        {
            $this->viewdata["function_title"] = "User list";
            $data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            $data['users'] = $this->ion_auth->get_users_array();
            $this->viewdata["main_content_view"] = $this->load->view('auth/index', $data, TRUE);
            $this->load->view("admin/default", $this->viewdata);
        }
		
		function user($id)
		{
			if($post = $this->input->post())
			{
				unset($post['submit']);
				//print_r($post);
				$this->ion_auth->update_user($id, $post);
			}
			
            $data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            $user = $this->ion_auth->get_user_array($id);
			$this->viewdata["function_title"] = "User: ".$user['username'];
			
			$form = array();
					
			$form[] = array('Username', 
				array(
					'value' => $user['username'],
					'type' => 'input',
					'name' => 'username'
				)
			);
			$form[] = array('Email', 
				array(
					'value' => $user['email'],
					'type' => 'input',
					'name' => 'email'
				)
			);
			$form[] = array('Reset password', array(
                     'type'        => 'checkbox',
                     'name'        => 'reset_password',
					 'display'	=> 'hidden'
                )
			);
			
			$groups = $this->ion_auth->get_groups();
			$options = array();
			foreach($groups as $item)
			{
				$options[$item->id] = $item->description;
			}
			
			
			
			$data['dropdown'] = form_dropdown('group_id', $options, $user['group_id']);
			
			$data['table'] = tabler($form, TRUE, TRUE);

            $this->viewdata["main_content_view"] = $this->load->view('auth/edit_user', $data, TRUE);
            $this->load->view("admin/default", $this->viewdata);
		}
		
		function you()
		{
			$user = $this->ion_auth->get_user();
			redirect('/admin/users/user/'.$user->id);
		}
        
        function teams($stub = "")
        {
			if($post = $this->input->post())
			{
				$team = new Team();
				$team->where('stub', $stub)->get();
				$post["id"] = $team->id;
				$team->update_team($this->input->post());
			}
			
			if($stub == "")
			{
				$this->viewdata["function_title"] = "Team list";
				$teams = new Team();
				$teams->get_iterated();
				$rows = array();
				foreach($teams as $team)
				{
					$rows[] = array('title' => '<a href="'.site_url('admin/users/teams/'.$team->stub).'">'.$team->name.'</a>');
				}
				$data['list'] = lister($rows);
				$this->viewdata["main_content_view"] = $this->load->view('admin/users/users', $data, TRUE);
				$this->load->view("admin/default", $this->viewdata);
			}
			else 
			{
				$team = new Team();
				$team->where('stub', $stub)->get();
				$this->viewdata["function_title"] = "Team";
				$this->viewdata["extra_title"][] = $team->name;

				$result = ormer($team);
				$result = tabler($result, TRUE, TRUE);
				$data['table'] = $result;
				$this->viewdata["main_content_view"] = $this->load->view('admin/form', $data, TRUE);
				$this->load->view("admin/default", $this->viewdata);
				
			}
            
        }
        
        function home_team()
        {
			$team = new Team();
			$team->where('name', get_setting('fs_gen_default_team'))->get();
			redirect('/admin/users/teams/'.$team->stub);
        }
		
		function add_team()
		{
			
			if($post = $this->input->post())
			{
				$team = new Team();
				$team->update_team($this->input->post());
				redirect('/admin/users/teams/'.$team->stub);
			}
			
			$team = new Team();

			$this->viewdata["function_title"] = "Team";
			$this->viewdata["extra_title"][] = 'New';

			$result = ormer($team);
			$result = tabler($result, FALSE, TRUE);
			$data['table'] = $result;
			$this->viewdata["main_content_view"] = $this->load->view('admin/form', $data, TRUE);
			$this->load->view("admin/default", $this->viewdata);
		}
        
}