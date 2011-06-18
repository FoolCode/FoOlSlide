<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Members extends Admin_Controller {

	function __construct() {
		parent::__construct();
		$this->tank_auth->is_logged_in() or redirect('/admin/auth/login');
		$this->viewdata['controller_title'] = "Members";
	}

	function index() {
		redirect('/admin/members/you');
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
		foreach ($users->all as $key => $item) {
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

		$this->viewdata["extra_title"][] = $this->tank_auth->get_username() . " (" . _("That's you!") . ")";

		return $this->member($this->tank_auth->get_user_id());
	}

	function member($id) {
		if (!is_numeric($id))
			return false;

		if ($this->tank_auth->get_user_id() == $id && $this->uri->segment(3) != 'you')
			redirect('/admin/members/you/');

		if ($this->tank_auth->is_admin() || $this->tank_auth->is_group('mod'))
			$can_edit = true;
		else
			$can_edit = false;

		if ($this->tank_auth->get_user_id() == $id)
			$can_edit_limited = true;
		else
			$can_edit_limited = false;

		if ($this->input->post() && ( $can_edit || $can_edit_limited)) {
			$profile = new Profile($id); 
			$profile->from_array($this->input->post(), array('display_name', 'twitter', 'bio'), TRUE);
		}

		$this->viewdata["function_title"] = _("Member");

		$user = new User($id);
		if ($user->result_count() == 0)
			return false;
		
		if($this->tank_auth->is_allowed())
		{
			$table = ormer($user);
			//$table = tabler($table, TRUE, $can_edit); not even admins should edit
			$table = tabler($table, TRUE, FALSE);
			$data['table'] = $table;
		}
		else {
			$data["table"] = "";
		}


		$data['user'] = $user;

		$profile = new Profile();
		$profile->where('user_id', $id)->get();
		$profile_table = ormer($profile);
		$data['profile'] = tabler($profile_table, TRUE, $can_edit);
		$data['can_edit'] = $can_edit;
		$this->viewdata["main_content_view"] = $this->load->view('auth/user', $data, TRUE);
		$this->load->view("admin/default", $this->viewdata);
	}

	function teams($stub = "") {
		if ($stub == "") {
			$this->viewdata["function_title"] = "Team list";
			$teams = new Team();
			$teams->order_by('name', 'ASC')->get_iterated();
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
				$can_edit_limited = false;

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

			if ($can_edit_limited)
				$team->validation['name']['disabled'] = 'true';

			$result = ormer($team);

			$result = tabler($result, TRUE, ($can_edit || $can_edit_limited));
			$data['table'] = $result;
			$data['team'] = $team;

			$members = new Membership();
			$users = $members->get_members($team->id);

			$users_arr = array();
			foreach ($users->all as $key => $item) {
				$users_arr[$key][] = '<a href="' . site_url('/admin/members/member/' . $item->id) . '">' . $item->username . '</a>';
				if ($can_edit)
					$users_arr[$key][] = $item->email;
				$users_arr[$key][] = $item->last_login;
				$users_arr[$key][] = ($item->is_leader) ? _('Leader') : _('Member');
				if ($this->tank_auth->is_team_leader($team->id) || $this->tank_auth->is_allowed()) {
					$buttoner = array();
					$buttoner = array(
						'text' => _("Remove member"),
						'href' => site_url('/admin/members/reject_application/' . $team->id . '/' . $item->id),
						'plug' => _('Do you want to remove this team member?')
					);
				}
				$users_arr[$key][] = (isset($buttoner) && !empty($buttoner)) ? buttoner($buttoner) : '';
				if (!$item->is_leader && ($this->tank_auth->is_team_leader($team->id) || $this->tank_auth->is_allowed())) {
					$buttoner = array();
					$buttoner = array(
						'text' => _("Make leader"),
						'href' => site_url('/admin/members/make_team_leader/' . $team->id . '/' . $item->id),
						'plug' => _('Do you want to make this user a team leader?')
					);
				}
				if ($item->is_leader && ($this->tank_auth->is_team_leader($team->id) || $this->tank_auth->is_allowed())) {
					$buttoner = array();
					$buttoner = array(
						'text' => _("Remove leader"),
						'href' => site_url('/admin/members/remove_team_leader/' . $team->id . '/' . $item->id),
						'plug' => _('Do you want to remove this user from the team leadership?')
					);
				}
				$users_arr[$key][] = (isset($buttoner) && !empty($buttoner)) ? buttoner($buttoner) : '';
			}

			// Spawn the form for adding a team leader
			$data["no_leader"] = FALSE;
			if ($this->tank_auth->is_allowed())
				$data["no_leader"] = TRUE;

			$data['members'] = tabler($users_arr, TRUE, FALSE);

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

	function apply_team($team_id) {
		if (!isAjax()) {
			return false;
		}
		$this->viewdata["function_title"] = "Applying to team...";
		$team = new Team($team_id);
		if ($team->result_count() != 1)
			return false;

		$member = new Membership();
		$member->apply($team->id, $this->tank_auth->get_user_id());
		flash_notice('notice', _('You have applied for membership in this team. Come later to check the status of your application.'));
		echo json_encode(array('href' => site_url('/admin/members/teams/' . $team->stub)));
	}

	function accept_application($team_id, $user_id = NULL) {
		if (!isAjax()) {
			return false;
		}

		$this->viewdata["function_title"] = _("Accepting into team...");
		$member = new Membership();
		if (!$member->accept_application($team_id, $user_id)) {
			return FALSE;
		}
		flash_notice('notice', _('User accepted into the team.'));
		$team = new Team($team_id);
		echo json_encode(array('href' => site_url('/admin/members/teams/' . $team->stub)));
	}

	function reject_application($team_id, $user_id = NULL) {
		if (!isAjax()) {
			return false;
		}

		$this->viewdata["function_title"] = _("Removing from team...");
		$member = new Membership();
		if (!$member->reject_application($team_id, $user_id)) {
			return FALSE;
		}
		flash_notice('notice', _('User removed from the team.'));
		$team = new Team($team_id);
		echo json_encode(array('href' => site_url('/admin/members/teams/' . $team->stub)));
	}

	function make_team_leader($team_id, $user_id) {
		if (!isAjax()) {
			return false;
		}
		if (!$this->tank_auth->is_team_leader($team_id) && !$this->tank_auth->is_allowed())
			return false;
		$this->viewdata["function_title"] = "Making team leader...";
		$member = new Membership();
		$member->make_team_leader($team_id, $user_id);
		flash_notice('notice', _('You have made the user a team leader.'));
		$team = new Team($team_id);
		echo json_encode(array('href' => site_url('/admin/members/teams/' . $team->stub)));
	}

	function make_team_leader_username($team_id) {
		if (!$this->tank_auth->is_team_leader($team_id) && !$this->tank_auth->is_allowed())
			return false;
		$team = new Team($team_id);
		$user = new User();
		$user->where('username', $this->input->post('username'))->get();
		if ($user->result_count() != 1) {
			flash_notice('error', _('User not found.'));
			redirect('/admin/members/teams/' . $team->stub);
		}
		$this->viewdata["function_title"] = "Making team leader...";
		$member = new Membership();
		$member->make_team_leader($team_id, $user->id);
		flash_notice('notice', _('You have made the user a team leader.'));
		redirect('/admin/members/teams/' . $team->stub);
	}

	function remove_team_leader($team_id, $user_id) {
		if (!isAjax()) {
			return false;
		}
		if (!$this->tank_auth->is_team_leader($team_id) && !$this->tank_auth->is_allowed())
			return false;
		$this->viewdata["function_title"] = "Removing team leader...";
		$member = new Membership();
		$member->remove_team_leader($team_id, $user_id);
		flash_notice('notice', _('You have removed the user from his team leader position.'));
		$team = new Team($team_id);
		echo json_encode(array('href' => site_url('/admin/members/teams/' . $team->stub)));
	}

	function make_admin($user_id) {
		if (!isAjax()) {
			return false;
		}
		if (!$this->tank_auth->is_admin())
			return false;
		$profile = new Profile();
		if ($profile->change_group($user_id, 1)) {
			flash_notice('notice', _('You have added the user to the admin group.'));
			echo json_encode(array('href' => site_url('/admin/members/member/' . $user_id)));
			return true;
		}
		return false;
	}

	function remove_admin($user_id) {
		if (!isAjax()) {
			return false;
		}
		if (!$this->tank_auth->is_admin())
			return false;
		$profile = new Profile();
		if ($profile->change_group($user_id, 0)) {
			flash_notice('notice', _('You have removed the user from the administrators group.'));
			echo json_encode(array('href' => site_url('/admin/members/member/' . $user_id)));
			return true;
		}
		return false;
	}

	function make_mod($user_id) {
		if (!isAjax()) {
			return false;
		}
		if (!$this->tank_auth->is_admin())
			return false;
		$profile = new Profile();
		if ($profile->change_group($user_id, 3)) {
			flash_notice('notice', _('You have added the user to the moderators group.'));
			echo json_encode(array('href' => site_url('/admin/members/member/' . $user_id)));
			return true;
		}
		return false;
	}

	function remove_mod($user_id) {
		if (!isAjax()) {
			return false;
		}
		if (!$this->tank_auth->is_admin())
			return false;
		$profile = new Profile();
		if ($profile->change_group($user_id, 0)) {
			flash_notice('notice', _('You have removed the user from the moderators group.'));
			echo json_encode(array('href' => site_url('/admin/members/member/' . $user_id)));
			return true;
		}
		return false;
	}

}