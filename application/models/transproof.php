<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Transproof extends DataMapper
{

	var $has_one = array(
		'chapter' => array()
	);
	var $has_many = array(
		'transproof' => array()
	);
	var $validation = array(
		'chapter_id' => array(// need this to not be bound by page_id, as we can have many versions (RAW, v1, v2)
			'rules' => array('required', 'min_size' => 1),
			'label' => 'Chapter ID'
		),
		'related_transproof_id' => array(// original transproof_id to be updated with this
			'rules' => array('required', 'min_size' => 1),
			'label' => 'Related ID'
		),
		'user_id' => array(// recognize the user and allow him to change his text in case
			'rules' => array('required', 'min_size' => 1),
			'label' => 'User ID'
		),
		'pagenum' => array(// unbind the translations from the page_id, so we can have multiple versions
			'rules' => array('required', 'min_size' => 1),
			'label' => 'Page number',
		),
		'order' => array(// selective order of reading of the elements on the page
			'rules' => array('min_size' => 1),
			'label' => 'Order',
		),
		'type' => array(// translation = 1, comment = 2. font? info?
			'rules' => array('required', 'min_size' => 1),
			'label' => 'Type',
			'valid_match' => array(1, 2)
		),
		'text' => array(// simple text that can be comment or translation
			'rules' => array(),
			'label' => 'Text'
		),
		'font' => array(// font to be used, it's a serialized array
			'rules' => array(),
			'label' => 'Font'
		),
		'accepted' => array(
			'rules' => array(),
			'label' => 'Accepted',
			'valid_match' => array(1, 2, 3) // 1 = white, 2 = red, 3 = green
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
		),
		'deleted' => array(
			'rules' => array(),
			'label' => 'Deleted',
			'valid_match' => array(0, 1)
		)
	);
	var $auto_populate_has_many = TRUE;
	var $auto_populate_has_one = TRUE;
	var $min_box_height = 60;
	var $min_box_width = 60;

	function __construct($id = NULL)
	{
		parent::__construct($id);
		$this->CI &= get_instance();
	}


	function post_model_init($from_cache = FALSE)
	{
		
	}


	/**
	 * Override the DataMapper get to include always all the related transproofs
	 * 
	 * @author	Woxxy
	 * @param	integer|NULL $limit Limit the number of results.
	 * @param	integer|NULL $offset Offset the results when limiting.
	 * @return	DataMapper Returns self for method chaining.
	 */
	public function get($limit = NULL, $offset = NULL, $upwards = FALSE)
	{
		$result = parent::get($limit, $offset);

		// add data only if any result is found
		if ($result->result_count() > 0)
		{

			if ($upwards === TRUE) // we're from an object that isn't root and want to retrieve root objects
			{
				if ($this->related_transproof_id > 0)
				{
					/**
					 * @todo remake this with some joint or something
					 */
					// let's grab all the tree, automagically (and with some heavy database work...)
					$this->related_transproof = new Transproof();
					$this->related_transproof->where('id', $this->related_transproof_id)->get(NULL, NULL, TRUE);
				}
			}
			else
			{
				// use the $upwards variable to keep count: on 1 we are on root, on 2 we are on comment/translation, on 3 we are on edit
				// let's not fetch any further if we're at 3
				if ($upwards === FALSE)
				{
					$upwards = 1;
				}
				else
				{
					$upwards++;
				}
				if ($upwards < 3)
				{
					foreach ($this->all as $key => $item)
					{
						$item->transproofs = new Transproof();
						$item->transproofs->where('related_transproof_id', $this->id)->get(NULL, NULL, $upwards);
					}
				}
			}
		}

		return $result;
	}


	/**
	 * Comodity function to get a whole page of data
	 * 
	 * @param type $chapter_id
	 * @param type $pagenum
	 * @param String $datetime string in MySQL DateTime format
	 */
	function get_page($chapter_id, $pagenum, $datetime = '')
	{
		$this->where('chapter_id', $chapter_id)
				->where('pagenum', $pagenum);

		if ($datetime != '')
		{
			$this->where('created >', $datetime);
		}

		//$this->include_related('transproof', NULL, TRUE, TRUE);
	}


	/**
	 * Adds the translations and comments to database after very through inspection.
	 * It's safe to just throw a POST array into this.
	 * 
	 * @param array $data
	 * @return bool success
	 */
	function add($data = array())
	{
		// variables to override
		$data["user_id"] = $this->CI->tank_auth->get_user_id();
		unset($data["created"]);
		unset($data["edited"]);

		// prepare variables a bit, we're grabbing data from people who aren't admins or mods
		// check for the type
		if (isset($data["type"]))
		{
			$data["type"] = intval($data["type"]);
			if (!in_array($data["type"], array(1, 2))) // 1 = translation, 2 = comment
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
		if (isset($data["related_transproof_id"]))
		{
			$data["related_transproof_id"] = intval($data["related_transproof_id"]);
			$related_tp = new Transproof();
			$related_tp->where("id", $data["related_transproof_id"])->get(NULL, NULL, TRUE); // the true means to go upwards with the IDs

			if ($related_tp->result_count() != 1)
			{
				// not found, means something is fishy
				$this->error_message('error', _('The related comment could not be found.'));
				log_message('error', 'Transproof: The related comment could not be found.');
				return FALSE;
			}

			// set chapter_id just for the next function to validate that chapter exists and user is in the team
			// and unset it straight after so we don't save useless extra data
			$data["chapter_id"] = $related_tp->chapter_id;
		}

		// check for the chapter_id and the chapter to exist
		if (isset($data["chapter_id"]))
		{
			$data["chapter_id"] = intval($data["chapter_id"]);
			$chapter = new Chapter($data["chapter_id"]);

			// check that it exists at all
			if ($chapter->result_count() != 1)
			{
				$this->error_message('error', _('The chapter set does not exist.'));
				log_message('error', 'Transproof: The chapter ID set does not exist.');
				return FALSE;
			}

			// check if the current user is part of a team working on this chapter
			$chapter->get_teams(); // puts teams in $chapter->teams
			$is_team = $this->CI->tank_auth->is_team_array($chapter->teams); // using the is_team_array simplification!
			if (!$is_team)
			{
				$this->error_message('error', _('You tried to post a comment on a chapter not worked on by your team.'));
				log_message('error', 'Transproof: Chapter team didn\'t match any user team.');
				return FALSE;
			}

			// if the related transproof is set, we don't need chapter_id
			if ($data["related_transproof_id"] > 0)
			{
				unset($data["chapter_id"]);
			}
		}
		else
		{
			// if chapter_id isn't even set, stop it
			$this->error_message('error', _('The chapter was not set.'));
			log_message('error', 'Transproof: The chapter ID was not set.');
			return FALSE;
		}

		if (isset($data["pagenum"]))
		{
			// we don't want detailed pagenum on related transproof, we'll use the main one
			if (isset($data["related_transproof_id"]))
			{
				unset($data["pagenum"]);
			}

			$data["pagenum"] = intval($data["pagenum"]);

			// first check if it's a number and if it's higher than 0
			if ($data["pagenum"] === FALSE || $data["pagenum"] < 1)
			{
				$this->error_message('error', _('The page number was not a valid number.'));
				log_message('error', 'Transproof: The page number was not a valid number.');
				return FALSE;
			}
			// $chapter is already set for sure
			$pages = $chapter->get_pages();
			if ($data["pagenum"] > count($pages))
			{
				$this->error_message('error', _('There isn\'t a page with such a high number.'));
				log_message('error', 'Transproof: The page number was too high for the pages array.');
				return FALSE;
			}
		}
		else if (!isset($data["related_transproof_id"])) // might not be set if the related transproof is set
		{
			// if chapter_id isn't even set, stop it
			$this->error_message('error', _('The chapter was not set.'));
			log_message('error', 'Transproof: The chapter ID was not set.');
			return FALSE;
		}

		// now that we've confirmed some general stuff, back on related_transproof_id related to authentication
		if (isset($data["related_transproof_id"]))
		{
			if ($data["type"] === 0) // if it's a translation
			{
				// anyone can edit the translation
				if ($related_tp->type === 0)
				{
					// no limits
				}

				// putting a translation on a comment
				if ($related_tp->type === 1)
				{
					// trying to put a translation on a comment? Impossible
					$this->error_message('error', _('The chapter was not set.'));
					log_message('error', 'Transproof: Tried to set a translation related to a comment.');
					return FALSE;
				}
			}

			if ($data["type"] === 0) // if it's a comment
			{
				// adding a comment on a translation
				if ($related_tp->type === 0)
				{
					// it must be the root translation
					if ($related_tp->related_transproof_id != 0)
					{
						// trying to put a translation on a comment? Impossible
						$this->error_message('error', _('You can\'t put a comment on a translation that is not the root one.'));
						log_message('error', 'Transproof: Tried to set a comment on a translation that is not the root one.');
						return FALSE;
					}
				}

				// editing a comment
				if ($related_tp->type === 1)
				{
					// it must be the user's own comment
					if ($related_tp->user_id !== $this->CI->tank_auth->get_user_id())
					{
						// trying to put a translation on a comment? Impossible
						$this->error_message('error', _('You can only edit your own comments.'));
						log_message('error', 'Transproof: Tried to set a translation related to a comment.');
						return FALSE;
					}
				}
			}
		}

		// sanitize "deleted"
		// we'll need more security on this, depending on what are we deleting
		if (isset($data["deleted"])) // 0 means not deleted, 1 means deleted
		{
			$data["deleted"] = intval($data["deleted"]); // anything else than 1 means not to delete
			if (!in_array($data["deleted"], array(0, 1)))
			{
				$this->error_message('error', _('The deleting code was wrong.'));
				log_message('error', 'Transproof: Tried to set a deleting nuber different than 0 or 1.');
				return FALSE;
			}

			if ($data["delete"] === 0)
			{
				// if it's not 1 just unset and forget it
				unset($data["delete"]);
			}
			else // the deletion request is actually active
			{
				// we can't be deleting something that is just being created
				if (!isset($related_tp))
				{
					$this->error_message('error', _('You tried deleting something that you were just creating.'));
					log_message('error', 'Transproof: Tried to delete something on creation.');
					return FALSE;
				}

				// if it's a box that we're deleting
				// everyone should be able to get rid of them
				if ($related_tp->related_transproof_id == 0)
				{
					// leave that delete unscratched
				}
				else // something that is not a box
				{
					// you can't delete translation edits, they must be left versioned and at most brought back via rollback button
					if ($data["type"] === 1) // a translation
					{
						$this->error_message('error', _('You tried deleting a translation edit.'));
						log_message('error', 'Transproof: Tried to delete a translation edit.');
						return FALSE;
					}


					if ($data["type"] === 2) // a comment (could've gone without this, but we don't know if we'll add more types)
					{
						/**
						 * @todo fix the retriving
						 */
						// deleting edits is not allowed
						if ($related_tp->related_transproof->related_transproof_id !== 0)
						{
							$this->error_message('error', _('You tried deleting a comment edit.'));
							log_message('error', 'Transproof: Tried to delete a comment edit.');
							return FALSE;
						}

						// the user can only delete his own comments
						if ($related_tp->user_id !== $this->CI->tank_auth->get_user_id)
						{
							$this->error_message('error', _('You tried deleting something that doesn\'t belong to you.'));
							log_message('error', 'Transproof: Tried to delete something with a different user_id.');
							return FALSE;
						}
					}
				}
			}
		}
		// no else, we want it unset if possible
		// array with the name of the sizes, we'll use this either way
		$sizes = array('width', 'height', 'top', 'left', 'image_width', 'image_height');

		// let's give some division between classes of changes. the following is:
		// 1) any new box
		// 2) when with a translation edit we change the position of a translation box (a translation without parent)
		// 3) when with a comment edit we change the position of a comment box (a comment box without parent)
		if (!isset($data["related_transproof_id"]) || (isset($related_tp) && $related_tp->related_transproof_id == 0))
		{
			$has_sizes = FALSE;
			foreach ($sizes as $size)
			{
				if (isset($data[$size]))
				{
					$has_sizes = TRUE;
					break;
				}
			}

			// if any size has been set, make sure we got all the needed. we can't have just one piece of sizes
			// and, if we're creating a new box, we absolutely need the sizes
			if ($has_sizes || !isset($data["related_transproof_id"]))
			{
				// make the ones that are set INT values, and fail if they aren't INT values
				foreach ($sizes as $size)
				{
					// all the values must be set
					if (!isset($data[$size]))
					{
						$this->error_message('error', _('You must set all the sizes to create a box.'));
						log_message('error', 'Transproof: Tried to create a box without having all the sizes set.');
						return FALSE;
					}

					// check that the value is valid
					$data[$size] = intval($data[$size]);
					if ($data[$size] == 0)
					{
						$this->error_message('error', _('You set an invalid size value.'));
						log_message('error', 'Transproof: Tried to set an invalid size.');
						return FALSE;
					}
				}

				// make sure the numbers don't go out of bonduaries
				if ($data["width"] + $data["left"] <= $data["image_width"] || $data["height"] + $data["top"] <= $data["image_height"])
				{
					$this->error_message('error', _('The sizes you set on the image are incompatible with each other.'));
					log_message('error', 'Transproof: Tried to create a box with unmatching sizes.');
					return FALSE;
				}

				// make sure the size of a box will remain larger than default
				if ($data["width"] < $this->min_box_width || $data["height"] < $this->min_box_height)
				{
					$this->error_message('error', _('The sizes you set on the box are too small.'));
					log_message('error', 'Transproof: Tried to set too small width or height on the box.');
					return FALSE;
				}
			}
		}
		else // not a type supporting sizes
		{
			// just unset all the sizes
			foreach ($sizes as $size)
			{
				unset($data[$size]);
			}
		}
		
		// change the accepted setting on translations
		if(isset($data["accepted"]))
		{
			$data["accepted"] = intval($data["accepted"]);
			if(!in_array($data["accepted"], array(1, 2, 3)))
			{
				$this->error_message('error', _('You didn\'t use a proper value for accepting or rejecting.'));
				log_message('error', 'Transproof: Tried to set a wrong value to accepted.');
				return FALSE;
			}
			
			if($data["type"] == 1 && isset($related_tp)) // a translation edit
			{
				// leave it alone
			}
			else
			{
				// there's no point in setting this on a comment
				unset($data["accepted"]);
			}
		}

		// incredibly so, we might be done
		if (!$this->save())
		{
			// on error in save(), we'll just leave DataMapper errors on
			log_message('error', 'Transproof: Tried to set too small width or height on the box.');
			return FALSE;
		}
		else
		{
			// phew...
			return TRUE;
		}
	}


}