<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Joint DataMapper Model
 *
 * Use this basic model as a joint for creating new models.
 * It is not recommended that you include this file with your application,
 * especially if you use a Joint library (as the classes may collide).
 *
 * To use:
 * 1) Copy this file to the lowercase name of your new model.
 * 2) Find-and-replace (case-sensitive) 'Joint' with 'Your_model'
 * 3) Find-and-replace (case-sensitive) 'joint' with 'your_model'
 * 4) Find-and-replace (case-sensitive) 'joints' with 'your_models'
 * 5) Edit the file as desired.
 *
 * @license		MIT License
 * @category            Models
 * @author		Phil DeJarnett
 * @link		http://www.overzealous.com
 */
class Joint extends DataMapper {

	// Uncomment and edit these two if the class has a model name that
	//   doesn't convert properly using the inflector_helper.
	// var $model = 'joint';
	// var $table = 'joints';

	// You can override the database connections with this option
	// var $db_params = 'db_config_name';

	// --------------------------------------------------------------------
	// Relationships
	//   Configure your relationships below
	// --------------------------------------------------------------------

	// Insert related models that Joint can have just one of.
	var $has_one = array();

	// Insert related models that Joint can have more than one of.
	var $has_many = array('team');

	/* Relationship Examples
	 * For normal relationships, simply add the model name to the array:
	 *   $has_one = array('user'); // Joint has one User
	 *
	 * For complex relationships, such as having a Creator and Editor for
	 * Joint, use this form:
	 *   $has_one = array(
	 *   	'creator' => array(
	 *   		'class' => 'user',
	 *   		'other_field' => 'created_joint'
	 *   	)
	 *   );
	 *
	 * Don't forget to add 'created_joint' to User, with class set to
	 * 'joint', and the other_field set to 'creator'!
	 *
	 */

	// --------------------------------------------------------------------
	// Validation
	//   Add validation requirements, such as 'required', for your fields.
	// --------------------------------------------------------------------

	var $validation = array(
		'joint_id' => array(
			'rules' => array('required', 'unique', 'max_length' => 256),
			'label' => 'Name'
		),
                'team_id' => array(
			'rules' => array('required', 'max_length' => 256),
			'label' => 'Stub'
		),
                'creator' => array(
			'rules' => array('max_length' => 256),
			'label' => 'URL'
		),
                'editor' => array(
			'rules' => array('max_length' => 256),
			'label' => 'Forum'
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
	function get_open_joints()
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


        public function check_joint($teams)
        {
            $teams = array_unique($teams);
            $size = count($teams);
            $joints = new Joint();
            $joints->where('team_id', $teams[0])->get_iterated();
            if($joints->result_count() < 1)
            {
                log_message('debug', 'check_joint: joint not found, result count zero');
                return false;
            }

            $found = false;
            foreach($joints as $joint)
            {
                $join = new Joint();
                $join->where('joint_id', $joint->joint_id)->get_iterated();
                if($join->result_count() == $size)
                {
                    $test = $teams;
                    foreach($join as $joi)
                    {
                        if(!$key = array_search($joi->team_id, $teams))
                        {
                            break;
                        }
                        unset($test[$key]);
                    }
                    if(empty($test))
                    {
                        return $joi->joint_id;
                    }
                }
            }
            log_message('debug', 'check_joint: joint not found');
            return false;

        }

		// $teams is an array of names
		public function add_joint_via_name($teams)
		{
			$result = array();
			foreach($teams as $team)
			{
				$tea = new Team();
				$tea->where('name', $team)->get();
				if($tea->result_count() == 0)
				{
					set_notice('error', 'One of the named teams does not exist.');
                    log_message('error', 'add_joint_via_name: team does not exist');
				}
				$result[] = $tea->id;
			}
			$this->add_joint($result);
		}
		
        // $teams is an array of IDs
        public function add_joint($teams)
        {
            if(!$result = $this->check_joint($teams))
            {
                $maxjoint = new Joint();
                $maxjoint->select_max('joint_id')->get();
                $max = $maxjoint->joint_id + 1;

                foreach($teams as $key => $team)
                {
                    $joint = new Joint();
                    $joint->joint_id = $max;
                    $joint->team_id = $team;
                    $joint->creator = $this->logged_id();
                    $joint->editor = $this->logged_id();
                    if(!$joint->save())
                    {
                        if($joint->valid)
                        {
                            set_notice('error', 'One or more fields had wrong value types.');
                            log_message('error', 'add_joint: validation failed');
                        }
                        else
                        {
                            set_notice('error', 'Couldn\'t save Joint to database due to an unknown error.');
                            log_message('error', 'add_joint: saving failed');
                        }
                        return false;
                    }
                }
                return $max;

            }
            return $result;
        }

        public function remove_joint()
        {
            if(!$this->delete_all())
            {
                set_notice('error', 'The joint couldn\'t be removed.');
                log_message('error', 'remove_joint: failed deleting');
                return false;
            }
            return true;
        }

        public function add_team($team_id)
        {
            $joint = new Joint();
            $joint->team_id = $team_id;
            $joint->joint_id = $this->joint_id;
            $joint->creator = $this->logged_id();
            $joint->editor = $this->logged_id();
            if(!$joint->save())
            {
                if($joint->valid)
                {
                    set_notice('error', 'One or more fields had wrongly inpiutted data.');
                    log_message('error', 'add_team (joint.php): validation failed');
                }
                else
                {
                    set_notice('error', 'Couldn\'t add team to joint for unknown reasons.');
                    log_message('error', 'add_team (joint.php): saving failed');
                }
                return false;
            }
        }

        public function remove_team($team_id)
        {
            $this->where('team_id', $team_id)->get();
            if (!$this->delete())
            {
                set_notice('error', 'Couldn\'t remove the team from the joint.');
                log_message('error', 'remove_team (joint.php): removing failed');
                return false;
            }
        }

        public function remove_team_from_all($team_id)
        {
            $joints = new Joint();
            $joints->where('team_id', $team_id)->get();
            if (!$joints->delete_all())
            {
                set_notice('error', 'Couldn\'t remove the team from all the joints.');
                log_message('error', 'remove_team_from_all (joint.php): removing failed');
                return false;
            }
        }

}

/* End of file joint.php */
/* Location: ./application/models/joint.php */
