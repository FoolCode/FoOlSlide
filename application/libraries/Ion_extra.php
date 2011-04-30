<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:  Ion Extras Model
 *
 * Author:  Ben Edmunds
 * 		   ben.edmunds@gmail.com
 * 	  	   @benedmunds
 *
 * Added Awesomeness: Phil Sturgeon
 *
 * Location: http://github.com/benedmunds/CodeIgniter-Ion-Auth
 *
 * Created:  10.01.2009
 *
 * Description:  Modified auth system based on redux_auth with extensive customization.  This is basically what Redux Auth 2 should be.
 * Original Author name has been kept but that does not mean that the method has not been modified.
 *
 * Requirements: PHP5 or above
 *
 */
class Ion_extra extends CI_Model {

	var $CI;
	
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Hashes the password to be stored in the database.
	 *
	 * @return boolean Whether the user is from team or not
	 * @author Woxxy
	 * */
	public function is_team($team_id = 0, $joint_id = 0) {
		return false;
	}

	/**
	 * Hashes the password to be stored in the database.
	 *
	 * @return boolean Whether the user is team leader or not
	 * @author Woxxy
	 * */
	public function is_team_leader($team_id = 0, $joint_id = 0) {
		return false;
	}

	/**
	 * Hashes the password to be stored in the database.
	 *
	 * @return boolean Whether the user is a moderator or not
	 * @author Woxxy
	 * */
	public function is_mod($team_id = 0, $joint_id = 0) {
		return false;
	}

	/**
	 * Checks is_mod(), is_admin(), is_team_leader(), is_team() and gives
	 * permission if it evaluates true to any of these
	 *
	 * @return boolean Whether the user is from team, administrator, mod or not
	 * @author Woxxy
	 * */
	public function is_allowed($team_id = 0, $joint_id = 0) {
		return $this->ion_auth->is_admin();
	}

}

 /* End of file Ion_extras.php */