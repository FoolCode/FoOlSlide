<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reader extends Public_Controller {

	function __construct()
	{
		parent::__construct();
                $this->load->library('pagination');
                $this->load->library('template');
                $this->template->set_layout('reader');
        }
        
        public function index($comic = NULL, $chapter = NULL, $subchapter = NULL, $group = NULL, $version = NULL, $id = NULL, $ispage = "page", $page = 0)
        {
            if(is_null($comic))
            {
                redirect('reader/list');
                return true;
            }
            
            if(is_null($chapter))
            {
                $this->_list_chapters($comic);
                return true;
            }
            
            $this->reader($comic, $chapter, $subchapter, $group, $version, $id);
        }
        
        public function lista($page = 1)
        {
            $comic = new Comic();
            $comic->get_paged_iterated($page, 20);
            $data["comic"] = $comic;
            $this->template->title('Comic list');
            $this->template->build('list', $data);
            
        }
        
        
        /*
        public function latest();
        public function rss();
        
        public function list_chapters($comic)
        {
            
        }
        
        public function reader($comic, $chapter, $subchapter, $group, $version, $id)
        {
            
        }
         * 
         */
}