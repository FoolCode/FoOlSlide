<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update008 extends CI_Migration {

	function up() {
		$this->db->query("
				ALTER TABLE `" . $this->db->dbprefix('archives') . "`
					ADD `comic_id` INT( 11 ) NOT NULL AFTER `id`,
					ADD `volume_id` INT( 11 ) NOT NULL AFTER `comic_id`
		");

		$this->db->query("
				ALTER TABLE `" . $this->db->dbprefix('comics') . "`
					ADD COLUMN `author` VARCHAR(512) NOT NULL AFTER `hidden`,
					ADD COLUMN `artist` VARCHAR(512) NOT NULL AFTER `author`;
		");
	}

}
