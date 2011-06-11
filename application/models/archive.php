<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Archive extends DataMapper {

	var $has_one = array();
	var $has_many = array();
	var $validation = array(
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

	function __construct($id = NULL) {
		parent::__construct($id);
	}

	function post_model_init($from_cache = FALSE) {
		
	}

	/**
	 * Creates a compressed cache file for the chapter
	 * 
	 * @author Woxxy
	 * @return url to compressed file
	 */
	function compress($chapter) {
		$chapter->get_comic();
		$chapter->get_pages();
		$files = array();

		$this->where('chapter_id', $chapter->id)->get();
		if ($this->result_count() == 0) {
			$this->remove_old();
			$CI = & get_instance();
			
			require_once(FCPATH.'assets/pclzip/pclzip.lib.php');
			$filename = $this->filename_compressed($chapter);
			$archive = new PclZip("content/comics/" . $chapter->comic->directory() . "/" . $chapter->directory() . "/" . $filename.'.zip');
			
			$filearray = array();
			foreach ($chapter->pages as $page) {
				$filearray[] = "content/comics/" . $chapter->comic->directory() . "/" . $chapter->directory() . "/" . $page["filename"];
			}
			
			$v_list = $archive->create(implode(',', $filearray), PCLZIP_OPT_REMOVE_ALL_PATH, PCLZIP_OPT_ADD_PATH, $filename, PCLZIP_OPT_NO_COMPRESSION);

			$this->chapter_id = $chapter->id;
			$this->filename = $filename.'.zip';
			$this->size = filesize("content/comics/" . $chapter->comic->directory() . "/" . $chapter->directory() . "/" . $filename.'.zip');
			$this->lastdownload = date('Y-m-d H:i:s', time());
			$this->save();
		}
		else {
			$this->lastdownload = date('Y-m-d H:i:s', time());
			$this->save();
		}

		return site_url() . "content/comics/" . $chapter->comic->directory() . "/" . $chapter->directory() . "/" . $this->filename;
	}

	/**
	 * Removes the compressed file from the disk and database
	 * 
	 * @author Woxxy
	 * @returns bool 
	 */
	function remove() {
		$chapter = new Chapter();
		$chapter->where('id', $this->chapter_id)->get();
		$chapter->get_comic();
		unlink("content/comics/" . $chapter->comic->directory() . "/" . $chapter->directory() . "/" . $this->filename);
		$this->delete();
	}

	function calculate_size() {
		$this->select_sum('size')->get();
		return $this->size;
	}

	function remove_old() {
		while ($this->calculate_size() > (get_setting('fs_dl_archive_max') * 1024 * 1024)) {
			$archive = new Archive();
			$archive->order_by('lastdownload', 'ASC')->limit(1)->get();
			$archive->remove();
		}
	}

	function filename_compressed($chapter) {
		$chapter->get_teams();
		$chapter->get_comic();
		$filename = "";
		foreach ($chapter->teams as $team) {
			$filename .= "[" . $team->name . "]";
		}
		$filename .= $chapter->comic->name;
		if ($chapter->volume !== FALSE && $chapter->volume != 0)
			$filename .= '_v' . $chapter->volume;
		$filename .= '_c' . $chapter->chapter;
		if ($chapter->subchapter !== FALSE && $chapter->subchapter != 0)
			$filename .= '_s' . $chapter->subchapter;

		$bad = array_merge(
				array_map('chr', range(0, 31)), array("<", ">", ":", '"', "/", "\\", "|", "?", "*"));
		$filename = str_replace($bad, "", $filename);
		$filename = str_replace(" ", "_", $filename);

		return $filename;
	}

}

/* End of file team.php */
/* Location: ./application/models/team.php */