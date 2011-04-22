<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Chapter DataMapper Model
 *
 * Use this basic model as a chapter for creating new models.
 * It is not recommended that you include this file with your application,
 * especially if you use a Chapter library (as the classes may collide).
 *
 * To use:
 * 1) Copy this file to the lowercase name of your new model.
 * 2) Find-and-replace (case-sensitive) 'Chapter' with 'Your_model'
 * 3) Find-and-replace (case-sensitive) 'chapter' with 'your_model'
 * 4) Find-and-replace (case-sensitive) 'chapters' with 'your_models'
 * 5) Edit the file as desired.
 *
 * @license		MIT License
 * @category	Models
 * @author		Phil DeJarnett
 * @link		http://www.overzealous.com
 */
class Chapter extends DataMapper {

	// Uncomment and edit these two if the class has a model name that
	//   doesn't convert properly using the inflector_helper.
	// var $model = 'chapter';
	// var $table = 'chapters';

	// You can override the database connections with this option
	// var $db_params = 'db_config_name';

	// --------------------------------------------------------------------
	// Relationships
	//   Configure your relationships below
	// --------------------------------------------------------------------

	// Insert related models that Chapter can have just one of.
	var $has_one = array('comic', 'team', 'joint');

	// Insert related models that Chapter can have more than one of.
	var $has_many = array('page');

	/* Relationship Examples
	 * For normal relationships, simply add the model name to the array:
	 *   $has_one = array('user'); // Chapter has one User
	 *
	 * For complex relationships, such as having a Creator and Editor for
	 * Chapter, use this form:
	 *   $has_one = array(
	 *   	'creator' => array(
	 *   		'class' => 'user',
	 *   		'other_field' => 'created_chapter'
	 *   	)
	 *   );
	 *
	 * Don't forget to add 'created_chapter' to User, with class set to
	 * 'chapter', and the other_field set to 'creator'!
	 *
	 */

	// --------------------------------------------------------------------
	// Validation
	//   Add validation requirements, such as 'required', for your fields.
	// --------------------------------------------------------------------

	var $validation = array(
		'name' => array(
			'rules' => array('max_length' => 256),
			'label' => 'Name',
			'type'	=> 'input'
		),
                'comic_id' => array(
			'rules' => array('is_int', 'required', 'max_length' => 256),
			'label' => 'Comic ID',
			'type'	=> 'hidden'
		),
                'team_id' => array(
			'rules' => array('is_int', 'max_length' => 256),
			'label' => 'Team ID'
		),
                'joint_id' => array(
			'rules' => array('is_int', 'max_length' => 256),
			'label' => 'Joint ID'
		),
                'stub' => array(
			'rules' => array('stub', 'required', 'max_length' => 256),
			'label' => 'Stub'
		),
                'chapter' => array(
			'rules' => array('is_int', 'required'),
			'label' => 'Chapter number',
			'type'	=> 'input'
		),
                'subchapter' => array(
			'rules' => array('is_int'),
			'label' => 'Subchapter number',
			'type'	=> 'input'
		),
                'uniqid' => array(
			'rules' => array('required', 'max_length' => 256),
			'label' => 'Uniqid'
		),
                'hidden' => array(
			'rules' => array('is_int'),
			'label' => 'Hidden',
			'type'	=> 'checkbox'
		),
                'description' => array(
			'rules' => array(),
			'label' => 'Description',
			'type'	=> 'textarea'
		),
                'thumbnail' => array(
			'rules' => array('max_length' => 512),
			'label' => 'Thumbnail'
		),
                'lastseen' => array(
			'rules' => array(),
			'label' => 'Lastseen'
		),
                'creator' => array(
			'rules' => array('required'),
			'label' => 'Creator'
		),
                'editor' => array(
			'rules' => array('required'),
			'label' => 'Editor'
		)
	);

	// --------------------------------------------------------------------
	// Default Ordering
	//   Uncomment this to always sort by 'name', then by
	//   id descending (unless overridden)
	// --------------------------------------------------------------------

	// var $default_order_by = array('name', 'id' => 'desc');

	// --------------------------------------------------------------------

	/**
	 * Constructor: calls parent constructor
	 */
    function __construct($id = NULL)
	{
		parent::__construct($id);
    }

