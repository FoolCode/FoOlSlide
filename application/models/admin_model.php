<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_model extends CI_Model {


        public $sidebar = array(
                    "dashboard" => array(
                        "name" => "Dashboard",
                        "level" => "members",
                        "content" => array(
                                "index" => array("level" => "members", "name" => "Dashboard"),
                            )
                        ),
                    "comics" => array(
                        "name" => "Comics",
                        "level" => "team",
                        "content" => array(
                                "manage" => array("level" => "team", "name" => "Manage"),
                                "add_new" => array("level" => "team","name" => "Add new")
                            )
                        ),
                    "preferences" => array(
                        "name" => "Preferences",
                        "level" => "team",
                         "content" => array(
                               "general" => array("level" => "admin", "name" => "General")
                             )
                        )
                );



	public function __construct()
	{
		parent::__construct();
        }

        public function sidebar()
        {
            $result = "";
                foreach ($this->sidebar as $key => $item) {
                    if ( ($this->ion_auth->is_admin() || $this->ion_auth->is_group($item["level"]))  &&  !empty($item))
                    {
			$result .= '<div class="group">'.$item["name"].'</div>';
			foreach ($item["content"] as $subkey => $subitem)
			{
                                if ( ($this->ion_auth->is_admin() || $this->ion_auth->is_group($subitem["level"])) )
				{
				//if($subitem["name"] == $_GET["location"]) $is_active = " active"; else $is_active = "";
                                $is_active = "";
                                    $result .= '<a href="'.site_url(array("admin", $key, $subkey)).'"><div class="element'.$is_active.'">'.$subitem["name"].'</div></a>';
				}
			} 
                    }
            

                }
            return $result;

        }





}