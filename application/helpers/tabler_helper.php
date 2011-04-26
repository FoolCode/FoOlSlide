<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

if (!function_exists('tabler')) {

	function tabler($rows, $list = TRUE, $edit = TRUE) {
		$result = array();
		$CI = & get_instance();


		$rows[] = array(
			"",
			array(
				'type' => 'submit',
				'name' => 'submit',
				'id' => 'submit',
				'value' => 'Save'
			)
		);

		$echo = "";

		foreach ($rows as $rowk => $row) {
			foreach ($row as $colk => $column) {
				if ($colk == 0) {
					$result[$rowk][$colk]["table"] = $column;
					$result[$rowk][$colk]["form"] = $column;
				} else {
					if (!isset($column['value']))
						$column['value'] = "";
					if (is_array($column)) {
						$result[$rowk][$colk]["table"] = writize($column);
						if (isset($column['type'])) {
							$result[$rowk][$colk]["form"] = formize($column);
							$result[$rowk][$colk]["type"] = $column['type'];
						}
					} else {
						$result[$rowk][$colk]["table"] = writize($column);
						$result[$rowk][$colk]["form"] = $column;
					}
				}
			}
		}

		// echo '<pre>'; print_r($result); echo '</pre>';
		if ($list && $edit) {
			$CI->buttoner[] = array(
				'text' => 'Edit',
				'href' => '',
				'onclick' => "slideToggle('.plain'); slideToggle('.edit'); return false;"
			);
		}

		if ($list) {
			$echo .= '<div class="plain"><table class="form">';
			foreach ($result as $rowk => $row) {
				if (isset($row[1]['type']) && $row[1]['type'] == 'hidden') {
					//$echo .= $row[1]['form'];
				} else {
					if ($row[1]['table'] != 'Save' && $row[0]['table'] != 'id') {
						$echo .= '<tr>';
						foreach ($row as $column) {
							$echo .= '<td>';
							if (is_array($column['table'])) {
								foreach ($column['table'] as $mini) {
									$echo .= '' . $mini->name . ' ';
								}
							} else if ($column['table'] == "")
								$echo .= 'N/A';
							else
								$echo .= $column['table'];
							$echo .= '</td>';
						}
						$echo .= '</tr>';
					}
				}
			}
			$echo .= '</table></div>';
		}


		if ($edit) {
			$echo .= '<div class="edit" ' . (($list && $edit) ? 'style="display:none;"' : '') . '><table class="form">';
			foreach ($result as $rowk => $row) {
				if ($row[1]['type'] == 'hidden') {
					$echo .= $row[1]['form'];
				} else {
					$echo .= '<tr>';
					foreach ($row as $column) {
						$echo .= '<td>';
							$echo .= $column['form'];
						$echo .= '</td>';
					}
					$echo .= '</tr>';
				}
			}
			$echo .= '</table></div>';
		}
		return $echo;
	}

}


if (!function_exists('formize')) {

	function formize($column) {
		$CI = & get_instance();
		if (isset($column['preferences']))
			$column['value'] = get_setting($column['name']);

		//if($column['type'] == 'input' || $column['type'] == 'nation') $column['value'] = set_value($column['name']);
		
		if ($column['type'] == 'checkbox') {
			if($column['value'] == 1) $column['checked'] = 'checked';
			$column['value'] = 1;
		}


		$formize = 'form_' . $column['type'];
		$type = $column['type'];
		if(isset($column['help'])) $help = $column['help'];
		unset($column['type']);
		unset($column['preferences']);
		unset($column['help']);

		if (is_array($column['value'])) {
			$result = array();
			$column['name'] .= '[]';
			$minion = $column['value'];
			foreach ($minion as $mini) {
				if (isset($mini->name))
					$column['value'] = $mini->name;
				else
					$column['value'] = $mini;
				$result[] = $formize($column);
			}
			if (empty($result)) {
				$column['value'] = "";
				$result[] = $formize($column);
			}
			$column['value'] = "";
			$column['onKeyUp'] = "addField(this);";
			$result[] = $formize($column);
		} else {
			// echo '<pre>'; print_r($column); echo '</pre>';
			if ($type == 'hidden' && isset($column["value"])) {
				$result = $formize($column['name'], $column['value']);
			}
			else
				$result = $formize($column);
		}
		
		if(is_array($result))
		{
			$results = $result;
			$result = "";
			foreach($results as $resulting)
			{
				$result.= $resulting.'<br/>';
			}
		}

		if (isset($help)) $result = $result.'<div class="help">'.$help.'</div>';
		return $result;
	}

}

