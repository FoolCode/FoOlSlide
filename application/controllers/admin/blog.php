<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Blog extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();
		if (!($this->tank_auth->is_allowed()))
			redirect('account');

		// if this is a load balancer, let's not allow people in the series tab
		if (get_setting('fs_balancer_master_url'))
			redirect('/admin/members');

		$this->load->model('files_model');
		$this->load->library('pagination');
		$this->viewdata['controller_title'] = '<a href="'.site_url("admin/blog").'">' . _("Blog") . '</a>';;
	}


	function index()
	{
		redirect('/admin/blog/manage');
	}


	function manage($page = 1)
	{
		$this->viewdata["function_title"] = _('Manage');
        $posts= new Post();

		if ($this->input->post('search'))
		{
			$search = $this->input->post('search');
			$posts->ilike('name', $search)->limit(20);
			$this->viewdata["extra_title"][] = _('Searching') . ': ' . htmlspecialchars(($search));
		}

		$posts->order_by('name', 'ASC');
		$posts->get_paged_iterated($page, 20);
		$data["posts"] = $posts;

		$this->viewdata["main_content_view"] = $this->load->view("admin/blog/manage.php", $data, TRUE);
		$this->load->view("admin/default.php", $this->viewdata);
	}


	function post($stub = NULL)
	{
		$post = new Post();
		$post->where("stub", $stub)->get();

		if ($post->result_count() == 0)
		{
			set_notice('warn', _('Sorry, the post you are looking for does not exist.'));
			$this->manage();
			return false;
		}

		$this->viewdata["function_title"] = '<a href="' . site_url('/admin/blog/manage/') . '">' . _('Manage') . '</a>';
		if ($stub == "") $this->viewdata["extra_title"][] = $post->name;

		if ($this->input->post())
		{
			// Prepare for stub change in case we have to redirect instead of just printing the view
			$old_post_stub = $post->stub;
			$post->update_post_db($this->input->post());

			flash_notice('notice', sprintf(_('Updated series information for %s.'), $post->name));
			// Did we change the stub of the post? We need to redirect to the new page then.
			if (isset($old_post_stub) && $old_post_stub != $post->stub)
			{
				redirect('/admin/blog/post/' . $post->stub);
			}
		}

		$data["post"] = $post;

		$custom_slug = array(array(
			_('Custom URL Slug'),
			array(
				'name' => 'has_custom_slug',
				'type' => 'checkbox',
				'text' => _('Has Custom URL Slug'),
				'help' => _('If you want to have a custom url slug or the post\'s title is written with non-latin letters tick this.'),
				'class' => 'jqslugcb'
			)
		));

		$table = ormer($post);
		array_splice($table, 2, 0, $custom_slug);
		$table = tabler($table);
		$data['table'] = $table;
		
		$this->viewdata["extra_script"] = '<script type="text/javascript" src="'.base_url().'assets/js/form-extra.js"></script>';
		$this->viewdata["main_content_view"] = $this->load->view("admin/blog/post.php", $data, TRUE);
		$this->load->view("admin/default.php", $this->viewdata);
	}


	function add_new($stub = "")
	{
		$this->viewdata["function_title"] = '<a href="#">'._("Add New").'</a>';

		//$stub stands for $post, but there's already a $post here
		$post = new Post();
		if ($this->input->post())
		{
			if ($post->add($this->input->post()))
			{
				$config['upload_path'] = 'content/cache/';
				$config['allowed_types'] = 'jpg|png|gif';
				$this->load->library('upload', $config);
				$field_name = "thumbnail";

				flash_notice('notice', sprintf(_('The post %s has been added.'), $post->name));
				redirect('/admin/blog/post/' . $post->stub);
			}
		}

			$table = ormer($post);

			$custom_slug = array(array(
				_('Custom URL Slug'),
				array(
					'name' => 'has_custom_slug',
					'type' => 'checkbox',
					'text' => _('Has Custom URL Slug'),
					'help' => _('If you want to have a custom url slug or the post\'s title is written with non-latin letters tick this.'),
					'class' => 'jqslugcb'
				)
			));
			array_splice($table, 2, 0, $custom_slug);

			$table = tabler($table, FALSE, TRUE);
			$data["form_title"] = _('Add New') . ' ' . _('Post');
			$data['table'] = $table;

			$this->viewdata["extra_title"][] = _("Post");
			$this->viewdata["extra_script"] = '<script type="text/javascript" src="'.base_url().'assets/js/form-extra.js"></script>';
			$this->viewdata["main_content_view"] = $this->load->view("admin/form.php", $data, TRUE);
			$this->load->view("admin/default.php", $this->viewdata);
	}

	function delete($type, $id = 0)
	{
		if (!isAjax())
		{
			$this->output->set_output(_('You can\'t delete posts from outside the admin panel through this link.'));
			log_message("error", "Controller: post.php/remove: failed post removal");
			return false;
		}
		$id = intval($id);
		
		switch ($type)
		{
			case("post"):
				$post = new Post();
				$post->where('id', $id)->get();
				$title = $post->name;
				if (!$post->remove())
				{
					flash_notice('error', sprintf(_('Failed to delete the post %s.'), $title));
					log_message("error", "Controller: post.php/remove: failed post removal");
					$this->output->set_output(json_encode(array('href' => site_url("admin/blog/manage"))));
					return false;
				}
				flash_notice('notice', 'The post ' . $post->name . ' has been removed');
				$this->output->set_output(json_encode(array('href' => site_url("admin/blog/manage"))));
				break;
		}
	}

}
