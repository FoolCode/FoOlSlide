<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_Controller extends MY_Controller {

        public function __construct()
	{
            parent::__construct();
            $this->viewdata["sidebar"] = $this->sidebar();
        }

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
                        "level" => "mod",
                        "content" => array(
                                "manage" => array("level" => "mod", "name" => "Manage"),
                                "add_new" => array("level" => "mod","name" => "Add new")
                            )
                        ),
                    "users" => array(
                        "name" => "Users",
                        "level" => "mod",
                         "content" => array(
                               "users" => array("level" => "mod", "name" => "User list"),
                               "teams" => array("level" => "mod", "name" => "Team list"),
                               "home_team" => array("level" => "mod", "name" => "Home team")
                             )
                        ),
                    "preferences" => array(
                        "name" => "Preferences",
                        "level" => "admin",
                         "content" => array(
                               "general" => array("level" => "admin", "name" => "General")
                             )
                        )
                );

        public function sidebar()
        {
            $result = "";
                foreach ($this->sidebar as $key => $item) {
                    if ( ($this->ion_auth->is_admin() || $this->ion_auth->is_group($item["level"]))  &&  !empty($item))
                    {
						$result .= '<div class="collection">';
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
						$result .= '</div>';
                    }


                }
            return $result;

        }





}