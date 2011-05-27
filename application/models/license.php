<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class License extends DataMapper {

	var $has_one = array('comic');
	var $has_many = array();
	var $validation = array(
		'comic_id' => array(
			'rules' => array(),
			'label' => 'Comic ID',
		),
		'nation' => array(
			'rules' => array(),
			'label' => 'nation',
		)
	);

	function __construct($id = NULL) {
		parent::__construct($id);
	}

	function post_model_init($from_cache = FALSE) {
		
	}
	
	/**
	 * Gets all the comics' licensed nations and puts the country codes in an array
	 * 
	 * @param int $id comic_id
	 * @return array country codes
	 */
	function get_by_comic($id){
		$this->where('comic_id', $id)->get();
		$result = array();
		foreach($this->all as $item)
		{
			$result[] = $item->nation;
		}
		return $result;
	}
	
	function update($id, $new)
	{
		$this->where('comic_id', $id)->get();
		if($this->result_count() > 0)
		{
			$previous = $this->get_by_comic($id);
			$partial = array_diff($previous, $new);
			if(empty($partial) && (count($previous) == count($new)))
			{
				return true;
			}
			$this->clear();
			$this->where('comic_id', $id)->get();
			$this->delete_all();
		}
		
		
		foreach($new as $item)
		{
			if($item == FALSE) continue;
			$this->clear();
			$this->comic_id = $id;
			$this->nation = $item;
			$this->save();
		}
		return true;		
	}

}

/* End of file license.php */
/* Location: ./application/models/license.php */
