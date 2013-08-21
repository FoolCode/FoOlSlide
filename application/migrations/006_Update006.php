<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update006 extends CI_Migration {

	function up() {
		$this->db->query("
				ALTER TABLE `" . $this->db->dbprefix('comics') . "` ADD `adult` TINYINT( 2 ) NOT NULL AFTER `customchapter`
		");
	}

}
