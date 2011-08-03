<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');


$CI = & get_instance();
$CI->buttoner = array(
	array(
		'href' => site_url('/admin/balancer/balancer_add/'),
		'text' => _('Add load balancer')
	)
);

echo buttoner();
echo '<div class="list comics">';

foreach ($balancers as $item)
{
	echo '<div class="item">
	<div class="title"><a href="' . site_url("admin/balancer/balancers/" . $item->id) . '">' . $item->url . '</a></div>';
	echo '</div>';
}

echo '</div>';