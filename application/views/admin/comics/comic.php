<?php
echo form_open_multipart("");
echo $table;
echo form_close();
?>



<div class="section">Chapters:</div>
<div class="smalltext"><a href="<?= site_url('/admin/comics/'.$comic->stub.'/add_chapter'); ?>" onclick="slideToggle('#addnew_chapter'); return false;">Add new</a></div>



<div class="list chapters">

<?php

    foreach ($chapters as $item)
    {
        echo '<div class="item">
                <div class="title"><a href="'.site_url("admin/comics/comic/".$comic->stub."/".$item->id).'">'. (($item->name != "") ? $item->name : "Chapter ".$item->chapter.".".$item->subchapter).'</a></div>
                <div class="smalltext info">
                    Chapter #'.$item->chapter.'
                    Sub #'.$item->subchapter.'
                    By <a href="'.site_url("/admin/users/team/".$item->team_stub).'">'.$item->team_name.'</a>
                </div>
                <div class="smalltext">
                    <a href="#" onclick="">Quick tools</a>
                </div>';
             echo '</div>';
    }

?>
    
</div>


