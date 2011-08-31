<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Transproof extends DataMapper
{

	var $has_one = array('chapter');
	var $has_many = array();
	var $validation = array(
		'page_id' => array( // the original page_id, though we shouldn't need it
			'rules' => array('required', 'min_size' => 1),
			'label' => 'Page ID'
		),
		'chapter_id' => array( // need this to not be bound by page_id, as we can have many versions (RAW, v1, v2)
			'rules' => array('required', 'min_size' => 1),
			'label' => 'Chapter ID'
		),
		'related_transproof_id' => array( // original transproof_id to be updated with this
			'rules' => array('required', 'min_size' => 1),
			'label' => 'Related ID'
		),
		'user_id' => array( // recognize the user and allow him to change his text in case
			'rules' => array('required', 'min_size' => 1),
			'label' => 'User ID'
		),
		'pagenum' => array( // unbind the translations from the page_id, so we can have multiple versions
			'rules' => array('required', 'min_size' => 1),
			'label' => 'Page number',
		),
		'order' => array( // selective order of reading of the elements on the page
			'rules' => array('required', 'min_size' => 1),
			'label' => 'Order',
		),
		'type' => array( // translation = 1, comment = 2. font? info?
			'rules' => array('required', 'min_size' => 1),
			'label' => 'Type',
			'valid_match' => array(1, 2)
		),
		'text' => array( // simple text that can be comment or translation
			'rules' => array(),
			'label' => 'Text'
		),
		'font' => array( // font to be used, it's a serialized array
			'rules' => array(),
			'label' => 'Font'
		),
		'width' => array(
			'rules' => array('min_size' => 1),
			'label' => 'Width',
		),
		'height' => array(
			'rules' => array('min_size' => 1),
			'label' => 'Height'
		),
		'top' => array(
			'rules' => array('min_size' => 1),
			'label' => 'Top'
		),
		'left' => array(
			'rules' => array('min_size' => 1),
			'label' => 'Left'
		),
		'image_width' => array(
			'rules' => array('min_size' => 1),
			'label' => 'Image Width'
		),
		'image_height' => array(
			'rules' => array('min_size' => 1),
			'label' => 'Image Height'
		)
	);

	function __construct($id = NULL)
	{
		parent::__construct($id);
		$this->CI &= get_instance();
	}


	function post_model_init($from_cache = FALSE)
	{
		
	}
	
	function add($data = array())
	{
		// prepare variables a bit, we're grabbing data from people who aren't admins or mods
		
		// check for the type
		if(isset($data["type"]))
		{
			$data["type"] = intval($data["type"]);
			if(!in_array($data["type"], array(1,2))) // 1 = translation, 2 = comment
			{
				$this->error_message('error', _('The type of comment doesn\'t match any of the available.'));
				log_message('error', 'Transproof: The type doesn\'t match any of the available.');
				return FALSE;
			}
		}
		else
		{
			$this->error_message('error', _('The type of comment was not set.'));
			log_message('error', 'Transproof: The type of comment was not set.');
			return FALSE;
		}
		
		// there might be no related_transproof_id, but in case, it's an INT
		// if it's set, check that it exists and it DOESN'T have a parent
		if(isset($data["related_transproof_id"]))
		{
			$related_tp = new Transproof();
			$related_tp->where("id", intval($data["related_transproof_id"]))
					->where("chapter_id", $data["chapter_id"]) // it better not be from another chapter!
					->get(); 
			
			if($related_tp->result_count() != 1)
			{
				// not found, means something is fishy
				$this->error_message('error', _('The related comment could not be found.'));
				log_message('error', 'Transproof: The related comment could not be found.');
				return FALSE;
			}
		}
		
		// check for the chapter_id and the chapter to exist
		if(isset($data["chapter_id"]))
		{
			$data["chapter_id"] = intval($data["chapter_id"]);
			$chapter = new Chapter($data["chapter_id"]);
			
			// check that it exists at all
			if($chapter->result_count() != 1)
			{
				$this->error_message('error', _('The chapter set does not exist.'));
				log_message('error', 'Transproof: The chapter ID set does not exist.');
				return FALSE;
			}
			
			// check if the current user is part of a team working on this chapter
			$chapter->get_teams(); // puts teams in $chapter->teams
			$is_team = $this->CI->tank_auth->is_team_array($chapter->teams); // using the is_team_array simplification!
			if(!$is_team)
			{
				$this->error_message('error', _('You tried to post a comment on a chapter not worked on by your team.'));
				log_message('error', 'Transproof: Chapter team didn\'t match any user team.');
				return FALSE;
			}
		}
		else
		{
			// if chapter_id isn't even set, stop it
			$this->error_message('error', _('The chapter was not set.'));
			log_message('error', 'Transproof: The chapter ID was not set.');
			return FALSE;
		}
		
		if(isset($data["pagenum"]))
		{
			$data["pagenum"] = intval($data["pagenum"]);
			
			// first check if it's a number and if it's higher than 0
			if($data["pagenum"] === FALSE || $data["pagenum"] < 1)
			{
				$this->error_message('error', _('The page number was not a valid number.'));
				log_message('error', 'Transproof: The page number was not a valid number.');
				return FALSE;
			}
			// $chapter is already set for sure
			$pages = $chapter->get_pages();
			if($data["pagenum"] > count($pages))
			{
				$this->error_message('error', _('There isn\'t a page with such a high number.'));
				log_message('error', 'Transproof: The page number was too high for the pages array.');
				return FALSE;
			}
		}
		else
		{
			// if chapter_id isn't even set, stop it
			$this->error_message('error', _('The chapter was not set.'));
			log_message('error', 'Transproof: The chapter ID was not set.');
			return FALSE;
		}
		
		
		
		if($data["type"] === 1) // if it's a translation
		{
			if()
		}
	}


}