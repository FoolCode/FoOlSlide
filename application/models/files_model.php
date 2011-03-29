<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Files_model extends CI_Model {


        function __construct()
        {
            // Call the Model constructor
            parent::__construct();
            $this->load->library('unzip');
        }

        public function compressed_chapter($data)
        {
            $chapter = new Chapter();
            $chapter->where("id", $data["chapter_id"])->get();
            $uniqid = uniqid();
            $overwrite = ($data["overwrite"] == 1);
            $cachedir = 'content/cache/'.$data["raw_name"]."_".$uniqid;
            if(!mkdir($cachedir))
            {
                log_message('error', 'compressed_chapter: failed creating dir');
                return false;
            }
            $this->unzip->allow(array('png', 'gif', 'jpeg', 'jpg'));
            $this->unzip->extract($data["full_path"], $cachedir);

            // Get the filename
            $dirarray = get_dir_file_info($cachedir, FALSE);
            //echo '<pre>'; print_r($dirarray); echo '</pre>';

            foreach($dirarray as $key => $value)
            {
                $page = new Page();
                if(!$page->add_page($value, $chapter->id, 0, ""))
                {
                    log_message('error', 'compressed_chapter: one page in the loop failed being added');
                    return false;
                }
            }

            // Let's delete all the cache
            if (!delete_files($cachedir, TRUE))
            {
                log_message('error', 'compressed_chapter: files inside cache dir could not be removed');
                return false;
            }
            else
            {
                if(!rmdir($cachedir))
                {
                    log_message('error', 'compressed_chapter: cache dir could not be removed');
                    return false;
                }
            }
            return true;
        }

        // This is just a plug to adapt the variable names for the comic_model
        public function page($data)
        {
            // $data["chapter_id"];
            // $data["raw_name"];
            $file["server_path"] = $data["full_path"];
            $file["name"] = $data["file_name"];

            $page = new Page();
            if (!$page->add_page($file, $data["chapter_id"], $data["hidden"], $data["description"]))
            {
                log_message('error', 'page: function add_page failed');
                return false;
            }
            return true;
        }

        // This is just a plug to adapt the variable names for the comic_model
        public function comic_thumb($comic, $data)
        {
            $file["server_path"] = $data["full_path"];
            $file["name"] = $data["file_name"];

            if (!$comic->add_comic_thumb($file))
            {
                log_message('error', 'Model: files_model.php/comic_thumb: function add_comic_thumb failed');
                return false;
            }
            return true;
        }

}