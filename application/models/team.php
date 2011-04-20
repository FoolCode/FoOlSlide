<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Team DataMapper Model
 *
 * Use this basic model as a team for creating new models.
 * It is not recommended that you include this file with your application,
 * especially if you use a Team library (as the classes may collide).
 *
 * To use:
 * 1) Copy this file to the lowercase name of your new model.
 * 2) Find-and-replace (case-sensitive) 'Team' with 'Your_model'
 * 3) Find-and-replace (case-sensitive) 'team' with 'your_model'
 * 4) Find-and-replace (case-sensitive) 'teams' with 'your_models'
 * 5) Edit the file as desired.
 *
 * @license		MIT License
 * @category	Models
 * @author		Phil DeJarnett
 * @link		http://www.overzealous.com
 */
class Team extends DataMapper {

	// Uncomment and edit these two if the class has a model name that
	//   doesn't convert properly using the inflector_helper.
	// var $model = 'team';
	// var $table = 'teams';

	// You can override the database connections with this option
	// var $db_params = 'db_config_name';

	// --------------------------------------------------------------------
	// Relationships
	//   Configure your relationships below
	// --------------------------------------------------------------------

	// Insert related models that Team can have just one of.
	var $has_one = array();

	// Insert related models that Team can have more than one of.
	var $has_many = array('chapter');

