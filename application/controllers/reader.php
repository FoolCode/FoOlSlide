<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Read extends Public_Controller {

	function __construct()
	{
		parent::__construct();
                $this->load->library('pagination');
                $this->viewdata['controller_title'] = "Reader";
        }
        
        public function index($comic = NULL, $chapter = NULL, $subchapter = NULL, $group = NULL, $version = NULL, $id = NULL, $ispage = "page", $page = 0)
        {
            if(is_null($comic))
            {
                $this->_list_comics();
                return true;
            }
            
            if(is_null($chapter))
            {
                $this->_list_chapters($comic);
                return true;
            }
            
            $this->reader($comic, $chapter, $subchapter, $group, $version, $id);
        }
        
        public function lista($page)
        {
            $comic = new Comic();
            $comic->get_paged_iterated($page, 20);
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