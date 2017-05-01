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
		$this->viewdata['controller_title'] = '<a href="'.site_url("admin/series").'">' . _("Series") . '</a>';;
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
		
		$data["post"] = $post;
	}


	function add_new($stub = "")
	{
		$this->viewdata["function_title"] = '<a href="#">'._("Add New").'</a>';

		//$stub stands for $comic, but there's already a $comic here
		if ($stub != "")
		{
			if ($this->input->post())
			{
				$chapter = new Post();
				if ($chapter->add($this->input->post()))
				{
					$subchapter = is_int($chapter->subchapter) ? $chapter->subchapter : 0;
					flash_notice('notice', sprintf(_('Chapter %s has been added to %s.'), $chapter->chapter.'.'.$subchapter, $chapter->comic->name));
					redirect('/admin/blog/post/' . $chapter->comic->stub . '/' . $chapter->id);
				}
			}
			$comic = new Post();
			$comic->where('stub', $stub)->get();
			$this->viewdata["extra_title"][] = _("Chapter in") . ' ' . $comic->name;

			$this->viewdata["main_content_view"] = $this->load->view("admin/form.php", $data, TRUE);
			$this->load->view("admin/default.php", $this->viewdata);
			return true;
		}
		else
		{
			$comic = new Post();
			if ($this->input->post())
			{
				if ($comic->add($this->input->post()))
				{
					$config['upload_path'] = 'content/cache/';
					$config['allowed_types'] = 'jpg|png|gif';
					$this->load->library('upload', $config);
					$field_name = "thumbnail";
					if (count($_FILES) > 0 && $this->upload->do_upload($field_name))
					{
						$up_data = $this->upload->data();
						if (!$this->files_model->comic_thumb($comic, $up_data))
						{
							log_message("error", "Controller: series.php/add_new: image failed being added to folder");
						}
						if (!unlink($up_data["full_path"]))
						{
							log_message('error', 'series.php/add_new: couldn\'t remove cache file ' . $data["full_path"]);
							return false;
						}
					}
					flash_notice('notice', sprintf(_('The series %s has been added.'), $comic->name));
					redirect('/admin/blog/post/' . $comic->stub);
				}
			}

			$table = ormer($comic);
			$table[] = array(
				_('Licensed in'),
				array(
					'name' => 'licensed',
					'type' => 'nation',
					'value' => array(),
					'help' => _('Insert the nations where the series is licensed in order to limit the availability.'),
				),
			);

			$custom_slug = array(array(
				_('Custom URL Slug'),
				array(
					'name' => 'has_custom_slug',
					'type' => 'checkbox',
					'text' => _('Has Custom URL Slug'),
					'help' => _('If you want to have a custom url slug or the comic\'s title is written with non-latin letters tick this.'),
					'class' => 'jqslugcb'
				)
			));
			array_splice($table, 2, 0, $custom_slug);

			$table = tabler($table, FALSE, TRUE);
			$data["form_title"] = _('Add New') . ' ' . _('Series');
			$data['table'] = $table;

			$this->viewdata["extra_title"][] = _("Series");
			$this->viewdata["extra_script"] = '<script type="text/javascript" src="'.base_url().'assets/js/form-extra.js"></script>';
			$this->viewdata["main_content_view"] = $this->load->view("admin/form.php", $data, TRUE);
			$this->load->view("admin/default.php", $this->viewdata);
		}
	}


	function upload()
	{
		$info = array();

		// compatibility for flash uploader and browser not supporting multiple upload
		if (is_array($_FILES['Filedata']) && !is_array($_FILES['Filedata']['tmp_name']))
		{
			$_FILES['Filedata']['tmp_name'] = array($_FILES['Filedata']['tmp_name']);
			$_FILES['Filedata']['name'] = array($_FILES['Filedata']['name']);
		}

		for ($file = 0; $file < count($_FILES['Filedata']['tmp_name']); $file++)
		{
			$valid = explode('|', 'png|zip|rar|gif|jpg|jpeg');
			if (!in_array(strtolower(substr($_FILES['Filedata']['name'][$file], -3)), $valid))
				continue;

			if (!in_array(strtolower(substr($_FILES['Filedata']['name'][$file], -3)), array('zip', 'rar')))
				$pages = $this->files_model->page($_FILES['Filedata']['tmp_name'][$file], $_FILES['Filedata']['name'][$file], $this->input->post('chapter_id'));
			else
				$pages = $this->files_model->compressed_chapter($_FILES['Filedata']['tmp_name'][$file], $_FILES['Filedata']['name'][$file], $this->input->post('chapter_id'));

			foreach ($pages as $page)
			{
				$info[] = array(
					'name' => $page->filename,
					'size' => $page->size,
					'url' => $page->page_url(),
					'thumbnail_url' => $page->page_url(TRUE),
					'delete_url' => site_url("admin/series/delete/page"),
					'delete_data' => $page->id,
					'delete_type' => 'POST'
				);
			}
		}

		// return a json array
		$this->output->set_output(json_encode($info));
		return true;
	}


	function get_file_objects()
	{
		// Generate JSON File Output (Required by jQuery File Upload)
		header('Content-type: application/json');
		header('Pragma: no-cache');
		header('Cache-Control: private, no-cache');
		header('Content-Disposition: inline; filename="files.json"');

		$id = $this->input->post('id');
		$chapter = new Chapter($id);
		$pages = $chapter->get_pages();
		$info = array();
		foreach ($pages as $page)
		{
			$info[] = array(
				'name' => $page['filename'],
				'size' => intval($page['size']),
				'url' => $page['url'],
				'thumbnail_url' => $page['thumb_url'],
				'delete_url' => site_url("admin/series/delete/page"),
				'delete_data' => $page['id'],
				'delete_type' => 'POST'
			);
		}

		$this->output->set_output(json_encode($info));
		return true;
	}


	function delete($type, $id = 0)
	{
		if (!isAjax())
		{
			$this->output->set_output(_('You can\'t delete chapters from outside the admin panel through this link.'));
			log_message("error", "Controller: series.php/remove: failed serie removal");
			return false;
		}
		$id = intval($id);

		switch ($type)
		{
			case("serie"):
				$comic = new Comic();
				$comic->where('id', $id)->get();
				$title = $comic->name;
				if (!$comic->remove())
				{
					flash_notice('error', sprintf(_('Failed to delete the series %s.'), $title));
					log_message("error", "Controller: series.php/remove: failed serie removal");
					$this->output->set_output(json_encode(array('href' => site_url("admin/series/manage"))));
					return false;
				}
				flash_notice('notice', 'The serie ' . $comic->name . ' has been removed');
				$this->output->set_output(json_encode(array('href' => site_url("admin/series/manage"))));
				break;
			case("chapter"):
				$chapter = new Chapter($id);
				$title = $chapter->chapter;
				if (!$comic = $chapter->remove())
				{
					flash_notice('error', sprintf(_('Failed to delete chapter %s.'), $chapter->comic->chapter));
					log_message("error", "Controller: series.php/remove: failed chapter removal");
					$this->output->set_output(json_encode(array('href' => site_url("admin/series/series/" . $comic->stub))));
					return false;
				}
				set_notice('notice', 'Chapter deleted.');
				$this->output->set_output(json_encode(array('href' => site_url("admin/series/serie/" . $comic->stub))));
				break;
			case("page"):
				$page = new Page($this->input->post('id'));
				$page->get_chapter();
				$page->chapter->get_comic();
				if (!$data = $page->remove_page())
				{
					log_message("error", "Controller: series.php/remove: failed page removal");
					return false;
				}
				$this->output->set_output(json_encode(array('href' => site_url("admin/series/serie/" . $page->chapter->comic->stub . "/" . $page->chapter->id))));
				break;
			case("allpages"):
				$chapter = new Chapter($id);
				$chapter->get_comic();
				if (!$chapter->remove_all_pages())
				{
					log_message("error", "Controller: series.php/remove: failed all pages removal");
					return false;
				}
				$this->output->set_output(json_encode(array('href' => site_url("admin/series/serie/" . $chapter->comic->stub . "/" . $chapter->id))));
				break;
		}
	}


	function import($stub)
	{
		if (!$this->tank_auth->is_admin())
			show_404();

		if (!$stub)
			show_404();

		$comic = new Comic();
		$comic->where('stub', $stub)->get();
		$data['comic'] = $comic;
		$this->viewdata["extra_title"][] = $comic->name;

		$archive[] = array(
			_("Absolute directory path to ZIP archive for the series") . ' ' . $comic->name,
			array(
				'type' => 'input',
				'name' => 'directory',
				'help' => sprintf(_('Insert the absolute directory path. This means from the lowest accessible directory. Example: %s'), '/var/www/backup/' . $comic->stub)
			)
		);

		$data['archive'] = tabler($archive, FALSE, TRUE, TRUE);

		$this->viewdata["function_title"] = _("Import");
		if ($this->input->post('directory'))
		{
			$data['directory'] = $this->input->post('directory');
			if (!is_dir($data['directory']))
			{
				set_notice('error', _('The directory you set does not exist.'));
				$this->viewdata["main_content_view"] = $this->load->view("admin/series/import", $data, TRUE);
				$this->load->view("admin/default.php", $this->viewdata);
				return FALSE;
			}
			$data['archives'] = $this->files_model->import_list($data);
			$this->viewdata["main_content_view"] = $this->load->view("admin/series/import_compressed_list", $data, TRUE);
			$this->load->view("admin/default.php", $this->viewdata);
			return TRUE;
		}

		if ($this->input->post('action') == 'execute')
		{
			$result = $this->files_model->import_compressed();
			if (isset($result['error']) && !$result['error'])
			{
				$this->output->set_output(json_encode($result));
				return FALSE;
			}
			else
			{
				$this->output->set_output(json_encode($result));
				return true;
			}
		}

		$this->viewdata["main_content_view"] = $this->load->view("admin/series/import", $data, TRUE);
		$this->load->view("admin/default.php", $this->viewdata);
	}


}
