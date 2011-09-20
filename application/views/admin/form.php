<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo buttoner();
echo form_open_multipart("", array('class' => 'form-stacked'));
echo $table;
echo form_close();