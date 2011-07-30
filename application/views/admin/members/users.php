<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="smartsearch">
<?php
echo form_open();
echo form_input(array('name'=>'search', 'placeholder' => _('To search, write and hit enter')));
echo form_close();
?>
</div>
<?php
echo buttoner();

echo $table;
?>

