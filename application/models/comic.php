<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Comic DataMapper Model
 *
 * Use this basic model as a comic for creating new models.
 * It is not recommended that you include this file with your application,
 * especially if you use a Comic library (as the classes may collide).
 *
 * To use:
 * 1) Copy this file to the lowercase name of your new model.
 * 2) Find-and-replace (case-sensitive) 'Comic' with 'Your_model'
 * 3) Find-and-replace (case-sensitive) 'comic' with 'your_model'
 * 4) Find-and-replace (case-sensitive) 'comics' with 'your_models'
 * 5) Edit the file as desired.
 *
 * @license		MIT License
 * @category	Models
 * @author		Phil DeJarnett
 * @link		http://www.overzealous.com
 */
class Comic extends DataMapper {

	// Uncomment and edit these two if the class has a model name that
	//   doesn't convert properly using the inflector_helper.
	// var $model = 'comic';
	// var $table = 'comics';

	// You can override the database connections with this option
	// var $db_params = 'db_config_name';

	// --------------------------------------------------------------------
	// Relationships
	//   Configure your relationships below
	// --------------------------------------------------------------------

	// Insert related models that Comic can have just one of.
	var $has_one = array();

	// Insert related models that Comic can have more than one of.
	var $has_many = array('chapter');

	/* Relationship Examples
	 * For normal relationships, simply add the model name to the array:
	 *   $has_one = array('user'); // Comic has one User
	 *
	 * For complex relationships, such as having a Creator and Editor for
	 * Comic, use this form:
	 *   $has_one = array(
	 *   	'creator' => array(
	 *   		'class' => 'user',
	 *   		'other_field' => 'created_comic'
	 *   	)
	 *   );
	 *
	 * Don't forget to add 'created_comic' to User, with class set to
	 * 'comic', and the other_field set to 'creator'!
	 *
	 */

	// --------------------------------------------------------------------
	// Validation
	//   Add validation requirements, such as 'required', for your fields.
	// --------------------------------------------------------------------