function writize($column) {
	//echo '<pre>'; print_r($column); echo '</pre>';
	if (!is_array($column)) {
		return $column;
	}

	if (isset($column['display'])) {
		if ($column['display'] == 'image' && $column['value'])
			$column['value'] = '<img src="' . $column['value'] . '" />';
		//if($column['display'] == 'hidden') return '';
	}
	
	if (isset($column['type']) && $column['type'] == 'language')
	{
		$lang = config_item('fs_languages');
		if (!isset($column['value']) || $column['value'] == "")
			$column['value'] = get_setting('fs_gen_default_lang');
		$column['value'] = $lang[$column['value']];
	}
	return $column['value'];
}

if (!function_exists('lister')) {

	function lister($rows) {
		$echo = '<div class="list">';
		foreach ($rows as $row) {
			if (!isset($row['smalltext_r']))
				$row['smalltext_r'] = "";
			if (!isset($row['smalltext']))
				$row['smalltext'] = "";

			$echo .= '<div class="item">
                    <div class="title">' .
					$row['title'] .
					'</div>
                    <div class="smalltext info">' .
					$row['smalltext_r'] .
					'</div>
                    <div class="smalltext">' .
					$row['smalltext'] .
					'</div>';
			$echo .= '</div>';
		}
		return $echo . '</div>';
	}

}


if (!function_exists('ormer')) {

	function ormer($db) {
		$result = array();
		$rows = $db->validation;
		foreach ($rows as $key => $row) {
			if ($key == 'id') {
				$row['type'] = 'hidden';
			}

			if (!isset($row['value']))
				$row['value'] = '';
			if (!isset($row['display']))
				$row['display'] = '';
			if (!isset($row['placeholder']))
				$row['placeholder'] = '';
			if (!isset($row['help']))
				$row['help'] = '';
			
			if (isset($row['type'])) {
				if ($db->$key != "")
					$row['value'] = $db->$key;
				$result[] = array(
					$row['label'],
					array(
						'name' => ($key),
						'value' => ($row['value']),
						'type' => ($row['type']),
						'display' => ($row['display']),
						'placeholder' => ($row['placeholder']),
						'help' => ($row['help'])
					)
				);
			}
		}
		//		echo '<pre>'; print_r($result); echo '</pre>';

		return $result;
	}

}

if (!function_exists('buttoner')) {

	function buttoner() {
		$CI = & get_instance();
		if (!isset($CI->buttoner))
			return "";
		$texturl = $CI->buttoner;

		$echo = '<div class="gbuttons">';
		foreach ($texturl as $key => $item) {
			$echo .= '<a class="gbutton" ';
			if (isset($item['onclick']))
				$echo .= 'onclick="' . ($item['onclick']) . '" ';
			if (isset($item['href']))
				$echo .= 'href="' . ($item['href']) . '" ';
			if (isset($item['plug']))
				$echo .= 'onclick="confirmPlug(\'' . $item['href'] . '\', \'' . addslashes($item['plug']) . '\'); return false;"';
			//	if (isset($item['slide']) && $item['slide']) $echo .= 'onclick="confirmSlide(); return false;"';
			$echo .= '>' . $item['text'] . '</a>';
			//	if (isset($item['slide']) && $item['slide'])
			//	{
			//		$echo .= '<a class="gbutton red" href="'.$item['href'].'">DO IT!</a>';
			//	}
		}
		$echo .= '<div class="clearer_r"></div></div>';
		return $echo;
	}

}

if (!function_exists('form_nation')) {
	function form_nation($column)
	{
		$codes = config_item('fs_country_codes');
		$nations = config_item('fs_country_names');
		
		$nationcodes = array();
		foreach($codes as $key => $code)
		{
			$nationcodes[$code] = $nations[$key]; 
		}
		if(isset($column['onKeyUp']))
		{
			$column['onChange'] = 'onChange="'.$column['onKeyUp'].'"';
			unset($column['onKeyUp']);
		}
		else $column['onChange'] = '';
		return form_dropdown($column['name'], $nationcodes, $column['value'], $column['onChange']);
	}
}

if (!function_exists('form_language')) {
	function form_language($column)
	{
		$lang = config_item('fs_languages');
		if (!isset($column['value']) || $column['value'] == "")
			$column['value'] = get_setting('fs_gen_default_lang');
		return form_dropdown($column['name'], $lang, $column['value']);
	}
}