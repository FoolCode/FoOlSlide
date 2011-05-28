<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Upgrade_model extends CI_Model {

	function __construct() {
		// Call the Model constructor
		parent::__construct();
		$this->pod = 'http://foolrulez.com/pod';
	}

	function check_latest($force = FALSE) {
		$result = $this->curl->simple_get($this->pod . '/api/software/foolslide');
		if (!$result) {
			set_notice('error', _('FoOlPod server could not be contacted: impossible to check for new versions.'));
			return FALSE;
		}
		$data = json_decode($result);
		$latest = $data->versions[0];

		$version = explode('.', get_setting('fs_priv_version'));
		$current->version = $version[0];
		$current->subversion = $version[1];
		$current->subsubversion = $version[2];

		if ($force || $latest->version > $current->version ||
				($latest->version == $current->version && $latest->subversion > $current->subversion) ||
				($latest->version == $current->version && $latest->subversion == $current->subversion && $latest->subsubversion > $current->subsubversion)) {
			return $latest;
		}

		return FALSE;
	}

	function get_file($url) {
		$this->clean();
		$zip = $this->curl->simple_get($url);
		if (!$zip){
			log_message('error', 'upgrade_model get_file(): impossible to get the update from FoOlPod');
			set_notice('error', _('Can\'t get the update file from FoOlPod. It might be a momentary problem. Browse <a href="http://foolrulez.com/pod">http://foolrulez.com/pod</a> to check if it\'s a known issue.'));
			return FALSE;
		}
		@mkdir('content/cache/upgrade');
		write_file('content/cache/upgrade/upgrade.zip', $zip);
		$this->unzip->extract('content/cache/upgrade/upgrade.zip');
	}

	function check_files() {
		if (!is_writable('.')) {
			return FALSE;
		}
		if (!is_writable('index.php')) {
			return FALSE;
		}
		if (!is_writable('application')) {
			return FALSE;
		}
		if (!is_writable('system')) {
			return FALSE;
		}
		if (!is_writable('content')) {
			return FALSE;
		}
		if (!is_writable('assets')) {
			return FALSE;
		}
		if (!is_writable('content/themes')) {
			return FALSE;
		}
		if (!is_writable('content/themes/default')) {
			return FALSE;
		}

		if (!is_writable('content/themes/mobile')) {
			return FALSE;
		}

		if (!is_writable('content/cache')) {
			return FALSE;
		}

		return TRUE;
	}

	function do_upgrade() {
		if (!$this->check_files()) {
			log_message('error', 'upgrade.php:_do_upgrade() check_files() failed');
			return false;
		}

		$latest = $this->upgrade_model->check_latest(TRUE);
		if ($latest === FALSE)
			return FALSE;

		$this->upgrade_model->get_file($latest->download);


		if (!file_exists('content/cache/upgrade')) {
			return FALSE;
		}
		if (!file_exists('content/cache/upgrade/index.php')) {
			return FALSE;
		}
		if (!file_exists('content/cache/upgrade/application')) {
			return FALSE;
		}
		if (!file_exists('content/cache/upgrade/system')) {
			return FALSE;
		}
		if (!file_exists('content/cache/upgrade/assets')) {
			return FALSE;
		}
		if (!file_exists('content/cache/upgrade/content/themes/default')) {
			return FALSE;
		}
		if (!file_exists('content/cache/upgrade/content/themes/mobile')) {
			return FALSE;
		}

		unlink('index.php');
		rename('content/cache/upgrade/index.php', 'index.php');		
		delete_files('application/', TRUE);
		rename('content/cache/upgrade/application', 'application');
		delete_files('system/', TRUE);
		rename('content/cache/upgrade/system', 'system');
		delete_files('assets/', TRUE);
		rename('content/cache/upgrade/assets', 'assets');
		delete_files('content/themes/default/', TRUE);
		rename('content/cache/upgrade/content/themes/default', 'content/themes/default');
		delete_files('content/themes/mobile/', TRUE);
		rename('content/cache/upgrade/content/themes/mobile', 'content/themes/mobile');

		$this->db->update('preferences', array('value' => $latest->version.'.'.$latest->subversion.'.'.$latest->subsubversion), array('name' => 'fs_priv_version'));
		$this->upgrade_model->clean();
	}

	function clean() {
		delete_files('content/cache/upgrade/', TRUE);
	}

}