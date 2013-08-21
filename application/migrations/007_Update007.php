<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update007 extends CI_Migration {

	function up() {
		$this->db->query("
				ALTER TABLE `" . $this->db->dbprefix('comics') . "`
					ADD `format` TINYINT( 1 ) NOT NULL AFTER `customchapter`
		");

		$this->db->query("
				ALTER TABLE `" . $this->db->dbprefix('pages') . "`
					DROP COLUMN `description`,
					DROP COLUMN `thumbnail`,
					DROP COLUMN `grayscale`,
					DROP COLUMN `thumbheight`,
					DROP COLUMN `thumbwidth`,
					DROP COLUMN `thumbsize`
		");
	}

}
