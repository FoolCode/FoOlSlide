<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Preferences extends Admin_Controller {

	function __construct() {
		parent::__construct();
		$this->tank_auth->is_logged_in() or redirect('/admin/auth/login');
		$this->tank_auth->is_admin() or redirect('admin');
		$this->tank_auth->is_admin() or die(1);
		$this->viewdata['controller_title'] = _("Preferences");
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
		set_notice('notice', _('Settings changed.'));
	}

	function general() {
		$this->viewdata["function_title"] = _("General");


		$form = array();


		$form[] = array(
			_('Site title'),
			array(
				'type' => 'input',
				'name' => 'fs_gen_site_title',
				'id' => 'site_title',
				'maxlength' => '200',
				'placeholder' => _('comic reader'),
				'preferences' => 'fs_gen'
			)
		);

		$form[] = array(
			_('Back URL'),
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
			_('Footer text'),
			array(
				'type' => 'textarea',
				'name' => 'fs_gen_footer_text',
				'placeholder' => '',
				'preferences' => 'fs_gen'
			)
		);

		$form[] = array(
			_('Default team'),
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
			_('Default language'),
			array(
				'type' => 'language',
				'name' => 'fs_gen_default_lang',
				'preferences' => 'fs_gen'
			)
		);

		$form[] = array(
			_('Show Anonymous as team?'),
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
		$this->viewdata["function_title"] = _("Advertising");

		$form = array();


		$form[] = array(
			_('Top banner'),
			array(
				'type' => 'textarea',
				'name' => 'fs_ads_top_banner',
				'help' => _('Insert the HTML provided by your advertiser'),
				'preferences' => 'fs_ads'
			)
		);

		$form[] = array(
			_('Reload every pageview?'),
			array(
				'type' => 'checkbox',
				'name' => 'fs_ads_top_banner_reload',
				'placeholder' => '',
				'preferences' => 'fs_ads',
				'help' => _('Reload the advertising. Useful for ProjectWonderful.com. Use it without violating the TOS of your advertiser.')
			)
		);


		$form[] = array(
			_('Active'),
			array(
				'type' => 'checkbox',
				'name' => 'fs_ads_top_banner_active',
				'placeholder' => '',
				'preferences' => 'fs_ads'
			)
		);

		$form[] = array(
			_('Right banner'),
			array(
				'type' => 'textarea',
				'name' => 'fs_ads_left_banner',
				'help' => _('Insert the HTML provided by your advertiser'),
				'preferences' => 'fs_ads'
			)
		);

		$form[] = array(
			_('Reload every pageview?'),
			array(
				'type' => 'checkbox',
				'name' => 'fs_ads_left_banner_reload',
				'placeholder' => '',
				'preferences' => 'fs_ads',
				'help' => _('Reload the advertising. Useful for ProjectWonderful.com. Use it without violating the TOS of your advertiser.')
			)
		);


		$form[] = array(
			_('Active'),
			array(
				'type' => 'checkbox',
				'name' => 'fs_ads_left_banner_active',
				'placeholder' => '',
				'preferences' => 'fs_ads'
			)
		);

		$form[] = array(
			_('Bottom banner'),
			array(
				'type' => 'textarea',
				'name' => 'fs_ads_bottom_banner',
				'help' => _('Insert the HTML provided by your advertiser'),
				'preferences' => 'fs_ads'
			)
		);

		$form[] = array(
			_('Reload every pageview?'),
			array(
				'type' => 'checkbox',
				'name' => 'fs_ads_bottom_banner_reload',
				'placeholder' => '',
				'preferences' => 'fs_ads',
				'help' => _('Reload the advertising. Useful for ProjectWonderful.com. Use it without violating the TOS of your advertiser.')
			)
		);


		$form[] = array(
			_('Active'),
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

			$ads = array('fs_ads_top_banner' => 'ads_top.html', 'fs_ads_bottom_banner' => 'ads_bottom.html', 'fs_ads_left_banner' => 'ads_left.html');
			foreach ($ads as $ad => $adfile) {
				if (!write_file('./content/ads/' . $adfile, $ad_before . $this->input->post($ad) . $ad_after)) {
					log_message('error', 'preferences.php/advertising: couldn\'t update HTML files');
					set_notice('error', _('Couldn\'t save the advertising code in the HTML'));
				}
			}
		}

		$table = tabler($form, FALSE);

		$data['table'] = $table;


		$this->viewdata["main_content_view"] = $this->load->view("admin/preferences/general.php", $data, TRUE);
		$this->load->view("admin/default.php", $this->viewdata);
	}
	
	function registration() {
		$this->viewdata["function_title"] = _("Registration");


		$form = array();


		$form[] = array(
			_('Disable registration'),
			array(
				'type' => 'checkbox',
				'name' => 'fs_reg_disabled',
				'id' => 'disable_reg',
				'preferences' => 'fs_reg',
				'help' => _('Disable registration in case you\'re not expecting any')
			)
		);
		
		$form[] = array(
			_('Disable activation email'),
			array(
				'type' => 'checkbox',
				'name' => 'fs_reg_email_disabled',
				'id' => 'disable_reg',
				'preferences' => 'fs_reg',
				'help' => _('Disable the necessity to proceed with an activation after registering')
			)
		);

		$form[] = array(
			_('reCaptcha public key'),
			array(
				'type' => 'input',
				'name' => 'fs_reg_recaptcha_public',
				'id' => 'captcha_public',
				'maxlength' => '200',
				'preferences' => 'fs_reg',
				'help' => _('Use <a href="http://www.google.com/recaptcha">reCaptcha</a> service, avoid hellish captchas!')
			)
		);

		$form[] = array(
			_('reCaptcha secret key'),
			array(
				'type' => 'input',
				'name' => 'fs_reg_recaptcha_secret',
				'preferences' => 'fs_reg',
				'help' => _('You must insert both public and secret key if you want to use reCaptcha')
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
	
	function reader() {
		$this->viewdata["function_title"] = _("Reading");


		$form = array();


		$form[] = array(
			_('Enable direct downloads'),
			array(
				'type' => 'checkbox',
				'name' => 'fs_dl_enabled',
				'id' => 'enable_dl',
				'preferences' => 'fs_dl',
				'help' => _('Direct downloads usually increase the bandwidth usage by one-third. The issue is wheter you have enough space to keep both images and ZIPs. FoOlSlide tries to avoid dealing with the problem by using on-the-fly ZIP compression and caching.')
			)
		);

		$form[] = array(
			_('Maximum direct download cache in Megabyte'),
			array(
				'type' => 'input',
				'name' => 'fs_dl_archive_max',
				'id' => 'max_dl',
				'preferences' => 'fs_dl',
				'help' => _('Over this size, the ZIPs that have been downloaded the furthest in time will be removed. If you insert a very large number, the ZIPs will be cached indefinitely.')
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

	function server() {
		show_404();
		$this->viewdata["function_title"] = _("Server");

		if ($post = $this->input->post()) {
			$this->_submit($post);
		}

		$form = array();


		$form[] = array(
			_('Input on each line the URL of FoOlSlide on the other server.'),
			array(
				'type' => 'textarea',
				'name' => 'fs_srv_servers',
				'placeholder' => _('List of servers'),
				'preferences' => 'fs_srv'
			)
		);

		$table = tabler($form, FALSE);

		$data['table'] = $table;


		$this->viewdata["main_content_view"] = $this->load->view("admin/preferences/general.php", $data, TRUE);
		$this->load->view("admin/default.php", $this->viewdata);
	}

}