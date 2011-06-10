<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Comics extends Admin_Controller {

	function __construct() {
		parent::__construct();
		$this->tank_auth->is_logged_in() or redirect('/admin/auth/login');
		if (!($this->tank_auth->is_allowed()))
			redirect('admin');
		$this->load->model('files_model');
		$this->load->library('pagination');
		$this->viewdata['controller_title'] = _("Comics");
	}

	function index() {
		redirect('/admin/comics/manage');
	}

	function manage($page = 1) {
		$this->viewdata["function_title"] = '<a href="' . site_url('/admin/comics/manage/') . '">' . _('manage') . '</a>';
		$comics = new Comic();

		if ($this->input->post('search')) {
			$search = $this->input->post('search');
			$comics->ilike('name', $search)->limit(20);
			$this->viewdata["extra_title"][] = _('Searching') . ': ' . htmlspecialchars(($search));
		}

		$comics->order_by('name', 'ASC');
		$comics->get_paged_iterated($page, 20);
		$data["comics"] = $comics;

		$this->viewdata["main_content_view"] = $this->load->view("admin/comics/manage.php", $data, TRUE);
		$this->load->view("admin/default.php", $this->viewdata);
	}

	function comic($stub = NULL, $chapter_id = "") {
		$comic = new Comic();
		$comic->where("stub", $stub)->get();
		if ($comic->result_count() == 0) {
			set_notice('warn', _('The comic you looked for doesn\'t exist.'));
			$this->manage();
			return false;
		}

		$this->viewdata["function_title"] = '<a href="' . site_url('admin/comics/comic') . '/' . $comic->stub . '">' . $comic->name . '</a>';
		$data["comic"] = $comic;

		if ($chapter_id != "") {
			if ($this->input->post()) {
				$chapter = new Chapter();
				$chapter->update_chapter_db($this->input->post());
			}

			$chapter = new Chapter($chapter_id);
			$data["chapter"] = $chapter;

			$team = new Team();
			$teams = $team->get_teams($chapter->team_id, $chapter->joint_id);

			$table = ormer($chapter);

			$table[] = array(
				_('Teams'),
				array(
					'name' => 'team',
					'type' => 'input',
					'value' => $teams,
					'help' => _('Insert the names of the teams who worked on this chapter.')
				)
			);

			$table = tabler($table);

			$data["table"] = $table;


			$this->viewdata["extra_title"][] = (($chapter->name != "") ? $chapter->name : $chapter->chapter . "." . $chapter->subchapter);


			$data["pages"] = $chapter->get_pages();

			$this->viewdata["main_content_view"] = $this->load->view("admin/comics/chapter.php", $data, TRUE);
			$this->load->view("admin/default.php", $this->viewdata);
			return true;
		}

		if ($this->input->post()) {
			// Prepare for stub change in case we have to redirect instead of just printing the view
			$old_comic_stub = $comic->stub;
			$comic->update_comic_db($this->input->post());

			$config['upload_path'] = 'content/cache/';
			$config['allowed_types'] = 'jpg|png|gif';
			$this->load->library('upload', $config);
			$field_name = "thumbnail";
			if (count($_FILES) > 0 && $this->upload->do_upload($field_name)) {
				$up_data = $this->upload->data();
				if (!$this->files_model->comic_thumb($comic, $up_data)) {
					log_message("error", "Controller: comics.php/comic: image failed being added to folder");
				}
				if (!unlink($up_data["full_path"])) {
					log_message('error', 'comics.php/comic: couldn\'t remove cache file ' . $data["full_path"]);
					return false;
				}
			}
			
			// Did we change the stub of the comic? We need to redirect to the new page then.
			if (isset($old_comic_stub) && $old_comic_stub != $comic->stub) {
				redirect('/admin/comics/comic/' . $comic->stub);
			}
		}

		$chapters = new Chapter();
		$chapters->where('comic_id', $comic->id)->include_related('team')
				->order_by('chapter', 'DESC')->order_by('subchapter', 'DESC')->get();
		foreach ($chapters->all as $key => $item) {
			if ($item->joint_id > 0) {
				$teams = new Team();
				$jointers = $teams->get_teams(0, $item->joint_id);
				$item->jointers = $jointers;
				unset($jointers);
				unset($teams);
			}
		}

		$data["chapters"] = $chapters;

		if ($comic->get_thumb())
			$comic->thumbnail = $comic->get_thumb();

		$table = ormer($comic);

		$licenses = new License();

		$table[] = array(
			_('Licensed in'),
			array(
				'name' => 'licensed',
				'type' => 'nation',
				'value' => $licenses->get_by_comic($comic->id),
				'help' => _('Insert the nations where the comic is licensed in order to limit the availability.')
			)
		);

		$table = tabler($table);
		$data['table'] = $table;

		$this->viewdata["main_content_view"] = $this->load->view("admin/comics/comic.php", $data, TRUE);
		$this->load->view("admin/default.php", $this->viewdata);
	}

	function add_new($stub = "") {
		$this->viewdata["function_title"] = _("Add new");

		//$stub stands for $comic, but there's already a $comic here
		if ($stub != "") {
			if ($this->input->post()) {
				$chapter = new Chapter();
				if ($chapter->add($this->input->post())) {
					redirect('/admin/comics/comic/' . $chapter->comic->stub . '/' . $chapter->id);
				}
			}
			$comic = new Comic();
			$comic->where('stub', $stub)->get();
			$this->viewdata["extra_title"][] = _("Chapter in") . ' ' . $comic->name;
			$chapter = new Chapter();
			$chapter->comic_id = $comic->id;

			$table = ormer($chapter);

			$table[] = array(
				_('Teams'),
				array(
					'name' => 'team',
					'type' => 'input',
					'value' => array('value' => get_setting('fs_gen_default_team')),
					'help' => _('Insert the names of the teams who worked on this chapter.')
				)
			);

			$table = tabler($table, FALSE, TRUE);

			$data["table"] = $table;

			$this->viewdata["main_content_view"] = $this->load->view("admin/form.php", $data, TRUE);
			$this->load->view("admin/default.php", $this->viewdata);
			return true;
		}
		else {
			$comic = new Comic();
			if ($this->input->post()) {
				if ($comic->add($this->input->post())) {
					$config['upload_path'] = 'content/cache/';
					$config['allowed_types'] = 'jpg|png|gif';
					$this->load->library('upload', $config);
					$field_name = "thumbnail";
					if (count($_FILES) > 0 && $this->upload->do_upload($field_name)) {
						$up_data = $this->upload->data();
						if (!$this->files_model->comic_thumb($comic, $up_data)) {
							log_message("error", "Controller: comics.php/add_new: image failed being added to folder");
						}
						if (!unlink($up_data["full_path"])) {
							log_message('error', 'comics.php/add_new: couldn\'t remove cache file ' . $data["full_path"]);
							return false;
						}
					}
					redirect('/admin/comics/comic/' . $comic->stub);
				}
			}

			$table = ormer($comic);
			$table[] = array(
				_('Licensed in'),
				array(
					'name' => 'licensed',
					'type' => 'nation',
					'value' => array(),
					'help' => _('Insert the nations where the comic is licensed in order to limit the availability.')
				)
			);

			$table = tabler($table, FALSE, TRUE);
			$data['table'] = $table;

			$this->viewdata["extra_title"][] = _("Comic");
			$this->viewdata["main_content_view"] = $this->load->view("admin/form.php", $data, TRUE);
			$this->load->view("admin/default.php", $this->viewdata);
		}
	}

	function upload($type) {
		$config['upload_path'] = 'content/cache/';

		if ($this->input->post('uploader') == 'uploadify') {
			$config['allowed_types'] = 'png|zip|rar|gif|jpg|jpeg';
			$this->load->library('upload', $config);
			if (!$this->upload->do_upload('Filedata')) {
				log_message('error', 'durr' . print_r($_FILES, true));
				print_r($error = array('error' => $this->upload->display_errors()));
				log_message('error', $this->upload->display_errors());
				return false;
			}
			else {
				$data = $this->upload->data();
				$data["chapter_id"] = $this->input->post('chapter_id');
				$data["overwrite"] = $this->input->post('overwrite');

				if (strtolower($data['file_ext']) != ".zip" && strtolower($data['file_ext']) != ".rar")
					$this->files_model->page($data);
				else
					$this->files_model->compressed_chapter($data);
			}
			if (!unlink($data["full_path"])) {
				set_notice('error', 'comics.php/upload: couldn\'t remove cache file ' . $data["full_path"]);
				return false;
			}

			echo 1;

			return true;
		}

		switch ($type) {
			case "compressed_chapter":
				$config['allowed_types'] = 'zip';
				$this->load->library('upload', $config);
				if (!$this->upload->do_upload('Filedata')) {
					log_message('error', print_r($_FILES, true));
					print_r($error = array('error' => $this->upload->display_errors()));
					log_message('error', $this->upload->display_errors());
					return false;
				}
				else {
					$data = $this->upload->data();
					$data["chapter_id"] = $this->input->post('chapter_id');
					$data["overwrite"] = $this->input->post('overwrite');
					$this->files_model->compressed_chapter($data);
				}
				if (!unlink($data["full_path"])) {
					set_notice('error', 'comics.php/upload: couldn\'t remove cache file ' . $data["full_path"]);
					return false;
				}
				$chapter = new Chapter();
				$chapter->where('id', $data["chapter_id"])->get();
				$comic = new Comic();
				$comic->where('id', $chapter->comic_id)->get();

				if ($this->input->post('uploader') == 'uploadify') {
					$output['session'] = $this->session->get_js_session();
					echo json_encode($output);
					return;
				}

				redirect('admin/comics/comic/' . $comic->stub . '/' . $data["chapter_id"]);
				break;

			case "page":
				$config['allowed_types'] = 'gif|jpg|png';
				$this->load->library('upload', $config);
				if (!$this->upload->do_upload()) {
					$error = array('error' => $this->upload->display_errors());
					//$this->load->view('upload_form', $error);
					return false;
				}
				break;
		}

		return true;
	}

	function get_sess_id() {
		echo json_encode(array('session' => $this->session->get_js_session(), 'csrf' => $this->security->get_csrf_hash()));
	}

	function delete($type, $id = 0) {
		if (!isAjax()) {
			echo _('You can\'t delete chapters from outside the admin panel through this link.');
			log_message("error", "Controller: comics.php/remove: failed comic removal");
			return false;
		}
		$id = intval($id);

		switch ($type) {
			case("comic"):
				$comic = new Comic();
				$comic->where('id', $id)->get();
				if (!$comic->remove()) {
					log_message("error", "Controller: comics.php/remove: failed comic removal");
					return false;
				}
				flash_notice('notice', 'The comic ' . $comic->name . ' has been removed');
				echo json_encode(array('href' => site_url("admin/comics/manage")));
				break;
			case("chapter"):
				$chapter = new Chapter($id);
				if (!$comic = $chapter->remove()) {
					log_message("error", "Controller: comics.php/remove: failed chapter removal");
					return false;
				}
				set_notice('notice', 'Chapter deleted.');
				echo json_encode(array('href' => site_url("admin/comics/comic/" . $comic->stub)));
				break;
			case("page"):
				$page = new Page($this->input->post('id'));
				$page->get_chapter();
				$comic = new Chapter($chapter->comic_id);
				if (!$data = $page->remove_page()) {
					log_message("error", "Controller: comics.php/remove: failed page removal");
					return false;
				}
				echo json_encode(array('href' => site_url("admin/comics/comic/" . $page->chapter->comic->stub . "/" . $page->chapter->id)));
				break;
			case("allpages"):
				$chapter = new Chapter($id);
				$chapter->get_comic();
				if (!$chapter->remove_all_pages()) {
					log_message("error", "Controller: comics.php/remove: failed all pages removal");
					return false;
				}
				echo json_encode(array('href' => site_url("admin/comics/comic/" . $chapter->comic->stub . "/" . $chapter->id)));
				break;
		}
	}

	function import($stub) {
		if(!$this->tank_auth->is_admin())
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
		if ($this->input->post('directory')) {
			$data['directory'] = $this->input->post('directory');
			if (!is_dir($data['directory'])) {
				set_notice('error', _('The directory you set does not exist.'));
				$this->viewdata["main_content_view"] = $this->load->view("admin/comics/import", $data, TRUE);
				$this->load->view("admin/default.php", $this->viewdata);
				return FALSE;
			}
			$data['archives'] = $this->files_model->import_list($data);
			$this->viewdata["main_content_view"] = $this->load->view("admin/comics/import_compressed_list", $data, TRUE);
			$this->load->view("admin/default.php", $this->viewdata);
			return TRUE;
		}

		if ($this->input->post('action') == 'execute') {
			$result = $this->files_model->import_compressed();
			if (isset($result['error']) && !$result['error']) {
				echo json_encode($result);
				return FALSE;
			}
			else {
				echo json_encode($result);
				return true;
			}
		}

		$this->viewdata["main_content_view"] = $this->load->view("admin/comics/import", $data, TRUE);
		$this->load->view("admin/default.php", $this->viewdata);
	}

}