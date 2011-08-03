<?php
$CI = & get_instance();
$CI->buttoner = array(
	array(
		'href' => site_url('/admin/balancer/balancer_remove/'.$balancer->id),
		'text' => _('Remove load balancer'),
		'plug' => _('Do you really want to remove this load balancer? The content on the load balancer will be left intact, the connections will just be refused.')
	)
);
echo buttoner();

echo form_open_multipart("");
echo $table;
echo form_close();