<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo _("Before using the load balancing system, you must of course setup a load balancing server. On that server, just install a clean FoOlSlide (don't create teams, chapters or anything!), go in the admin panel. There, go under the section LoadBalancer/Client, grab the key and enter the url to this Slide (probably \"%s\"), and use it here. Migration will be slow, but secure, at the rate of one chapter per minute. That's given that there are users using this Slide, else it will take longer. When the load balancer is up to date, it will activate automatically.");


echo buttoner();
echo form_open_multipart("");
echo $table;
echo form_close();