	var $validation = array(
		'name' => array(
			'rules' => array('required', 'unique', 'max_length' => 256),
			'label' => 'Name',
			'type'	=> 'input',
			'placeholder' => 'required'
		),
                'stub' => array(
			'rules' => array('required', 'stub', 'unique', 'max_length' => 256),
			'label' => 'Stub'
		),
                'uniqid' => array(
			'rules' => array('required', 'max_length' => 256),
			'label' => 'Uniqid'
		),
                'hidden' => array(
			'rules' => array(),
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
			'label' => 'Thumbnail',
			'type'	=> 'upload',
			'display' => 'image'
		),
                'lastseen' => array(
			'rules' => array(),
			'label' => 'Lastseen'
		),
                'creator' => array(
			'rules' => array(''),
			'label' => 'Creator'
		),
                'editor' => array(
			'rules' => array(''),
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
	function get_open_comics()
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




        public function add_comic($data = array())
        {
			$this->to_stub = $data['name'];
            $this->uniqid = uniqid();
			$this->stub = $this->stub();
            
            
            if (!$this->add_comic_dir())
            {
                log_message('error', 'add_comic: failed creating dir');
                return false;
            }
            if(!$this->update_comic_db($data))
            {
                log_message('error', 'add_comic: failed writing to database');
                $this->remove_comic_dir();
                return false;
            }

            return true;
        }

        public function remove_comic()
        {
            if(!$this->remove_comic_dir())
            {
                log_message('error', 'remove_comic: failed to delete dir');
                return false;
            }
            
            if (!$this->remove_comic_db())
            {
                log_message('error', 'remove_comic: failed to delete database entry');
                return false;
            }

            return true;
        }

        public function update_comic_db($data = array())
        {

            // Check if we're updating or creating a new entry by looking at $data["id"].
            // False is pushed if the ID was not found.
            if(isset($data["id"]) && $data['id'] != '')
            {
                $this->where("id", $data["id"])->get();
                if ($this->result_count() == 0)
                {
                    set_notice('error', 'The ID of the comic you wanted to edit doesn\'t exist.');
                    log_message('error', 'update_comic_db: failed to find requested id');
                    return false;
                }
				$old_stub = $this->stub;
            }
            else // let's set the creator name if it's a new entry
            {
                $this->creator = $this->logged_id();
            }

            // always set the editor name
            $this->editor = $this->logged_id();

			unset($data["creator"]);
			unset($data["editor"]);
            //
            foreach($data as $key => $value)
            {
                $this->$key = $value;
            }
			
			if (!isset($this->uniqid)) $this->uniqid = uniqid();
			if (!isset($this->stub)) $this->stub = $this->stub();
			
			$this->stub = $this->name;
			$this->stub = $this->stub();
			
			if(isset($old_stub) && $old_stub != $this->stub)
			{
				$dir_old = "content/comics/".$old_stub."_".$this->uniqid;
				$dir_new = "content/comics/".$this->stub."_".$this->uniqid;
				rename($dir_old, $dir_new);
			}

            // let's save and give some error check. Push false if fail, true if good.
            $success = $this->save();
            if (!$success)
            {
                if (!$this->valid)
                {
                    set_notice('error', 'One or more of the fields you inputted didn\'t respect the values required.');
                    log_message('error', 'update_comic_db: failed validation');
                } else {
                    set_notice('error', 'Failed saving the Comic to database for unknown reasons.');
                    log_message('error', 'update_comic_db: failed to save');
                }
                return false;
            }

            return $this;
        }

        public function remove_comic_db()
        {
            if ($this->result_count() != 1)
            {
                set_notice('error', 'You tried removing a comic that doesn\'t exist');
                log_message('error', 'remove_comic_db: id not found, entry not removed');
                return false;
            }

            $chapters = new Chapter();
            $chapters->where("comic_id", $this->id)->get_iterated();
            foreach($chapters as $chapter)
            {
                $chapter->remove_chapter_db();
            }

            $temp = $this->get_clone();
            $success = $this->delete();
            if(!$success)
            {
                set_notice('error', 'The comic couldn\'t be removed from the database for unknown reasons.');
                log_message('error', 'remove_comic_db: id found but entry not removed');
                return false;
            }

            return $temp;
        }

        public function add_comic_dir()
        {
            if (!mkdir("content/comics/".$this->stub."_".$this->uniqid))
            {
                set_notice('error', 'The directory could not be created. Please, check file permissions.');
                log_message('error', 'add_comic_dir: folder could not be created');
                return false;
            }
            return true;
        }

        public function remove_comic_dir()
        {
            $dir = "content/comics/".$this->stub."_".$this->uniqid."/";
            if (!delete_files($dir, TRUE))
            {
                set_notice('error', 'The files inside the comic directory could not be removed. Please, check the file permissions.');
                log_message('error', 'remove_comic_dir: files inside folder could not be removed');
                return false;
            }
            else
            {
                if(!rmdir($dir))
                {
                    set_notice('error', 'The directory could not be removed. Please, check file permissions.');
                    log_message('error', 'remove_comic_dir: folder could not be removed');
                    return false;
                }
            }

            return true;
        }

        public function add_comic_thumb($filedata)
        {
            if($this->thumbnail != "") $this->remove_comic_thumb();

            $dir = "content/comics/".$this->stub."_".$this->uniqid."/";
            if (!copy($filedata["server_path"], $dir.$filedata["name"]))
            {
                set_notice('error', 'Failed to create the thumbnail image for the comic. Check file permissions.');
                log_message('error', 'add_comic_thumb: failed to create/copy the image');
                return false;
            }
            $CI =& get_instance();
            $CI->load->library('image_lib');

            $image =  "thumb_".$filedata["name"];

            $img_config['image_library'] = 'GD2';
            $img_config['source_image'] = $filedata["server_path"];
            $img_config["new_image"] = $dir.$image;
            $img_config['maintain_ratio'] = TRUE;
            $img_config['width'] = 250;
            $img_config['height'] = 250;
            $img_config['maintain_ratio'] = TRUE;
            $img_config['master_dim'] = 'auto';
            $CI->image_lib->initialize($img_config);

            if(!$CI->image_lib->resize())
            {
                set_notice('error', 'Failed to create the thumbnail image for the comic. Resize function didn\'t work');
                log_message('error', 'add_comic_thumb: failed to create thumbnail');
                return false;
            }
            $CI->image_lib->clear();

            $this->thumbnail = $filedata["name"];
            $this->save();

            return $filedata["name"];
        }


        public function remove_comic_thumb()
        {
            $dir = "content/comics/".$this->stub."_".$this->uniqid."/";
            if (!unlink($dir.$this->thumbnail))
            {
                set_notice('error', 'Failed to remove the thumbnail\'s original image. Please, check file permissions.');
                log_message('error', 'Model: comic_model.php/remove_comic_thumb: failed to delete image');
                return false;
            }

            if (!unlink($dir."thumb_".$this->thumbnail))
            {
                set_notice('error', 'Failed to remove the thumbnail image. Please, check file permissions.');
                log_message('error', 'Model: comic_model.php/remove_comic_thumb: failed to delete thumbnail');
                return false;
            }

            $this->thumbnail = "";
            if(!$this->save())
            {
                set_notice('error', 'Failed to remove the thumbnail image from the database.');
                log_message('error', 'Model: comic_model.php/remove_comic_thumb: failed to remove from database');
                return false;
            }

            return true;
        }

        public function get_thumb($full = FALSE)
        {
			if($this->thumbnail != "")
            return base_url()."content/comics/".$this->stub."_".$this->uniqid."/".($full ? "" : "thumb_").$this->thumbnail;
			return false;
		}

}

/* End of file comic.php */
/* Location: ./application/models/comic.php */
