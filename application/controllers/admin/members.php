<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Members extends Admin_Controller {

	function __construct() {
		parent::__construct();
		$this->tank_auth->is_logged_in() or redirect('auth/login');
		// 
		$this->viewdata['controller_title'] = "Members";
	}

	function index() {
		redirect('/admin/members/you');
	}

	function member($id) {
		if (!is_numeric($id))
			return false;

		if ($this->tank_auth->is_admin() || $this->tank_auth->is_group('mod'))
			$can_edit = true;
		else
			$can_edit = false;

		if ($this->input->post() && $can_edit) {
			$profile = new Profile($id);
			$profile->from_array($this->input->post(), array('display_name', 'twitter', 'bio'), TRUE);
		}

		$this->viewdata["function_title"] = _("Member");

		$user = new User($id);
		$table = ormer($user);
		$table = tabler($table, TRUE, $can_edit);
		$data['table'] = $table;

		$group = array();
		$group[] = array(
			_('Group'),
			array(
				'type' => 'group',
				'name' => 'group',
			)
		);

		$data['group'] = tabler($group, TRUE, $can_edit);
		$data['can_edit'] = $can_edit;
		$this->viewdata["main_content_view"] = $this->load->view('auth/user', $data, TRUE);
		$this->load->view("admin/default", $this->viewdata);
	}

	function membersa($page = 1) {
		if ($this->tank_auth->is_admin() || $this->tank_auth->is_group('mod'))
			$can_edit = true;
		else
			$can_edit = false;

		$this->viewdata["function_title"] = "Members list";

		$users = new User();
		if ($this->input->post()) {
			$users->ilike('username', $this->input->post('search'));
			$this->viewdata['extra_title'][] = _('Searching') . " : " . $this->input->post('search');
		}

		$users->get_paged($page, 20);

		$users_arr = array();
		foreach ($users->all as $item) {
			$form[$key][] = '<a href="' . site_url('/admin/members/member/' . $item->id) . '">' . $item->username . '</a>';
			if ($can_edit)
				$form[$key][] = $item->email;
			$form[$key][] = $item->last_login;
		}

		$data['table'] = tabler($form, TRUE, FALSE);

		$this->viewdata["main_content_view"] = $this->load->view('auth/member_list', $data, TRUE);
		$this->load->view("admin/default", $this->viewdata);
	}

	function you() {
		if ($this->input->post()) {
			$profile = new Profile($this->tank_auth->get_user_id());
			$profile->from_array($this->input->post(), array('display_name', 'twitter', 'bio'), TRUE);
		}

		$this->viewdata["function_title"] = $this->tank_auth->get_username() . " (" . _("That's you!") . ")";

		$user = new User($this->tank_auth->get_user_id());

		$profile = new Profile($this->tank_auth->get_user_id());

		$table = ormer($user);
		$table = tabler($table, TRUE, FALSE);
		$data['table'] = $table;


		$group = ormer($profile);

		$data['user'] = $user;

		$this->viewdata["main_content_view"] = $this->load->view('auth/you', $data, TRUE);
		$this->load->view("admin/default", $this->viewdata);
	}

	function teams($stub = "") {
		if ($stub == "") {
			$this->viewdata["function_title"] = "Team list";
			$teams = new Team();
			$teams->get_iterated();
			$rows = array();
			foreach ($teams as $team) {
				$rows[] = array('title' => '<a href="' . site_url('admin/members/teams/' . $team->stub) . '">' . $team->name . '</a>');
			}
			$data['list'] = lister($rows);
			$this->viewdata["main_content_view"] = $this->load->view('admin/members/users', $data, TRUE);
			$this->load->view("admin/default", $this->viewdata);
		}
		else {
			$team = new Team();
			$team->where('stub', $stub)->get();

			if ($this->tank_auth->is_admin() || $this->tank_auth->is_group('mod'))
				$can_edit = true;
			else
				$can_edit = false;

			if ($this->tank_auth->is_team_leader($team->id) && !$can_edit)
				$can_edit_limited = true;
			else
				$can_edit_limited = true;

			if (($post = $this->input->post()) && ($can_edit || $can_edit_limited)) {
				$team = new Team();
				$team->where('stub', $stub)->get();
				$post["id"] = $team->id;
				if ($can_edit_limited) {
					unset($post['name']);
				}
				$team->update_team($post, TRUE);
				set_notice('notice', _('Saved.'));
			}


			$this->viewdata["function_title"] = "Team";
			$this->viewdata["extra_title"][] = $team->name;

			$team->validation['name']['display'] = 'hidden';
			
			$result = ormer($team);

			$result = tabler($result, TRUE, ($can_edit || $can_edit_limited));
			$data['table'] = $result;
			$data['team'] = $team;

			$members = new Membership();
			$members->where('team_id', $team->id)->get();
			$data['members'] = $members;

			$this->viewdata["main_content_view"] = $this->load->view('admin/members/team', $data, TRUE);
			$this->load->view("admin/default", $this->viewdata);
		}
	}

	function home_team() {
		$team = new Team();
		$team->where('name', get_setting('fs_gen_default_team'))->get();
		redirect('/admin/members/teams/' . $team->stub);
	}

	function add_team() {

		if ($post = $this->input->post()) {
			$team = new Team();
			$team->update_team($this->input->post());
			redirect('/admin/members/teams/' . $team->stub);
		}

		$team = new Team();

		$this->viewdata["function_title"] = "Team";
		$this->viewdata["extra_title"][] = 'New';

		$result = ormer($team);
		$result = tabler($result, FALSE, TRUE);
		$data['table'] = $result;
		$this->viewdata["main_content_view"] = $this->load->view('admin/form', $data, TRUE);
		$this->load->view("admin/default", $this->viewdata);
	}

	function apply_team($team_stub) {
		$this->viewdata["function_title"] = "Applying to team...";
		$team = new Team();
		$team->where('stub', $team_stub)->limit(1)->get();
		if ($team->result_count() != 1)
			return false;

		$member = new Membership();
		$member->apply($team->id, $this->tank_auth->get_user_id());
		flash_notice('notice', 'You have applied for membership in this team. Come later to check the status of your application.');
		redirect('/admin/members/teams/' . $team->stub);
	}

}