<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('tabler'))
{
    function tabler($rows, $list = TRUE, $edit = TRUE )
    {
        # echo '<pre>'.print_r($rows).'</pre>';
        $result = array();
        
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
                    $result[$rowk][$colk]["table"] = $column['value'];
                    $result[$rowk][$colk]["form"] = formize($column);
                }
            }
        }
                    
        if ($list)
        {
            echo '<table class="form">';
            foreach($result as $rowk => $row)
            {
                echo '<tr>';
                foreach($row as $column)
                {
                    echo '<td>'.$column['table'].'</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        }
        
        if ($edit)
        {
            echo '<table class="form">';
            foreach($result as $rowk => $row)
            {
                echo '<tr>';
                foreach($row as $column)
                {
                    echo '<td>'.$column['form'].'</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        }
    }
}
    
    
if (!function_exists('formize'))
    {
    function formize($column)
    {
        if($column['type'] == 'checkbox')
        {
           $column['value'] = '1';
        }
         
        $formize = 'form_'.$column['type'];
        unset($column['type']);
        return $formize($column);
    }
}
