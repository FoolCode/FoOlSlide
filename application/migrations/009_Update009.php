<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update009 extends CI_Migration {

	function up() {
        if (!$this->db->table_exists($this->db->dbprefix('blog_posts')))
		{
			$this->db->query(
					"CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix('posts') . "` (
                                          `id` int(11) NOT NULL AUTO_INCREMENT,
                                          `name` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
                                          `stub` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
                                          `hidden` int(11) NOT NULL,
                                          `description` text COLLATE utf8_unicode_ci NOT NULL,
                                          `created` datetime NOT NULL,
                                          `lastseen` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                                          `updated` datetime NOT NULL,
                                          `creator` int(11) NOT NULL,
                                          `editor` int(11) NOT NULL,
                                          PRIMARY KEY (`id`)
                                        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;"
			);
		}
	}

}
