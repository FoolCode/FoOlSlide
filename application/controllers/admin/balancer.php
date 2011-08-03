<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Balancer extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();

		// preferences are settable only by admins!
		$this->tank_auth->is_admin() or redirect('admin');

		// title on top
		$this->viewdata['controller_title'] = _("Load balancer");
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
		foreach ($form as $key => $item)
		{

			if (isset($post[$item[1]['name']]))
				$value = $post[$item[1]['name']];
			else
				$value = NULL;

			$this->db->from('preferences');
			$this->db->where(array('name' => $item[1]['name']));
			if (is_array($value))
			{
				foreach ($value as $key => $val)
				{
					if ($value[$key] == "")
						unset($value[$key]);
				}
				$value = serialize($value);
			}
			if ($this->db->count_all_results() == 1)
			{
				$this->db->update('preferences', array('value' => $value), array('name' => $item[1]['name']));
			}
			else
			{
				$this->db->insert('preferences', array('name' => $item[1]['name'], 'value' => $value));
			}
		}


		load_settings();

		set_notice('notice', _('Settings changed.'));
	}


	/*
	 * Allows turning FoOlSlide into a load balancing clone
	 * 
	 * @author Woxxy
	 */
	function balancers($id = NULL)
	{
		if (is_null($id))
		{
			$this->viewdata["function_title"] = _("Master");

			// create a form
			$balancers = new Loadbalancer();
			$balancers->get();
			$data["balancers"] = $balancers;

			// print out
			$this->viewdata["main_content_view"] = $this->load->view("admin/loadbalancer/balancers_list.php", $data, TRUE);
			$this->load->view("admin/default.php", $this->viewdata);
		}
		else
		{
			$balancer = new Loadbalancer($id);
			if($balancer->result_count() != 1)
			{
				show_404();
			}
			$data["balancer"] = $balancer;
			$table = ormer($balancer);
			$data["table"] = tabler($table, FALSE, TRUE, TRUE);

			
			$this->viewdata["main_content_view"] = $this->load->view("admin/loadbalancer/balancer.php", $data, TRUE);
			$this->load->view("admin/default.php", $this->viewdata);
		}
	}


	function balancer_add()
	{
		$this->viewdata["function_title"] = _("Add new");

		if ($this->input->post())
		{
			$loadbalancer = new Loadbalancer();
			if (!$loadbalancer->from_array($this->input->post(), array('url', 'ip', 'key'), TRUE))
			{
				if (!$loadbalancer->valid)
				{
					set_notice('error', _("The values you submitted aren't matching the requested. Check the fields."));
				}
				set_notice('error', _("Couldn't create the new entry"));
			}
			else
			{
				redirect('/admin/loadbalancer/balancers/' . $loadbalancer->id);
			}
		}

		$loadbalancer = new Loadbalancer();
		$table = ormer($loadbalancer);
		$data["table"] = tabler($table, FALSE, TRUE, TRUE);

		$this->viewdata["main_content_view"] = $this->load->view("admin/loadbalancer/add_new.php", $data, TRUE);
		$this->load->view("admin/default.php", $this->viewdata);
	}
	
	
	function balancer_remove($id)
	{
		if(!isAjax())
		{
			show_404();
		}
		$balancer = new Loadbalancer($id);
		if ($balancer->result_count() != 1)
		{
			show_404();
		}
		$balancer->delete();
		echo json_encode(array('href' => site_url('/admin/balancer/balancers/')));
	}


	/*
	 * Allows turning FoOlSlide into a load balancing clone
	 * 
	 * @author Woxxy
	 */
	function client()
	{
		$this->viewdata["function_title"] = _("Client");


		$form = array();

		// build the array for the form
		$form[] = array(
			_('URL to master FoOlSlide root'),
			array(
				'type' => 'input',
				'name' => 'fs_balancer_master_url',
				'id' => 'site_title',
				'placeholder' => 'http://',
				'preferences' => 'fs_gen',
				'help' => _('Turns this FoOlSlide in a load balancer. You can activate this only if there\'s no comics in your FoOlSlide. Every function of this FoOlSlide will be disabled and it will work silently to duplicate the data.')
			)
		);

		$form[] = array(
			_('Security key'),
			array(
				'type' => 'input',
				'name' => 'fs_balancer_master_key_on_client',
				'id' => 'site_title',
				'placeholder' => _('required'),
				'preferences' => 'fs_gen',
				'help' => _('Used to pair client with master')
			)
		);

		$form[] = array(
			_('Allow autoupdating'),
			array(
				'type' => 'checkbox',
				'name' => 'fs_balancer_master_key',
				'id' => 'site_title',
				'placeholder' => _('required'),
				'preferences' => 'fs_gen',
				'help' => _('Updates itself to the same version as the master FoOlSlide.')
			)
		);

		if ($post = $this->input->post())
		{
			$this->_submit($post, $form);
		}

		if (!get_setting('fs_balancer_client_key'))
		{
			$security_key = md5(time() . uniqid());
			$this->_submit(array('fs_balancer_client_key' => $security_key), array(array("", array('name' => 'fs_balancer_client_key'))));
		}

		if ($post = $this->input->post())
		{
			$this->_submit($post, $form);
		}

		$form[] = array(
			_('Security key'),
			get_setting('fs_balancer_client_key')
		);

		// create a form
		$table = tabler($form, FALSE);
		$data['table'] = $table;

		// print out
		$this->viewdata["main_content_view"] = $this->load->view("admin/preferences/general.php", $data, TRUE);
		$this->load->view("admin/default.php", $this->viewdata);
	}


}