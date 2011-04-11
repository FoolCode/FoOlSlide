<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="content">
    <div class="list">
        <div class="title">List of available series:</title>
        <?php
            foreach($comic->all as $key => $item)
            {
                echo '<div class="element">';
                echo $item->stub;
                echo '<div class="details">Latest chapter: '.$item->chapter->chapter.'</div>';
                echo '</div>';
            }
        ?>
    </div>
    
    <div class="list">
        <?php
            $chapters = new Chapter();
            $chapters->order_by('created')->limit(6)->get();

            foreach($chapters->all as $chapter)
            {
                $comic = new Comic();
                $comic->where('id', $chapter->comic_id)->get();
                echo '<div class="element">';
                echo $comic->name.': Chapter '.$item->chapter;
                echo '</div>';
            }
        ?>
    </div>
</div>
