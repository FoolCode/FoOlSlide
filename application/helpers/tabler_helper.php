<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

if (!function_exists('tabler')) {

	function tabler($rows, $list = TRUE, $edit = TRUE, $repopulate = FALSE) {
		$result = array();
		$CI = & get_instance();


		$rows[] = array(
			"",
			array(
				'type' => 'submit',
				'name' => 'submit',
				'id' => 'submit',
				'value' => _('Save')
			)
		);

		$echo = "";

		foreach ($rows as $rowk => $row) {
			foreach ($row as $colk => $column) {
				if ($colk == 0) {
					$result[$rowk][$colk]["table"] = $column;
					$result[$rowk][$colk]["form"] = $column;
				}
				else {
					if (!isset($column['value']))
						$column['value'] = "";
					if (is_array($column)) {
						$result[$rowk][$colk]["table"] = writize($column);
						if (isset($column['type'])) {
							$result[$rowk][$colk]["form"] = formize($column, $repopulate);
							$result[$rowk][$colk]["type"] = $column['type'];
						}
					}
					else {
						$result[$rowk][$colk]["table"] = writize($column);
						$result[$rowk][$colk]["form"] = $column;
					}
				}
			}
		}

		// echo '<pre>'; print_r($result); echo '</pre>';
		if ($list && $edit) {
			$CI->buttoner[] = array(
				'text' => _('Edit'),
				'href' => '',
				'onclick' => "slideToggle('.plain'); slideToggle('.edit'); return false;"
			);
		}

		if ($list) {
			$echo .= '<div class="plain"><table class="form">';
			foreach ($result as $rowk => $row) {
				if (isset($row[1]['type']) && $row[1]['type'] == 'hidden') {
					//$echo .= $row[1]['form'];
				}
				else {
					if (!isset($row[1]) || $row[1]['table'] != _('Save') && $row[0]['table'] != 'id') {
						$echo .= '<tr>';
						foreach ($row as $column) {
							$echo .= '<td>';
							if (is_array($column['table'])) {
								foreach ($column['table'] as $mini) {
									$echo .= '' . $mini->name . ' ';
								}
							}
							else if ($column['table'] == "")
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
				}
				else {
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

	function formize($column, $repopulate) {
		$CI = & get_instance();
		if($repopulate && $CI->input->post()) $column['value'] = (set_value($column['name'])=="")?$column["value"]:set_value($column['name']);
		if (isset($column['preferences']))
			$column['value'] = get_setting($column['name']);

		//if($column['type'] == 'input' || $column['type'] == 'nation') $column['value'] = set_value($column['name']);

		if ($column['type'] == 'checkbox') {
			if ($column['value'] == 1)
				$column['checked'] = 'checked';
			$column['value'] = 1;
		}


		$formize = 'form_' . $column['type'];
		$type = $column['type'];
		if (isset($column['help']))
			$help = $column['help'];
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
		}
		else {
			// echo '<pre>'; print_r($column); echo '</pre>';
			if ($type == 'hidden' && isset($column["value"])) {
				$result = $formize($column['name'], $column['value']);
			}
			else
				$result = $formize($column);
		}

		if (is_array($result)) {
			$results = $result;
			$result = "";
			foreach ($results as $resulting) {
				$result.= $resulting . '<br/>';
			}
		}

		if (isset($help))
			$result = $result . '<div class="help">' . $help . '</div>';
		return $result;
	}

}

function writize($column) {
	//echo '<pre>'; print_r($column); echo '</pre>';
	if (!is_array($column)) {
		return $column;
	}

	if (isset($column['display'])) {

		if (function_exists('display_' . $column['display'])) {
			$displayfn = 'display_' . $column['display'];
			$column['value'] = $displayfn($column);
		}

		if ($column['display'] == 'image' && $column['value'])
			$column['value'] = '<img src="' . $column['value'] . '" />';
		//if($column['display'] == 'hidden') return '';
	}

	if (isset($column['type']) && $column['type'] == 'language') {
		$lang = config_item('fs_languages');
		if (!isset($column['value']) || $column['value'] == "")
			$column['value'] = get_setting('fs_gen_default_lang');
		$column['value'] = $lang[$column['value']];
	}
	
	if (isset($column['type']) && $column['type'] == 'nation') {
		$value = $column['value'];
		$column['value'] = "";
		$nations = config_item('fs_country_names');
		foreach($value as $key => $item)
		{
			$num = array_search($item, config_item('fs_country_codes'));
			if($key>0) $column['value'] .= ", ";
			$column['value'] .= $nations[$num];
		}
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

			if (isset($row['type'])) {
				if ($db->$key != "")
					$row['value'] = $db->$key;

				$details = array();
				$details = $row;
				unset($details['label']);
				$details['name'] = $key;

				$result[] = array(
					$row['label'],
					$details
				);
			}
		}
		//echo '<pre>';
		//print_r($result);
		//echo '</pre>';

		return $result;
	}

}

if (!function_exists('form_nation')) {

	function form_nation($column) {
		$codes = config_item('fs_country_codes');
		$nations = config_item('fs_country_names');

		$nationcodes = array();
		foreach ($codes as $key => $code) {
			$nationcodes[$code] = $nations[$key];
		}
		if (isset($column['onKeyUp'])) {
			$column['onChange'] = 'onChange="' . $column['onKeyUp'] . '"';
			unset($column['onKeyUp']);
		}
		else
			$column['onChange'] = '';
		return form_dropdown($column['name'], $nationcodes, $column['value'], $column['onChange']);
	}

}

if (!function_exists('form_language')) {

	function form_language($column) {
		$lang = config_item('fs_languages');
		if (!isset($column['value']) || $column['value'] == "")
			$column['value'] = get_setting('fs_gen_default_lang');
		return form_dropdown($column['name'], $lang, $column['value']);
	}

}

if (!function_exists('form_group')) {

	function form_group($column) {
		$CI = & get_instance();
		$groups = new Group();
		$groups->get();
		$set = array();
		foreach ($groups->all as $group) {
			$set[$group->id] = $group->name;
		}

		return form_dropdown($column['name'], $set, $column['value']);
	}

}

if (!function_exists('buttoner')) {

	function buttoner($data = NULL) {
		if (!is_array($data)) {
			$CI = & get_instance();
			if (!isset($CI->buttoner))
				return "";
			$texturl = $CI->buttoner;
		}
		else
			$texturl = array($data);

		$echo = '<div class="gbuttons">';
		foreach ($texturl as $key => $item) {
			$echo .= '<a class="gbutton" ';
			if (isset($item['onclick']))
				$echo .= 'onclick="' . ($item['onclick']) . '" ';
			if (isset($item['href']))
				$echo .= 'href="' . ($item['href']) . '" ';
			if (isset($item['plug']))
				$echo .= 'onclick="confirmPlug(\'' . $item['href'] . '\', \'' . addslashes($item['plug']) . '\', this); return false;"';
			$echo .= '>';
			if (isset($item['plug']))
				$echo .= '<img class="loader" src="'.site_url().'/assets/js/images/ajax-loader.gif'.'" />';
			$echo .= $item['text'] . '</a>';
		}
		$echo .= '<div class="clearer_r"></div></div>';
		return $echo;
	}

}

if (!function_exists('display_buttoner')) {

	function display_buttoner($column) {
		return buttoner($column);
	}

}

if (!function_exists('form_buttoner')) {

	function form_buttoner($column) {
		return buttoner($column);
	}

}

if (!function_exists('prevnext')) {

	function prevnext($base_url, $item) {
		$echo = '<div class="prevnext">';

		if ($item->paged->has_previous) {
			$echo .= '<div class="prev">
					<a class="gbutton fleft" href="' . site_url($base_url.'1') . '">«« First</a>
					<a class="gbutton fleft" href="' . site_url($base_url . $item->paged->previous_page) . '">« Prev</a>
				</div>';
		}
		if ($item->paged->has_next) {
			$echo .= '<div class="next">
					<a class="gbutton fright" href="'.site_url($base_url.$item->paged->total_pages).'">Last »»</a>
					<a class="gbutton fright" href="'.site_url($base_url.$item->paged->next_page).'">Next »</a>
				</div>';
		}
		$echo .= '<div class="clearer"></div></div>';
		
		return $echo;
	}

}

if (!function_exists('mobile_prevnext')) {

	function mobile_prevnext($base_url, $item) {
		
		/*

		
			<a href="<?php echo site_url('/reader/list/') ?>"><?php echo _("Go to series list") ?></a>
		<!-- /navbar -->
*/
		$echo = '<div data-role="navbar" data-theme="a"><ul>';

		if ($item->paged->has_previous) {
			$echo .= '
					<li><a class="gbutton fleft" href="' . site_url($base_url.'1') . '">«« First</a></li>
					<li><a class="gbutton fleft" href="' . site_url($base_url . $item->paged->previous_page) . '">« Prev</a></li>
				';
		}
		if ($item->paged->has_next) {
			$echo .= '
					<li><a class="gbutton fright" href="'.site_url($base_url.$item->paged->total_pages).'">Last »»</a></li>
					<li><a class="gbutton fright" href="'.site_url($base_url.$item->paged->next_page).'">Next »</a></li>
				';
		}
		$echo .= '</ul></div>';
		
		return $echo;
	}

}