	/* Relationship Examples
	 * For normal relationships, simply add the model name to the array:
	 *   $has_one = array('user'); // Team has one User
	 *
	 * For complex relationships, such as having a Creator and Editor for
	 * Team, use this form:
	 *   $has_one = array(
	 *   	'creator' => array(
	 *   		'class' => 'user',
	 *   		'other_field' => 'created_team'
	 *   	)
	 *   );
	 *
	 * Don't forget to add 'created_team' to User, with class set to
	 * 'team', and the other_field set to 'creator'!
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
			'type'	=> 'input'
		),
                'stub' => array(
			'rules' => array('required', 'stub', 'unique', 'max_length' => 256),
			'label' => 'Stub'
		),
                'url' => array(
			'rules' => array('max_length' => 256),
			'label' => 'URL',
			'type'	=> 'input'
		),
                'forum' => array(
			'rules' => array('max_length' => 256),
			'label' => 'Forum',
			'type'	=> 'input'
		),
                'irc' => array(
			'rules' => array('max_length' => 256),
			'label' => 'IRC',
			'type'	=> 'input'
		),
                'twitter' => array(
			'rules' => array(),
			'label' => 'Twitter username',
			'type'	=> 'input'
		),
                'facebook' => array(
			'rules' => array(),
			'label' => 'Facebook',
			'type'	=> 'input'
		),
                'facebookid' => array(
			'rules' => array('max_length' => 512),
			'label' => 'Facebook page ID',
			'type'	=> 'input'
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
	function get_open_teams()
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

        public function add_team($name, $url = "", $forum = "", $irc = "", $twitter = "", $facebook = "", $facebookid = "")
        {
            $this->name = $name;
            $this->stub = $name;
            $this->url = $url;
            $this->forum = $forum;
            $this->irc = $irc;
            $this->twitter = $twitter;
            $this->facebook = $facebook;
            $this->facebookid = $facebookid;

            if(!$this->update_team())
            {
                log_message('error', 'add_team: failed adding team');
                return false;
            }

            return true;
        }

        public function update_team($data = array())
        {

            // Check if we're updating or creating a new entry by looking at $data["id"].
            // False is pushed if the ID was not found.
            if(isset($data["id"]))
            {
                $this->where("id", $data["id"])->get();
                if ($this->result_count() == 0)
                {
                    set_notice('error', 'Failed to find the selected team\'s ID.');
                    log_message('error', 'update_team_db: failed to find requested id');
                    return false;
                }
            }
            else // let's set the creator name if it's a new entry
            {
                $this->creator = $this->logged_id();
            }

            // always set the editor name
            $this->editor = $this->logged_id();
			

					
            //
            foreach($data as $key => $value)
            {
                $this->$key = $value;
            }
			
			
			if(!isset($this->stub)) $this->stub = $this->stub();

			// let's save and give some error check. Push false if fail, true if good.
            if (!$this->save())
            {
                if ( ! $this->valid )
                {
                    set_notice('error', 'One or more fields contained the wrong value types.');
                    log_message('error', 'update_team: failed validation');
                } 
                else
                {
                    set_notice('error', 'Failed to update the team in the database for unknown reasons.');
                    log_message('error', 'update_team: failed to save');
                }
                return false;
            }
            else
            {
                return true;
            }

        }

        public function remove_team($also_chapters = FALSE)
        {
            if($this->result_count() != 1)
            {
                set_notice('error', 'Failed to remove the chapter directory. Please, check file permissions.');
                log_message('error', 'remove_team: id not found');
                return false;
            }

            if($also_chapters)
            {
                $chapters = new Chapter();
                $chapters->where("team_id", $this->id)->get();
                foreach($chapters->all as $chapter)
                {
                    if(!$chapter->remove_chapter())
                    {
                        set_notice('error', 'Failed removing the chapters while removing the team.');
                        log_message('error', 'remove_team: failed removing chapter');
                        return false;
                    }
                }
            }

            $joint = new Joint();
            if(!$joint->remove_team_from_all($this->id))
            {
                log_message('error', 'remove_team: failed removing traces of team in joints');
                return false;
            }

            if(!$this->delete())
            {
                set_notice('error', 'Failed to delete the team for unknown reasons.');
                log_message('error', 'remove_team: failed removing team');
                return false;
            }

            return true;
        }


        // this works by inputting an array of names (not stubs)
        public function get_teams_id($array, $create_joint = FALSE)
        {
            if (count($array) < 1)
            {
                set_notice('error', 'There were no groups selected.');
                log_message('error', 'get_groups: input array empty');
                return false;
            }

            if (count($array) == 1)
            {
                $team = new Team();
                $team->where("name", $array[0])->get();
                if($team->result_count() < 1)
                {
                    set_notice('error', 'There\'s no team under this ID.');
                    log_message('error', 'get_groups: team not found');
                    return false;
                }
                $result = array("team_id" => $team->id, "joint_id" => 0);
                return $result;
            }

            if (count($array) > 1)
            {
                $id_array = array();
                foreach($array as $key => $arra)
                {
                    $team = new Team();
                    $team->where('name', $arra[$key])->get();
                    if($team->result_count() < 1)
                    {
                        set_notice('error', 'There\'s no teams under this ID.');
                        log_message('error', 'get_groups: team not found');
                        return false;
                    }
                    $id_array[$key] = $team->id;
                }
                $joint = new Joint();
                if(!$joint->check_joint($id_array) && $create_joint)
                {
                    if(!$joint->add_joint($id_array))
                    {
                        log_message('error', 'get_groups: could not create new joint');
                        return false;
                    }
                }
                return array("team_id" => 0, "joint_id" => $joint->joint_id);
            }
            
            set_notice('error', 'There\'s no group found with this ID.');
            log_message('error', 'get_groups: no case matched');
            return false;
        }



        //////// UNFINISHED!

        public function get_teams_name($team_id, $joint_id = 0)
        {
            if ($joint_id > 0)
            {
                $joint = new Joint();
                $joint->where("id", $joint_id)->get();
                if($joint->result_count() < 1)
                {
                    log_message('error', 'get_teams_name: joint -> joint not found');
                    return false;
                }

                $team->where_related($joint)->get();
                if($team->result_count() < 1)
                {
                    log_message('error', 'get_teams_name: joint -> no teams found');
                    return false;
                }

                return $team->all;
            }

            $team = new Team();
            $team->where("id", $team_id)->get();
            if($team->result_count() < 1)
            {
                log_message('error', 'get_teams_name: team -> team not found');
                return false;
            }
            return array($team);



        }

}

/* End of file team.php */
/* Location: ./application/models/team.php */