	// --------------------------------------------------------------------
	// Post Model Initialisation
	//   Add your own custom initialisation code to the Model
	// The parameter indicates if the current config was loaded from cache or not
	// --------------------------------------------------------------------
	function post_model_init($from_cache = FALSE)
	{
	}

	// --------------------------------------------------------------------
	// Custom Methods
	//   Add your own custom methods here to enhance the model.
	// --------------------------------------------------------------------

	/* Example Custom Method
	function get_open_chapters()
	{
		return $this->where('status <>', 'closed')->get();
	}
	*/

	// --------------------------------------------------------------------
	// Custom Validation Rules
	//   Add custom validation rules for this model here.
	// --------------------------------------------------------------------

	/* Example Rule
	function _convert_written_numbers($field, $parameter)
	{
	 	$nums = array('one' => 1, 'two' => 2, 'three' => 3);
	 	if(in_array($this->{$field}, $nums))
		{
			$this->{$field} = $nums[$this->{$field}];
	 	}
	}
	*/


        //public function add_chapter($name, $comic_id, $chapter, $subchapter = 0, $team_id = 0, $joint_id = 0, $hidden = 0, $description = "")
        
		public function add_chapter($data)
		{
			$this->to_stub = $data['chapter']."_".$data['subchapter']."_".$data['name'];
			$this->uniqid = uniqid();
			$this->stub = $this->stub();

            $comic = new Comic;
            $comic->where("id", $data['comic_id'])->get();
            if($comic->result_count() == 0)
            {
                set_notice('error', 'The comic ID you were adding the chapter to does not exist.');
                log_message('error', 'add_chapter: comic_id does not exist in comic database');
                return false;
            }

            if (!$this->add_chapter_dir($comic->stub, $comic->uniqid))
            {
                log_message('error', 'add_chapter: failed creating dir');
                return false;
            }
            if(!$this->update_chapter_db())
            {
                $this->remove_chapter_dir($comic->stub, $comic->uniqid);
                return false;
            }

            return $comic;
        }

        public function remove_chapter()
        {
            $comic = new Comic();
            $comic->where("id", $this->comic_id)->get();
            if(!$this->remove_chapter_dir($comic->stub, $comic->uniqid))
            {
                log_message('error', 'remove_chapter: failed to delete dir');
                return false;
            }
            
            if (!$this->remove_chapter_db())
            {
                log_message('error', 'remove_chapter: failed to delete database entry');
                return false;
            }

            return $comic;
        }

