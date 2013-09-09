<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Archive extends DataMapper
{

	var $has_one = array();
	var $has_many = array();
	var $validation = array(
		'volume_id' => array(
			'rules' => array(),
			'label' => 'Volume ID',
		),
		'chapter_id' => array(
			'rules' => array(),
			'label' => 'Chapter ID',
		),
		'filename' => array(
			'rules' => array(),
			'label' => 'Filename'
		),
		'size' => array(
			'rules' => array(),
			'label' => 'Size',
		),
		'lastdownload' => array(
			'rules' => array(),
			'label' => 'Last download',
		)
	);

	function __construct($id = NULL)
	{
		parent::__construct($id);
	}


	function post_model_init($from_cache = FALSE)
	{

	}

	/**
	 * Creates a compressed cache file for the chapter
	 *
	 * @author Woxxy
	 * @return url to compressed file
	 */
	function compress($comic, $language = 'en', $volume = null, $chapter = null, $subchapter = 0)
	{
		require_once(FCPATH . 'assets/pclzip/pclzip.lib.php');
		$files = array();

		if (get_setting('fs_dl_volume_enabled') && $volume !== null && $chapter === null)
		{
			if ($volume == 0)
			{
				show_404();
			}

			$chapters = new Chapter();
			$chapters->where('comic_id', $comic->id)->where('volume', $volume)
				->order_by('volume', 'asc')->order_by('chapter', 'asc')->order_by('subchapter', 'asc')
				->get();

			if ($chapters->result_count() == 0)
			{
				show_404();
			}

			$volume_id = $volume;
			$chapter_id = $chapters->id;

			$filepath = $comic->directory();
			$filename = $this->filename_chapters_compressed($chapters);

			foreach ($chapters as $chaptere)
			{
				$pages = new Page();
				$pages->where('chapter_id', $chaptere->id)->get();

				foreach ($pages as $page)
				{
					$files[] = array(
						PCLZIP_ATT_FILE_NAME => 'content/comics/' . $comic->directory() . '/' . $chaptere->directory() . '/' . $page->filename,
						PCLZIP_ATT_FILE_NEW_FULL_NAME => $this->filename_chapter_compressed($chaptere) . '/' . $page->filename
					);
				}
			}
		}
		else
		{
			$chaptere = new Chapter();
			$chaptere->where('comic_id', $comic->id)->where('language', $language)->where('volume', $volume)->where('chapter', $chapter)->where('subchapter', $subchapter);
			$chaptere->get();

			if ($chaptere->result_count() == 0)
			{
				show_404();
			}

			$volume_id = 0;
			$chapter_id = $chaptere->id;
			$filepath = $comic->directory() . '/' . $chaptere->directory();
			$filename = $this->filename_chapter_compressed($chaptere);

			$pages = new Page();
			$pages->where('chapter_id', $chaptere->id)->get();

			foreach ($pages as $page)
			{
				$files[] = 'content/comics/' . $comic->directory() . '/' . $chaptere->directory() . '/' . $page->filename;
			}
		}

		$this->where('comic_id', $comic->id)->where('volume_id', $volume_id)->where('chapter_id', $chapter_id)->get();
		if ($this->result_count() == 0 || !file_exists('content/comics/' . $filepath . '/' .$this->filename))
		{
			$this->remove_old();

			$archive = new PclZip('content/comics/' . $filepath . '/' . $filename . '.zip');
			$archive->create($files, PCLZIP_OPT_REMOVE_ALL_PATH, PCLZIP_OPT_NO_COMPRESSION);

			$this->comic_id = $comic->id;
			$this->volume_id = $volume_id;
			$this->chapter_id = $chapter_id;
			$this->filename = $filename . '.zip';
			$this->size = filesize('content/comics/' . $filepath . '/' . $filename . '.zip');
			$this->lastdownload = date('Y-m-d H:i:s', time());
			$this->save();
		}
		else
		{
			$this->lastdownload = date('Y-m-d H:i:s', time());
			$this->save();
		}

		return array(
			"url" => site_url() . 'content/comics/' . $filepath . '/' . urlencode($this->filename),
			"server_path" => FCPATH . 'content/comics/' . $filepath . '/' . $this->filename
		);
	}


	/**
	 * Removes the compressed file from the disk and database
	 *
	 * @author Woxxy
	 * @returns bool
	 */
	function remove()
	{
		$chapter = new Chapter($this->chapter_id);
		$chapter->get_comic();

		if (file_exists("content/comics/" . $chapter->comic->directory() . "/" . $chapter->directory() . "/" . $this->filename))
		{
			if (!@unlink("content/comics/" . $chapter->comic->directory() . "/" . $chapter->directory() . "/" . $this->filename))
			{
				log_message('error', 'remove: error when trying to unlink() the compressed ZIP');
				return false;
			}
		}

		if (file_exists("content/comics/" . $chapter->comic->directory() . "/" . $this->filename))
		{
			if (!@unlink("content/comics/" . $chapter->comic->directory() . "/" . $this->filename))
			{
				log_message('error', 'remove: error when trying to unlink() the compressed ZIP');
				return false;
			}
		}

		$this->delete();
	}


	/**
	 * Calculates the size of the currently stored ZIPs
	 *
	 * @author Woxxy
	 * @returns int
	 */
	function calculate_size()
	{
		$this->select_sum('size')->get();
		return $this->size;
	}


	/**
	 * Removes ZIPs that are over the specified size
	 *
	 * @author Woxxy
	 * @returns bool
	 */
	function remove_old()
	{
		$unlink_errors = 0;
		while ($this->calculate_size() > (get_setting('fs_dl_archive_max') * 1024 * 1024))
		{
			$archive = new Archive();
			$archive->order_by('lastdownload', 'ASC')->limit(1, $unlink_errors)->get();
			if ($archive->result_count() == 1)
			{
				if (!$archive->remove())
				{
					$unlink_errors++;
				}
			}
			else
			{
				break;
			}
		}
	}


	/**
	 * Removes all the ZIPs
	 *
	 * @author Woxxy
	 * @returns bool
	 */
	function remove_all()
	{
		$archives = new Archive();
		$archives->get();
		foreach ($archive->all as $archive)
		{
			$archive->remove();
		}
	}


	/**
	 * Creates the filename for the ZIP
	 *
	 * @author Woxxy
	 * @returns bool
	 */
	function filename_chapter_compressed($chapter)
	{
		$chapter->get_teams();
		$chapter->get_comic();
		$filename = "";
		/*
		 *  Proposal for guesser
		 *
		 *  %comic% just name
		 *  %_comic% name with underscores
		 * 	%volume% volume number
		 * 	%chapter% chapter number
		 *  %subchapter% subchapter number
		 *  {volume ... } section dedicated to the volume
		 *  {chapter ... } section dedicated to the chapter
		 *  {subchapter ... } section dedicated to the subchapter
		 *  %group% print the name of the group
		 *  %r_group% print the separator for beginning of groups
		 *  %l_group% print the separator for end of group
		 *  %mid_group% print separator between groups
		 */

		$teams = array();
		foreach ($chapter->teams as $team)
		{
			$filename .= "[" . $team->name . "]";
		}

		$filename .= trim($chapter->comic->name);
		if ($chapter->volume !== FALSE && $chapter->volume != 0)
			$filename .= '_v' . str_pad($chapter->volume, 2, '0', STR_PAD_LEFT);
		$filename .= '_c' . str_pad($chapter->chapter, 2, '0', STR_PAD_LEFT);
		if ($chapter->subchapter !== FALSE && $chapter->subchapter != 0)
			$filename .= '_ex' . str_pad($chapter->subchapter, 2, '0', STR_PAD_LEFT);

		$filename = str_replace(" ", "_", $filename);

		$bad = array_merge(
				array_map('chr', range(0, 31)), array("<", ">", ":", '"', "/", "\\", "|", "?", "*"));
		$filename = str_replace($bad, "", $filename);

		return $filename;
	}

	function filename_chapters_compressed($chapters)
	{
		$teams = array();
		foreach ($chapters as $chapter)
		{
			$chapter->get_teams();
			$chapter->get_comic();

			foreach ($chapter->teams as $team)
			{
				if (!in_array($team->name, $teams))
				{
					$teams[] = $team->name;
				}
			}
		}

		$invalid = array_merge(array_map('chr', range(0, 31)), array("<", ">", ":", '"', "/", "\\", "|", "?", "*"));
		$filename = '['.implode('][', $teams).']'.trim($chapter->comic->name).'_v'.str_pad($chapter->volume, 2, '0', STR_PAD_LEFT);
		$filename = str_replace(" ", "_", $filename);
		$filename = str_replace($invalid, "", $filename);

		return $filename;
	}


}

/* End of file team.php */
/* Location: ./application/models/archive.php */
