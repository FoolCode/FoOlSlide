<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update008 extends CI_Migration {

	function up() {

		$this->db->query("
				ALTER TABLE `" . $this->db->dbprefix('comics') . "` ADD `author` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `hidden`
		");
		$this->db->query("
				ALTER TABLE `" . $this->db->dbprefix('comics') . "` ADD `artist` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `author`
		");
	}

}