<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Status extends REST_Controller
{
	
	function status_get()
	{
		$result = array();
		$result["title"] = get_setting('fs_gen_site_title');
		$result["version"] = get_setting('fs_priv_version');
		$this->response($result, 200); // 200 being the HTTP response code
	}
	
	function cron_get()
	{
			$this->response(array('success' => _('Cron triggered')), 200);
	}
}