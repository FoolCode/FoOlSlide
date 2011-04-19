<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('tabler'))
{
    function tabler($rows, $list = TRUE, $edit = TRUE )
    {
        $result = array();
        $CI =& get_instance();
        
        
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
        
        $echo = "";
        
		if ($list && $edit)
		{
			$echo .= '<div class="smalltext">
				<a href="" onclick="slideToggle(\'.form.plain\'); slideToggle(\'.form.edit\'); return false;">Edit data</a>
					</div>';
		}
		
        if ($list)
        {
            $echo .= '<table class="form plain">';
            foreach($result as $rowk => $row)
            {
				if ($row[1]['table'] != 'Save'){
					$echo .= '<tr>';
					foreach($row as $column)
					{
						$echo .= '<td>'.$column['table'].'</td>';
					}
					$echo .= '</tr>';
				}
            }
            $echo .= '</table>';
        }
        
		
        if ($edit)
        {
            $echo .= '<table class="form edit"'.(($list && $edit)?'style="display:none;"':'').'>';
            foreach($result as $rowk => $row)
            {
                $echo .= '<tr>';
                foreach($row as $column)
                {
                    $echo .= '<td>'.$column['form'].'</td>';
                }
                $echo .= '</tr>';
            }
            $echo .= '</table>';
        }
        return $echo;
    }
}
    
    
if (!function_exists('formize'))
{
    function formize($column)
    {
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
        unset($column['type']);
        return $formize($column);
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
		
		$result[] = array(
                "",
                array(
                     'type'        => 'submit',
                     'name'        => 'submit',
                     'id'          => 'submit',
                     'value'       => 'Save'
                )
            );
		
		return $result;
	}
}