<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comics extends Admin_Controller {

		function __construct()
		{
		parent::__construct();
                $this->ion_auth->logged_in() or redirect('auth/login');
                $this->ion_auth->is_admin() or redirect('admin');
                $this->ion_auth->is_admin() or die(1);
                $this->load->model('files_model');
                //$this->load->library('form_validation');
                $this->load->library('pagination');
                $this->viewdata['controller_title'] = "Comics";
        }

        function index()
        {
            redirect('/admin/comics/manage');
        }

		function manage($page = 1)
        {	
            $this->viewdata["function_title"] = '<a href="'.site_url('/admin/comics/manage/').'">manage</a>';
            $comics = new Comic();
			
			if($this->input->post('search'))
			{
				$search = $this->input->post('search');
				$comics->ilike('name', $search)->limit(20);
				$this->viewdata["extra_title"][] = 'Searching: '.htmlspecialchars(($search));
			}            
			
			$comics->order_by('name', 'ASC');
            $comics->get_paged_iterated($page, 20);
            $data["comics"] = $comics;

            $this->viewdata["main_content_view"] = $this->load->view("admin/comics/manage.php", $data, TRUE);
            $this->load->view("admin/default.php", $this->viewdata);
        }



        function comic($stub = NULL, $chapter_id = "")
        {
            $comic = new Comic();
            $comic->where("stub", $stub)->get();
            if($comic->result_count() == 0)
            {
                set_notice('warn', 'The comic you looked for does not exist.');
                $this->manage();
                return false;
            }

            $this->viewdata["function_title"] = '<a href="'.site_url('admin/comics/comic').'/'.$comic->stub.'">'.$comic->name.'</a>';
            $data["comic"] = $comic;

            if($chapter_id != "")
            {
				if($this->input->post())
				{
					$chapter = new Chapter();
					$chapter->update_chapter_db($this->input->post());
				}
				
                $chapter = new Chapter();
                $chapter->where('id', $chapter_id);
				$data["chapter"] = $chapter->get();
				
                $team = new Team();                
				$teams = $team->get_teams_name($chapter->team_id, $chapter->joint_id);
				
				$table = ormer($chapter);
				
				$table[] = array(
					'Teams',
					array(
						'name' => 'team',
						'type' => 'input',
						'value' => $teams
					)
				);
				
				$table = tabler($table);
				
				$data["table"] = $table;
				
                
                $this->viewdata["extra_title"][] = (($chapter->name != "") ? $chapter->name : $chapter->chapter.".".$chapter->subchapter);

                
                $data["pages"] = $chapter->get_pages();

                $this->viewdata["main_content_view"] = $this->load->view("admin/comics/chapter.php", $data, TRUE);
                $this->load->view("admin/default.php", $this->viewdata);
                return true;
            }
			
			if($this->input->post())
			{
				$comic->update_comic_db($this->input->post());
				
				$config['upload_path'] = 'content/cache/';
				$config['allowed_types'] = 'jpg|png|gif';
				$this->load->library('upload', $config);
				$field_name = "thumbnail";
				if (count($_FILES) > 0 && $this->upload->do_upload($field_name))
				{
					$up_data = $this->upload->data();
					if(!$this->files_model->comic_thumb($comic, $up_data))
					{
						log_message("error", "Controller: comics.php/comic: image failed being added to folder");
					}
					if ( ! unlink($up_data["full_path"]))
					{
						set_notice('error', 'comics.php/comic: couldn\'t remove cache file '.$data["full_path"]);
						return false;
					}
				}
			}

            $chapters = new Chapter();
            $chapters->where('comic_id', $comic->id)->include_related('team')
                   ->order_by('chapter', 'DESC')->order_by('subchapter', 'DESC')->get();
            foreach($chapters->all as $key => $item)
            {	
                if ($item->joint_id > 0)
                {
                    $teams = new Team();
					$jointers = $teams->get_teams_name(0, $item->joint_id);					
					$item->jointers = $jointers;
					unset($jointers);
					unset($teams);
                }
            }

			$data["chapters"] = $chapters;
			
			if($comic->get_thumb())$comic->thumbnail = $comic->get_thumb();
			
			$table = ormer($comic);
			
			$table[] = array(
					'Licensed nations',
					array(
						'name' => 'licensed',
						'type' => 'input',
						'value' => '',
						'help' => 'Insert the nations where the comic is licensed to disable availability for the nation.'
					)
				);
			
			$table = tabler($table);
			$data['table'] = $table;

            $this->viewdata["main_content_view"] = $this->load->view("admin/comics/comic.php", $data, TRUE);
            $this->load->view("admin/default.php", $this->viewdata);
        }


        function add_new($stub = "")
        {
			$this->viewdata["function_title"] = "Add new";
			
			//$stub stands for $comic, but there's already a $comic here
			if($stub != "")
			{
				if($this->input->post())
				{
					$chapter = new Chapter();
					if($comic = $chapter->add_chapter($this->input->post()))
					{
						redirect('/admin/comics/comic/'.$comic->stub.'/'.$chapter->id);
					}
				}
				$comic = new Comic();
				$comic->where('stub', $stub)->get();
				$this->viewdata["extra_title"][] = "Chapter in ".$comic->name;
				$chapter = new Chapter();
				$chapter->comic_id = $comic->id;
				
				$table = ormer($chapter);
				
				$table[] = array(
					'Teams',
					array(
						'name' => 'team',
						'type' => 'input',
						'value' => array('value' => get_setting('fs_gen_default_team'))
					)
				);
				
				$table = tabler($table, FALSE, TRUE);
				
				$data["table"] = $table;
					
				$this->viewdata["main_content_view"] = $this->load->view("admin/form.php", $data, TRUE);
				$this->load->view("admin/default.php", $this->viewdata);
				return true;
			}
			else
			{
				$comic = new Comic();
				if($this->input->post())
				{
					if($comic->add_comic($this->input->post()))
					{
						$config['upload_path'] = 'content/cache/';
						$config['allowed_types'] = 'jpg|png|gif';
						$this->load->library('upload', $config);
						$field_name = "thumbnail";
						if (count($_FILES) > 0 && $this->upload->do_upload($field_name))
						{
						    $up_data = $this->upload->data();
							if(!$this->files_model->comic_thumb($comic, $up_data))
                            {
                                log_message("error", "Controller: comics.php/add_new: image failed being added to folder");
                            }
                            if ( ! unlink($up_data["full_path"]))
                            {
                                set_notice('error', 'comics.php/add_new: couldn\'t remove cache file '.$data["full_path"]);
                                return false;
                            }
						}
						redirect('/admin/comics/comic/'.$comic->stub);
					}
				}
				
				$table = ormer($comic);
				$table[] = array(
					'Licensed nations',
					array(
						'name' => 'licensed',
						'type' => 'nation',
						'value' => array(),
						'help' => 'Insert the nations where the comic is licensed to disable availability for the nation.'
					)
				);
				
				$table = tabler($table, FALSE, TRUE);
				$data['table'] = $table;
            
				$this->viewdata["extra_title"][] = "Comic";
				$this->viewdata["main_content_view"] = $this->load->view("admin/form.php",$data, TRUE);
				$this->load->view("admin/default.php", $this->viewdata);
			}
        }


        function upload($type)
        {
            $config['upload_path'] = 'content/cache/';
			
			if($this->input->post('uploader') == 'uploadify')
			{			
				$config['allowed_types'] = 'png|zip|gif|jpg|jpeg';
				$this->load->library('upload', $config);
				if ( ! $this->upload->do_upload('Filedata'))
				{
					log_message('error', 'durr'.print_r($_FILES, true));
					print_r($error = array('error' => $this->upload->display_errors()));
					log_message('error',$this->upload->display_errors());
					return false;
				}
				else
				{
					$data = $this->upload->data();
					$data["chapter_id"] = $this->input->post('chapter_id');
					$data["overwrite"] = $this->input->post('overwrite');
					
					if($data['file_ext'] != ".zip")
					$this->files_model->page($data);
					else
					$this->files_model->compressed_chapter($data);
				}
				if ( ! unlink($data["full_path"]))
				{
				   set_notice('error', 'comics.php/upload: couldn\'t remove cache file '.$data["full_path"]);
				   return false;
				}
				
				echo 1;

				return true;
			}

            switch($type)
            {
                case "compressed_chapter":
                    $config['allowed_types'] = 'zip';
                    $this->load->library('upload', $config);
                    if ( ! $this->upload->do_upload('Filedata'))
                    {
						log_message('error',print_r($_FILES, true));
                        print_r($error = array('error' => $this->upload->display_errors()));
						log_message('error',$this->upload->display_errors());
                        return false;
                    }
                    else
                    {
                        $data = $this->upload->data();
                        $data["chapter_id"] = $this->input->post('chapter_id');
                        $data["overwrite"] = $this->input->post('overwrite');
                        $this->files_model->compressed_chapter($data);
                    }
                    if ( ! unlink($data["full_path"]))
                    {
                       set_notice('error', 'comics.php/upload: couldn\'t remove cache file '.$data["full_path"]);
                       return false;
                    }
                    $chapter = new Chapter();
                    $chapter->where('id', $data["chapter_id"])->get();
                    $comic = new Comic();
                    $comic->where('id', $chapter->comic_id)->get();
					
					if($this->input->post('uploader') == 'uploadify')
					{
						$output['session'] = $this->session->get_js_session();
						echo json_encode($output);
						return;
					}
					
                    redirect('admin/comics/comic/'.$comic->stub.'/'.$data["chapter_id"]);
                    break;
					
                case "page":
                    $config['allowed_types'] = 'gif|jpg|png';
                    $this->load->library('upload', $config);
                    if ( ! $this->upload->do_upload())
                    {
                        $error = array('error' => $this->upload->display_errors());
                        //$this->load->view('upload_form', $error);
                        return false;
                    }
                    break;
            }

            return true;
        }
		
		function get_sess_id()
		{
			//$sess['session'] = $this->session->get_js_session();
			//echo json_encode($sess);
			echo $this->session->get_js_session();
		}

        function delete($type, $id = 0)
        {
            switch($type)
            {
                case("comic"):
                    $comic = new Comic();
                    $comic->where('id', $id)->get();
                    if(!$comic->remove_comic())
                    {
                        log_message("error", "Controller: comics.php/remove: failed comic removal");
						return false;
                    }
                    flash_notice('notice','The comic '.$comic->name.' has been removed');
                    redirect("admin/comics/manage");
                    break;
                case("chapter"):
                    $chapter = new Chapter();
                    $chapter->where('id', $id)->get();
                    if(!$comic = $chapter->remove_chapter())
                    {
                        log_message("error", "Controller: comics.php/remove: failed chapter removal");
						return false;
                    }
					set_notice('notice', 'Chapter deleted.');
                    redirect("admin/comics/comic/".$comic->stub);
                    break;
                case("page"):
                    $page = new Page();
                    $page->where('id', $this->input->post('id'))->get();
                    if(!$data = $page->remove_page())
                    {
                        log_message("error", "Controller: comics.php/remove: failed page removal");
						return false;
                    }
					echo 'success';
                    break;
				case("allpages"):
					$chapter = new Chapter();
					$chapter->where('id', $this->input->post('id'))->get();
					if(!$chapter->remove_all_pages())
					{
						log_message("error", "Controller: comics.php/remove: failed all pages removal");
						return false;
					}
					echo 'success';
					break;
            }
        }
}