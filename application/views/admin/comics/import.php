<?php echo _('Here you can select a directory and import chapters from it. Make sure you already created a comic for the chapters you\'re going to add. After you press on save, you will get a list of chapters you can add, so you can refine the importing.'); 
?>
<br/><br/>
<?php 
echo form_open();
echo $archive; 
echo form_close();
?>
<br/><br/>
<?php echo _('When importing from FoOlReader, the system will try guessing the name of the comics you\'re importing.'); ?>
<br/><br/>
<?php
echo form_open();
echo $foolreader;
echo form_close();
?>

