<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Preferences extends Admin_Controller {

	function __construct() {
		parent::__construct();
		$this->ion_auth->logged_in() or redirect('auth/login');
		$this->ion_auth->is_admin() or redirect('admin');
		$this->ion_auth->is_admin() or die(1);
		$this->load->library('form_validation');
		$this->viewdata['controller_title'] = "Preferences";
	}

	function index() {
		redirect('/admin/preferences/general');
	}

	function _submit($post, $form) {
		foreach ($form as $key => $item) {

			if (isset($post[$item[1]['name']]))
				$value = $post[$item[1]['name']];
			else
				$value = NULL;

			$this->db->update('preferences', array('value' => $value), array('name' => $item[1]['name']));
		}

		$CI = & get_instance();
		$array = $CI->db->get('preferences')->result_array();
		$result = array();
		foreach ($array as $item) {
			$result[$item['name']] = $item['value'];
		}
		$CI->fs_options = $result;
	}

	function general() {
		$this->viewdata["function_title"] = "General";


		$form = array();


		$form[] = array(
			'Site title',
			array(
				'type' => 'input',
				'name' => 'fs_gen_site_title',
				'id' => 'site_title',
				'maxlength' => '200',
				'placeholder' => 'manga reader',
				'preferences' => 'fs_gen'
			)
		);

		$form[] = array(
			'Back URL',
			array(
				'type' => 'input',
				'name' => 'fs_gen_back_url',
				'id' => 'back_url',
				'maxlength' => '200',
				'placeholder' => 'http://',
				'preferences' => 'fs_gen'
			)
		);

		$form[] = array(
			'Footer text',
			array(
				'type' => 'textarea',
				'name' => 'fs_gen_footer_text',
				'placeholder' => '',
				'preferences' => 'fs_gen'
			)
		);

		$form[] = array(
			'Default team',
			array(
				'type' => 'input',
				'name' => 'fs_gen_default_team',
				'id' => 'default_team',
				'maxlength' => '200',
				'placeholder' => 'Anonymous',
				'preferences' => 'fs_gen'
			)
		);


		$form[] = array(
			'Default language',
			array(
				'type' => 'language',
				'name' => 'fs_gen_default_lang',
				'preferences' => 'fs_gen'
			)
		);

		$form[] = array(
			'Show Anonymous as team?',
			array(
				'type' => 'checkbox',
				'name' => 'fs_gen_anon_team_show',
				'id' => 'anon_team_show',
				'placeholder' => '',
				'preferences' => 'fs_gen'
			)
		);

		if ($post = $this->input->post()) {
			$this->_submit($post, $form);
		}

		$table = tabler($form, FALSE);

		$data['table'] = $table;


		$this->viewdata["main_content_view"] = $this->load->view("admin/preferences/general.php", $data, TRUE);
		$this->load->view("admin/default.php", $this->viewdata);
	}

	function advertising() {
		$this->viewdata["function_title"] = "Advertising";

		$form = array();


		$form[] = array(
			'Top banner',
			array(
				'type' => 'textarea',
				'name' => 'fs_ads_top_banner',
				'placeholder' => 'Insert the HTML provided by your advertiser',
				'preferences' => 'fs_ads'
			)
		);

		$form[] = array(
			'Reload every pageview? (for ProjectWondeful.com ads)',
			array(
				'type' => 'checkbox',
				'name' => 'fs_ads_top_banner_reload',
				'placeholder' => '',
				'preferences' => 'fs_ads'
			)
		);


		$form[] = array(
			'Active',
			array(
				'type' => 'checkbox',
				'name' => 'fs_ads_top_banner_active',
				'placeholder' => '',
				'preferences' => 'fs_ads'
			)
		);

		$form[] = array(
			'Right banner',
			array(
				'type' => 'textarea',
				'name' => 'fs_ads_right_banner',
				'placeholder' => 'Insert the HTML provided by your advertiser',
				'preferences' => 'fs_ads'
			)
		);

		$form[] = array(
			'Reload every pageview? (for ProjectWondeful.com ads)',
			array(
				'type' => 'checkbox',
				'name' => 'fs_ads_right_banner_reload',
				'placeholder' => '',
				'preferences' => 'fs_ads'
			)
		);


		$form[] = array(
			'Active',
			array(
				'type' => 'checkbox',
				'name' => 'fs_ads_right_banner_active',
				'placeholder' => '',
				'preferences' => 'fs_ads'
			)
		);

		$form[] = array(
			'Top banner',
			array(
				'type' => 'textarea',
				'name' => 'fs_ads_bottom_banner',
				'placeholder' => 'Insert the HTML provided by your advertiser',
				'preferences' => 'fs_ads'
			)
		);

		$form[] = array(
			'Reload every pageview? (for ProjectWondeful.com ads)',
			array(
				'type' => 'checkbox',
				'name' => 'fs_ads_bottom_banner_reload',
				'placeholder' => '',
				'preferences' => 'fs_ads'
			)
		);


		$form[] = array(
			'Active',
			array(
				'type' => 'checkbox',
				'name' => 'fs_ads_bottom_banner_active',
				'placeholder' => '',
				'preferences' => 'fs_ads'
			)
		);

		if ($post = $this->input->post()) {
			$this->_submit($post, $form);

			$ad_before = '<!DOCTYPE html>
						<html>
						  <head>
							<title>FoOlSlide ads</title>
							<style>body{margin:0; padding:0; overflow:hidden;}</style>
							<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
						  </head>
						  <body>';
			$ad_after = '</body>
						</html>';

			$ads = array('fs_ads_top_banner' => 'ads_top.html', 'fs_ads_bottom_banner' => 'ads_bottom.html', 'fs_ads_right_banner' => 'ads_right.html');
			foreach ($ads as $ad => $adfile) {
				if (!write_file('./content/ads/' . $adfile, $ad_before . $this->input->post($ad) . $ad_after)) {
					log_message('error', 'preferences.php/advertising: couldn\'t update HTML files');
					set_notice('error', 'Couldn\'t save the advertising code in the HTML');
				}
			}
		}

		$table = tabler($form, FALSE);

		$data['table'] = $table;


		$this->viewdata["main_content_view"] = $this->load->view("admin/preferences/general.php", $data, TRUE);
		$this->load->view("admin/default.php", $this->viewdata);
	}

	function server() {
		$this->viewdata["function_title"] = "General";

		if ($post = $this->input->post()) {
			$this->_submit($post);
		}

		$form = array();


		$form[] = array(
			'Input on each line the URL of FoOlSlide on the other server.',
			array(
				'type' => 'textarea',
				'name' => 'fs_srv_servers',
				'placeholder' => 'List of servers',
				'preferences' => 'fs_srv'
			)
		);

		$table = tabler($form, FALSE);

		$data['table'] = $table;


		$this->viewdata["main_content_view"] = $this->load->view("admin/preferences/general.php", $data, TRUE);
		$this->load->view("admin/default.php", $this->viewdata);
	}

}