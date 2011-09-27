<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

// it's not a draft of a controller... we're going to call the controller Draft
class Draft extends Team_Controller
{
	/**
	 * If we're here it means a team is selected and that 
	 * the current team's DataMapper object is in $this->teamc->team
	 * 
	 * No team-less people can be here, so don't worry about security that has
	 * been taken care of in the controller!
	 */
	function __construct()
	{
		parent::__construct();
		$this->viewdata['controller_title'] = _("Draft");
	}


	/**
	 * Draft index: shows general data on the currently selected draft
	 * 
	 * @param int $id draft id
	 */
	function index($id, $derp)
	{
		$this->output->set_output($id);
	}


	/**
	 * Shows the page with the necessary JavaScript to show the comments.
	 * The rest is dealt by the sync_script() controller function.
	 * 
	 * @author Woxxy
	 * @param int $id draft id
	 */
	function script($chapter_id, $page)
	{
		$chapter = new Chapter($chapter_id);
		if ($chapter->result_count() != 1)
		{
			show_404();
		}

		$chapter->get_teams(); // puts teams in $chapter->teams
		$is_team = $this->tank_auth->is_team_array($chapter->teams);

		if (!$is_team)
		{
			show_404();
		}

		if (!is_numeric($page) && $page < 0)
		{
			show_404();
		}

		$pages = $chapter->get_pages();
		if ($page > count($pages))
		{
			show_404();
		}

		$data["chapter_id"] = $chapter->id;
		$data["page_number"] = $page;
		$data["page_url"] = $pages[$page-1]["url"];
		$this->viewdata["main_content_view"] = $this->load->view('team/draft/script', $data, TRUE);
		$this->load->view('team/default', $this->viewdata);
		// show just the page, the javascript will do the sync
	}


	/**
	 * Download and update the script sequentially. This function does everything at once.
	 * 
	 * @author Woxxy
	 */
	function sync_script()
	{
		//print_r($this->input->post());
		// the user can sync only a chapter and page at time
		if (!$this->input->post('chapter_id') || !$this->input->post('pagenum'))
		{
			show_404();
		}

		$chapter = new Chapter($this->input->post('chapter_id'));
		if ($chapter->result_count() != 1)
		{
			show_404();
		}

		if ($this->session->userdata('transproof_chapter_id') != $chapter->id)
		{
			$chapter->get_teams(); // puts teams in $chapter->teams
			$is_team = $this->tank_auth->is_team_array($chapter->teams);
		}

		// if we're here, it means that we're already authenticated: let's use userdata to reduce database seeking
		$this->session->set_userdata('transproof_chapter_id', $chapter->id);

		$result = array(); // an array for success and errors
		if ($this->input->post('update'))
		{
			foreach ($this->input->post('update') as $key => $item)
			{
				// override in order to send all the changes to an unique chapter and page
				$item["chapter_id"] = $this->input->post('chapter_id');
				$item["pagenum"] = $this->input->post('pagenum');

				// smile: the model does the sanitization
				$transproof = new Transproof();
				$transproof->add($item);
				$result[] = $transproof->error->string;
			}
		}
		
		$transproof = new Transproof();
		$transproof->get_page($this->input->post('chapter_id'), $this->input->post('pagenum'));
		$this->output->set_output(json_encode(array("sync" => $transproof->all_to_array(), "results" => $result)));
	}


}