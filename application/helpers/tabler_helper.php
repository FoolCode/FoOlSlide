<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('tabler'))
{
    function tabler($rows, $list = TRUE, $edit = TRUE )
    {
        $result = array();
        $CI =& get_instance();
        
		
		$rows[] = array(
                "",
                array(
                     'type'        => 'submit',
                     'name'        => 'submit',
                     'id'          => 'submit',
                     'value'       => 'Save'
                )
            );
		
        $echo = "";

        foreach($rows as $rowk => $row)
        {
            foreach ($row as $colk => $column)
            {
                if ($colk == 0)
                {
                    $result[$rowk][$colk]["table"] = $column;
                    $result[$rowk][$colk]["form"] = $column;
                }
                else
                {
                    if(!isset($column['value'])) $column['value'] = "";
                    if(is_array($column))
                    {
                        $result[$rowk][$colk]["table"] = $column['value'];
                        if(isset($column['type'])) $result[$rowk][$colk]["form"] = formize($column);
                    }
                    else 
                    {
                        $result[$rowk][$colk]["table"] = $column;
                        $result[$rowk][$colk]["form"] = $column;
                    }    
                }
            }
        }
        
        // echo '<pre>'; print_r($result); echo '</pre>';
		if ($list && $edit)
		{
			$echo .= '<div class="smalltext">
				<a href="" onclick="slideToggle(\'.plain\'); slideToggle(\'.edit\'); return false;">Edit data</a>
					</div>';
		}
		
        if ($list)
        {
            $echo .= '<div class="plain"><table class="form">';
            foreach($result as $rowk => $row)
            {
				if ($row[1]['table'] != 'Save' && $row[0]['table'] != 'id'){
					$echo .= '<tr>';
					foreach($row as $column)
					{
						$echo .= '<td>';
						if (is_array($column['table']))
						{
							foreach($column['table'] as $mini)
							{
								$echo .= ''.$mini->name.' ';
							}
						}
						else $echo .= $column['table'];
						$echo .= '</td>';
					}
					$echo .= '</tr>';
				}
            }
            $echo .= '</table></div>';
        }
        
		
        if ($edit)
        {
            $echo .= '<div class="edit" '.(($list && $edit)?'style="display:none;"':'').'><table class="form">';
            foreach($result as $rowk => $row)
            {
				if (false)
				{
					//$echo .= $row[1]['form'];
				}
				else
				{
					$echo .= '<tr>';
					foreach($row as $column)
						{
							$echo .= '<td>';
							if (is_array($column['form']))
							{
								foreach($column['form'] as $mini)
								{
									$echo .= ''.$mini.' ';
								}
							}
							else $echo .= $column['form'];
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
    
    
if (!function_exists('formize'))
{
    function formize($column)
    {
		// FIX THIS ABSURDITY: no need to always check in database this bull!
        $CI =& get_instance();
        $query = $CI->db->get_where('preferences', array('name' => $column['name']), 1);
        $qarray = array();
        foreach($query->result() as $row)
        {
            $column['value'] = $row->value;
        }
                
        if($column['type'] == 'checkbox')
        {
           $column['value'] = '1';
        }
        
        $formize = 'form_'.$column['type'];
		$type = $column['type'];
        unset($column['type']);
		
		if(is_array($column['value']))
		{
			$result = array();
			$column['name'] .= '[]';
			$minion = $column['value'];
			foreach($minion as $mini)
			{
				$column['value'] = $mini->name;
				$result[] = $formize($column);
			}
		}
		else
		{
			//echo '<pre>'; print_r($column); echo '</pre>';
			if($type == 'hidden')
			{
				$result = $formize($column['name'], $column['value']);
			}
			else $result = $formize($column);
		}
		
		
        return $result;
    }
}


if (!function_exists('lister'))
{
    function lister($rows)
    {
        $echo = '<div class="list">';
        foreach ($rows as $row)
        {
            if(!isset($row['smalltext_r'])) $row['smalltext_r'] = "";
            if(!isset($row['smalltext'])) $row['smalltext'] = "";

            $echo .= '<div class="item">
                    <div class="title">'.
                        $row['title'].
                        '</div>
                    <div class="smalltext info">'.
                       $row['smalltext_r'].
                    '</div>
                    <div class="smalltext">'.
                        $row['smalltext'].
                    '</div>';
                 $echo .= '</div>';
        }
        return $echo.'</div>';
    }
}


if (!function_exists('ormer'))
{
    function ormer($db)
    {	
		$result = array();
		$rows = $db->validation;
		foreach($rows as $key => $row)
		{
			if($key == 'id')
			{
				$row['type'] = 'hidden';
			}
			
			if(!isset($row['value'])) $row['value'] = '';
			
			if(isset($row['type']))
			{	
				if($db->$key != "") $row['value'] = $db->$key;
				$result[] = array(
					$row['label'],
					array(
						'name' => addslashes($key),
						'value' => addslashes($row['value']),
						'type' => addslashes($row['type'])
					)
				);
			}
		}
		//		echo '<pre>'; print_r($result); echo '</pre>';

		return $result;
	}
}