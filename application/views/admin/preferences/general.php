<?php


$form = array();


$form[] = array(
    'Site title',
    array(
         'type'        => 'input',
         'name'        => 'fs_gen_site_title',
         'id'          => 'site_title',
         'maxlength'   => '200',
         'placeholder' => 'manga reader'
    )
);

$form[] = array(
    'Back URL',
    array(
         'type'        => 'input',
         'name'        => 'fs_gen_back_url',
         'id'          => 'back_url',
         'maxlength'   => '200',
         'placeholder' => 'http://'
    )
);

$form[] = array(
    'Default team',
    array(
         'type'        => 'input',
         'name'        => 'fs_gen_default_team',
         'id'          => 'default_team',
         'maxlength'   => '200',
         'placeholder' => 'Anonymous'
    )
);

$form[] = array(
    'Show Anonymous as team?',
    array(
         'type'        => 'checkbox',
         'name'        => 'fs_gen_anon_team_show',
         'id'          => 'anon_team_show',
         'placeholder' => ''
    )
);

$form[] = array(
    "",
    array(
         'type'        => 'submit',
         'name'        => 'submit',
         'id'          => 'submit',
         'value' => 'Save'
    )
);

echo form_open('admin/preferences/submit', '', array("goto" => site_url('admin/preferences/general')));
tabler($form, FALSE);
echo form_close();