        public function update_chapter_db($data = array())
        {
            // Check if we're updating or creating a new entry by looking at $data["id"].
            // False is pushed if the ID was not found.
            if(isset($data["id"]))
            {
                $this->where("id", $data["id"])->get();
                if ($this->result_count() == 0)
                {
                    set_notice('error', 'The chapter you were referring to does not exist.');
                    log_message('error', 'update_chapter_db: failed to find requested id');
                    return false;
                }
				$old_stub = $this->stub;
            }
            else // let's set the creator name if it's a new entry
            {    // let's also check that the related comic is defined, and exists
                if(!isset($this->comic_id))
                {
                    set_notice('error', 'You didn\'t select a chapter to refer to.');
                    log_message('error', 'update_chapter_db: comic_id was not set');
                    return false;
                }

                $comic = new Comic();
                $comic->where("id", $this->comic_id)->get();
                if($comic->result_count() == 0)
                {
                    set_notice('error', 'The comic you were referring to does not exist.');
                    log_message('error', 'update_chapter_db: comic_id does not exist in comic database');
                    return false;
                }

                $this->creator = $this->logged_id();
            }

            // always set the editor name
            $this->editor = $this->logged_id();
			
			unset($data["creator"]);
			unset($data["editor"]);
			foreach($data as $key => $value)
            {
                $this->$key = $value;
            }

			if (!isset($this->uniqid)) $this->uniqid = uniqid();
			if (!isset($this->stub)) $this->stub = $this->stub();
			
			$this->stub = $this->chapter.'_'.$this->subchapter.'_'.$this->name;
			$this->stub = $this->stub();
			
			if($old_stub != $this->stub)
			{
				$comic = new Comic();
				$comic->where('id', $this->comic_id)->get();
				$dir_old = "content/comics/".$comic->stub."_".$comic->uniqid."/".$old_stub."_".$this->uniqid;
				$dir_new = "content/comics/".$comic->stub."_".$comic->uniqid."/".$this->stub."_".$this->uniqid;
				rename($dir_old, $dir_new);
			}
			
			
			if(count($data['team']) > 1)
			{
				$this->team_id = 0;
				$joint = new Joint();
				$this->joint_id = $joint->add_joint_via_name($data['team']);
				
			}
			else if(count($data['team']) == 1)
			{
				$this->joint_id = 0;
				$team = new Team();
				$team->where("name", $data['team'][0])->get();
				if($team->result_count() == 0)
				{
					set_notice('error', 'The team you were referring this chapter to for doesn\'t exist.');
					log_message('error', 'update_chapter_db: team_id does not exist in team database');
					return false;
				}
				$this->team_id = $team->id;
			}
			else 
			{
				set_notice('error', 'You haven\'t selected any team related to this chapter.');
				log_message('error', 'update_chapter_db: team_id does not defined');
				return false;
			}            

			
            // let's save and give some error check. Push false if fail, true if good.
            $success = $this->save();
            if (!$success)
            {
                if (!$this->valid)
                {
                    log_message('error', $this->error->string);
                    set_notice('error', 'One or more of the fields inputted had the wrong kind of values.');
                    log_message('error', 'update_chapter_db: failed validation');
                } else {
                    set_notice('error', 'Failed to save to database for unknown reasons.');
                    log_message('error', 'update_chapter_db: failed to save');
                }
                return false;
            }
            else
            {
                return true;
            }

        }

        public function remove_chapter_db()
        {
            $pages = new Page();
            $pages->where('chapter_id', $this->id)->get_iterated();
            foreach($pages as $page)
            {
                $page->remove_page_db();
            }

            $success = $this->delete();
            if(!$success)
            {
                set_notice('error', 'Failed to remove the chapter from the database for unknown reasons.');
                log_message('error', 'remove_chapter_db: id found but entry not removed');
                return false;
            }

            return true;
        }


        public function add_chapter_dir($comicstub, $uniqid)
        {
            $dir = "content/comics/".$comicstub."_".$uniqid."/".$this->stub."_".$this->uniqid;
            if (!mkdir($dir))
            {
                set_notice('error', 'Failed to create the chapter directory. Please, check file permissions.');
                log_message('error', 'add_chapter_dir: folder could not be created');
                return false;
            }

            return true;
        }

        public function remove_chapter_dir($comicstub, $uniqid)
        {
            $dir = "content/comics/".$comicstub."_".$uniqid."/".$this->stub."_".$this->uniqid."/";
            if (!delete_files($dir, TRUE))
            {
                set_notice('error', 'Failed to remove the files inside the chapter directory. Please, check file permissions.');
                log_message('error', 'remove_chapter_dir: files inside folder could not be removed');
                return false;
            }
            else
            {
                if(!rmdir($dir))
                {
                    set_notice('error', 'Failed to remove the chapter directory. Please, check file permissions.');
                    log_message('error', 'remove_chapter_dir: folder could not be removed');
                    return false;
                }
            }

            return true;
        }



        public function get_pages()
        {
            $comic = new Comic();
            $comic->where('id', $this->comic_id)->get();
            $pages = new Page();
            $pages->where('chapter_id', $this->id)->get();

            $return = array();

            foreach($pages->all as $key => $item)
            {
                $return[$key]['object'] = $item;
                $return[$key]['width'] = $item->width;
                $return[$key]['height'] = $item->height;
                $return[$key]['url'] = base_url()."content/comics/".$comic->stub."_".$comic->uniqid."/".$this->stub."_".$this->uniqid."/".$item->filename;
                $return[$key]['thumb_url'] = base_url()."content/comics/".$comic->stub."_".$comic->uniqid."/".$this->stub."_".$this->uniqid."/".$item->thumbnail.$item->filename;
            }
            return $return;
        }

}

/* End of file chapter.php */
/* Location: ./application/models/chapter.php */
