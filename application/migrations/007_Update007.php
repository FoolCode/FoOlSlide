<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update007 extends CI_Migration {

	function up() {

		$this->db->query("
				ALTER TABLE `" . $this->db->dbprefix('comics') . "` ADD `status` ENUM('Ongoing','Dropped','Completed') NOT NULL AFTER `hidden`
		");
	}

}