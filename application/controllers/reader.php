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
            //$comic->include_related('chapter');
            $comic->get_paged($page, 20);
            foreach($comic->all as $item)
            {
                $chapter = new Chapter();
                $chapter->where('comic_id', $item->id)->limit('1')->get();
                $item->chapter = $chapter;
            }
            
            $this->template->set('comic', $comic);
            $this->template->title('Comic list');
            $this->template->build('list');
            
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