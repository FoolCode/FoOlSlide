<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update extends CI_Migration {

	function up() {

		$this->db->query(
				"INSERT INTO `" . $this->db->dbprefix('preferences') . "` (`name`, `value`, `group`) VALUES
						('fs_dl_archive_max', 0, 0),
						('fs_cron_autoupgrade_version', 0, 0);"
		);

		$this->db->query("ALTER TABLE `" . $this->db->dbprefix('chapters') . "` ADD `pagesnum` INT NOT NULL AFTER `thumbnail` ,
						ADD `size` INT NOT NULL AFTER `pages` ,
						ADD `compressed` VARCHAR( 140 ) NOT NULL AFTER `size` ,
						ADD `compressedsize` INT NOT NULL AFTER `compressed` ,
						ADD `compressedtime` DATETIME NOT NULL AFTER `compressed` ,
						ADD `dirsize` INT NOT NULL AFTER `compressedsize`"
		);

		$this->db->query("ALTER TABLE `" . $this->db->dbprefix('chapters') . "` ADD INDEX ( `created` )");

		$chapters = new Chapters();
		$chapters->get_iterated();
		foreach ($chapters as $chapter) {
			
		}
	}

}