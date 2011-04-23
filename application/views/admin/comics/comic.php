<?php
$this->buttoner[] = array(
	'text' => 'Delete comic',
	'href' => site_url('/admin/comics/delete/comic/'.$comic->id),
	'plug' => 'Do you really want to delete this comic and its chapters?'
);

echo buttoner();

echo form_open_multipart("");
echo $table;
echo form_close();
?>



<div class="section">Chapters:</div>
<?php
	$this->buttoner = array(
		array(
			'href' => site_url('/admin/comics/add_new/'.$comic->stub),
			'text' => 'Add chapter'
		)
	);
			
	echo buttoner();
?>


<div class="list chapters">

<?php

    foreach ($chapters as $item)
    {
        echo '<div class="item">
                <div class="title"><a href="'.site_url("admin/comics/comic/".$comic->stub."/".$item->id).'">'. (($item->name != "") ? $item->name : "Chapter ".$item->chapter.".".$item->subchapter).'</a></div>
                <div class="smalltext info">
                    Chapter #'.$item->chapter.'
                    Sub #'.$item->subchapter;
					if(isset($item->jointers))
					{
						echo ' By ';
						foreach($item->jointers as $key2 => $jointe)
						{
							if($key2>0) echo " | ";
							echo '<a href="'.site_url("/admin/users/teams/".$jointe->stub).'">'.$jointe->name.'</a>';
						}
					}
					else echo '
                    By <a href="'.site_url("/admin/users/teams/".$item->team_stub).'">'.$item->team_name.'</a>';
                echo '</div>
                <div class="smalltext">
                    <a href="#" onclick="">Quick tools</a>
                </div>';
             echo '</div>';
    }

?>
    
</div>


