<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update009 extends CI_Migration {

	function up() {

		$this->db->query("
				ALTER TABLE `" . $this->db->dbprefix('comics') . "` ADD `altname` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `name`
		");
		$this->db->query("
				ALTER TABLE `" . $this->db->dbprefix('comics') . "` ADD `genres` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL AFTER `description`
		");
		$this->db->query("
				ALTER TABLE `" . $this->db->dbprefix('comics') . "` ADD `publisher` ENUM('Weekly Shounen Jump','Weekly Shounen Magazine','Weekly Shounen Sunday','Bessatsu Shounen Magazine','Ulta Jump','Shounen A','Morning','Beans Ace','Shuukan Shounen Champion','Shounen Gangan','Comic Blade','Young King Ours','Jump SQ','Monthly Shounen Magazine','Champion RED','Dengeki Daioh') NOT NULL AFTER `artist`
		");
	}

}