<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="table">
	<h3><?php echo _('Banlancers'); ?></h3>
	<span class="clearfix">
	<?php
	echo _("Load balancers for FoOlSlide are servers that contain the images cloned from the master server. You can use an external service to do this (like a Content Delivery Network), or use a second FoOlSlide in \"client mode\" on another server.");

	echo '<br/><br/>';

	echo sprintf(_(" Setting up a FoOlSlide balancer is really easy: just install a slide on the other server, tell it the URL to this slide (%s), and add the URL to the other slide down here. The rest is automatic."), site_url());

	echo '<br/><br/>';

	echo _("The % is for ease. You can set each host to any %. If you set two balancers at 100%, they will actually work at 50% each");

	echo '<br/><br/>';


	echo _("The load balancer can't support download of compressed archives. Those will still be served by the master FoOlSlide. Don't worry: less than 20% readers downloads. Just keep this in mind while distributing the %.");

	echo '<br/><br/>';
	?>
	</span>
	<div style="padding-right: 10px">
		<?php echo form_open(); ?>
		<table class="zebra-striped">
			<thead>
				<th><?php echo _('URL'); ?></th>
				<th><?php echo _('Priority'); ?></th>
			</thead>
			<tbody>
			<?php
				foreach ($balancers as $key => $item)
				{
					echo '<tr>
						<td>'.form_input('url[]', $item["url"]).'</td>
						<td width="140px">
							<div class="input-append">
								<input style="text-align: right; width: 100px" type="number" name="priority[]" min="0" max="100" value="'.form_prep($item["priority"]).'" />
								<span class="add-on">%</span>
							</div>
						</td>';
					echo '</tr>';
				}
			?>
			</tbody>
		</table>
	<?php
		echo form_submit('submit', _("Add/Save"));
		echo form_close();
		
		$form = array();
		$form[] = array(
			_('IPs of load balancing servers'),
			array(
				'type' => 'input',
				'value' => (isset($ips) && is_array($ips))?$ips:array(),
				'name' => 'fs_balancer_ips',
				'help' => _('Add the IPs of the servers used to balance. This will prevent them from being limited via the nationality filter.')
			)
		);
		echo form_open('', array('class' => 'form-stacked'));
		echo tabler($form, FALSE, TRUE);
		echo form_close();
	?>
	</div>
</div>
