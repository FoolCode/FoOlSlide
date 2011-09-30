<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class System extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();

// only admins should do this
		$this->tank_auth->is_admin() or redirect('admin');

// we need the upgrade module's functions
		$this->load->model('upgrade_model');

// page title
		$this->viewdata['controller_title'] = '<a href="' . site_url("admin/system") . '">' . _("System") . '</a>';
	}


	/*
	 * A page telling if there's an ugrade available
	 * 
	 * @author Woxxy
	 */
	function index()
	{
		redirect('/admin/system/information');
	}


	function information()
	{
		$this->viewdata["function_title"] = _("Information");

		// get current version from database
		$data["current_version"] = get_setting('fs_priv_version');
		$data["form_title"] = _("Information");

		$this->viewdata["main_content_view"] = $this->load->view("admin/system/information", $data, TRUE);
		$this->load->view("admin/default.php", $this->viewdata);
	}


	function preferences()
	{
		$this->viewdata["function_title"] = _("Preferences");

		$form = array();

		$form[] = array(
			_('Path to ImageMagick'),
			array(
				'type' => 'input',
				'name' => 'fs_serv_imagick_path',
				'placeholder' => '/usr/bin',
				'preferences' => 'fs_gen',
				'help' => sprintf(_('FoOlSlide uses %s via command line to maximize speed. Enter here the location of the "convert" application on your server if it %s automatically.'), '<a href="#" rel="popover-below" title="ImageMagick" data-content="' . _('This is a library used to dynamically create, edit, compose or convert images.') . '">ImageMagick</a>', '<a href="#" rel="popover-below" title="' . _('How to find convert') . '" data-content="' . _('Normally on linux servers you don\'t need to set this variable. In case it\'s not found automatically, try /usr/bin. On Windows, you will need to know where did you install ImageMagick.') . '" >' . _('can\'t be found') . '</a>')
			)
		);


		if ($post = $this->input->post())
		{
			$this->_submit($post, $form);
		}

		// create a form
		$table = tabler($form, FALSE);
		$data['table'] = $table;

		// print out
		$this->viewdata["main_content_view"] = $this->load->view("admin/preferences/general.php", $data, TRUE);


		$data["form_title"] = _("Preferences");

		$this->viewdata["main_content_view"] = $this->load->view("admin/system/preferences", $data, TRUE);
		$this->load->view("admin/default.php", $this->viewdata);
	}


	/*
	 * _submit is a private function that submits to the "preferences" table.
	 * entries that don't exist are created. the preferences table could get very large
	 * but it's not really an issue as long as the variables are kept all different.
	 * 
	 * @author Woxxy
	 */
	function _submit($post, $form)
	{
		// Support Checkbox Listing
		$former = array();
		foreach ($form as $key => $item)
		{
			if (isset($item[1]['value']) && is_array($item[1]['value']))
			{
				foreach ($item[1]['value'] as $key => $item2)
				{
					$former[] = array('1', $item2);
				}
			}
			else
				$former[] = $form[$key];
		}

		foreach ($former as $key => $item)
		{
			if (isset($post[$item[1]['name']]))
				$value = $post[$item[1]['name']];
			else
				$value = NULL;

			$this->db->from('preferences');
			$this->db->where(array('name' => $item[1]['name']));
			if ($this->db->count_all_results() == 1)
			{
				$this->db->update('preferences', array('value' => $value), array('name' => $item[1]['name']));
			}
			else
			{
				$this->db->insert('preferences', array('name' => $item[1]['name'], 'value' => $value));
			}
		}

		$CI = & get_instance();
		$array = $CI->db->get('preferences')->result_array();
		$result = array();
		foreach ($array as $item)
		{
			$result[$item['name']] = $item['value'];
		}
		$CI->fs_options = $result;
		set_notice('notice', _('Updated settings.'));
	}


	function tools()
	{
		$this->viewdata["function_title"] = _("Information");

		// get current version from database
		$data["current_version"] = get_setting('fs_priv_version');
		$data["form_title"] = _("Information");

		$this->viewdata["main_content_view"] = $this->load->view("admin/system/information", $data, TRUE);
		$this->load->view("admin/default.php", $this->viewdata);
	}


	function upgrade()
	{
		$this->viewdata["function_title"] = _("Upgrade FoOlSlide");

		// get current version from database
		$data["current_version"] = get_setting('fs_priv_version');

		// check if the user can upgrade by checking if files are writeable
		$data["can_upgrade"] = $this->upgrade_model->check_files();
		if (!$data["can_upgrade"])
		{
			// if there are not writeable files, suggest the actions to take
			$this->upgrade_model->permissions_suggest();
		}

		// look for the latest version available
		$data["new_versions"] = $this->upgrade_model->check_latest();

		// print out
		$this->viewdata["main_content_view"] = $this->load->view("admin/system/upgrade", $data, TRUE);
		$this->load->view("admin/default.php", $this->viewdata);
	}


	/*
	 * This just triggers the upgrade function in the upgrade model
	 * 
	 * @author Woxxy
	 */
	function do_upgrade()
	{

		if (!isAjax())
		{
			return false;
		}

		// triggers the upgrade
		if (!$this->upgrade_model->do_upgrade())
		{
			// clean the cache in case of failure
			$this->upgrade_model->clean();
			// show some kind of error
			log_message('error', 'system.php do_upgrade(): failed upgrade');
			flash_message('error', _('Upgrade failed: check file permissions.'));
		}

		// return an url
		$this->output->set_output(json_encode(array('href' => site_url('admin/system/upgrade'))));
	}


}