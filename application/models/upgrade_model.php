<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Upgrade_model extends CI_Model {

	function __construct() {
		// Call the Model constructor
		parent::__construct();
		$this->pod = 'http://foolrulez.com/pod';
	}

	/**
	 * Connects to FoOlPod to retrieve which is the latest version from the API
	 * 
	 * @param type $force forces returning the download even if FoOlSlide is up to date
	 * @return type FALSE or the download URL
	 */
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

	/**
	 *
	 * @author Woxxy
	 * @param string $url
	 * @return bool 
	 */
	function get_file($url) {
		$this->clean();
		$zip = $this->curl->simple_get($url);
		if (!$zip) {
			log_message('error', 'upgrade_model get_file(): impossible to get the update from FoOlPod');
			set_notice('error', _('Can\'t get the update file from FoOlPod. It might be a momentary problem. Browse <a href="http://foolrulez.com/pod">http://foolrulez.com/pod</a> to check if it\'s a known issue.'));
			return FALSE;
		}
		if (!is_dir('content/cache/upgrade'))
			mkdir('content/cache/upgrade');
		write_file('content/cache/upgrade/upgrade.zip', $zip);
		$this->unzip->extract('content/cache/upgrade/upgrade.zip');
		return TRUE;
	}

	/**
	 * Checks files permissions before upgrading
	 * 
	 * @author Woxxy
	 * @return bool 
	 */
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

		if (!is_writable('application/models/upgrade2_model.php')) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Hi, I herd you liek upgrading, so I put an update for your upgrade, so you
	 * can update the upgrade before upgrading.
	 * 
	 * @author Woxxy
	 * @return bool 
	 */
	function update_upgrade() {
		if (!file_exists('content/cache/update/application/models/upgrade2_model.php')) {
			return FALSE;
		}
		unlink('application/models/upgrade2_model.php');
		rename('content/cache/upgrade/application/models/upgrade2_model.php', 'application/models/upgrade2_model.php');

		return TRUE;
	}

	/**
	 * Does further checking, updates the upgrade2 "stage 2" file to accomodate
	 * changes to the upgrade script, updates the version number with the one
	 * from FoOlPod, and cleans up.
	 *
	 * @author Woxxy
	 * @return bool 
	 */
	function do_upgrade() {
		if (!$this->check_files()) {
			log_message('error', 'upgrade.php:_do_upgrade() check_files() failed');
			return false;
		}

		$latest = $this->upgrade_model->check_latest(TRUE);
		if ($latest === FALSE)
			return FALSE;

		$this->upgrade_model->get_file($latest->download);
		$this->upgrade_model->update_upgrade();


		$this->load->model('upgrade2_model');
		if (!$this->upgrade2_model->do_upgrade()) {
			return FALSE;
		}

		$this->db->update('preferences', array('value' => $latest->version . '.' . $latest->subversion . '.' . $latest->subsubversion), array('name' => 'fs_priv_version'));
		$this->upgrade_model->clean();
		
		return TRUE;
	}

	/**
	 * Cleans up the upgrade folder
	 * 
	 * @author Woxxy
	 */
	function clean() {
		delete_files('content/cache/upgrade/', TRUE);
	}

}