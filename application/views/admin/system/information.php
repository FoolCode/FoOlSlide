<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="table">
	<?php if (isset($form_title)) echo '<h3 style="float: left">' . $form_title . '</h3>'; ?>
	<span style="float: right; padding: 5px"><a href="#" class="btn" data-keyboard="true" data-backdrop="true" data-controls-modal="modal-for-information"><?php echo _('Output Server Information'); ?></a></span>
	<hr class="clear"/>
	<div style="margin-right: 10px; padding-bottom: 10px">
		<?php
			// name = non-localized for developers
			// title = localized for users
			$information['server'] = array(
				'name' => 'Server Information',
				'title' => _('Server Information'),
				'data' => array(
					array(
						'name' => 'Web Server Software',
						'title' => _('Web Server Software'),
						'value' => $_SERVER["SERVER_SOFTWARE"],
						'text' => _('The web server that is currently running to server your content.')
					),
					array(
						'name' => 'PHP Version',
						'title' => _('PHP Version'),
						'value' => phpversion(),
						'text' => _('The version of the currently running PHP parser.')
					)
				)
			);
			
			$information['software'] = array(
				'name' => 'Software Information',
				'title' => _('Software Information'),
				'data' => array(
					array(
						'name' => 'FoOlSlide Version',
						'title' => _('FoOlSlide Version'),
						'value' => get_setting('fs_priv_version'),
						'text' => _('The version of FoOlSlide that you are currently running on your server.')
					),
					array(
						'name' => 'Environment',
						'title' => _('Environment'),
						'value' => ucfirst(ENVIRONMENT),
						'text' => _('The environment FoOlSlide is current running as on the server.')
					)
				)
			);
			
			$information['configuration'] = array(
				'name' => 'PHP Configuration',
				'title' => _('PHP Configuration'),
				'data' => array(
					array(
						'name' => 'Memory Limit',
						'title' => _('Memory Limit'),
						'value' => ini_get('memory_limit'),
						'text' => _('This is the maximum amount of memory in bytes that a script is allowed to allocate.') . '<p class="vartext">' . _('Variable') . ': memory_limit</p>'
					),
					array(
						'name' => 'Max Execution Time',
						'title' => _('Max Execution Time'),
						'value' => ini_get('max_execution_time'),
						'text' => _('This is the maximum time in seconds a script is allowed to run before it is terminated by the parser.') . '<p class="vartext">' . _('Variable') . ': max_execution_time</p>'
					),
					array(
						'name' => 'File Uploads',
						'title' => _('File Uploads'),
						'value' => (ini_get('file_uploads')) ? _('Enabled') : _('Disabled'),
						'text' => _('This states whether or not to allow HTTP file uploads.') . '<p class="vartext">' . _('Variable') . ': file_uploads</p>'
					),
					array(
						'name' => 'Max POST Size',
						'title' => _('Max POST Size'),
						'value' => ini_get('post_max_size'),
						'text' => _('This is max size of post data allowed.') . '<p class="vartext">' . _('Variable') . ': post_max_size</p>'
					),
					array(
						'name' => 'Max Upload Size',
						'title' => _('Max Upload Size'),
						'value' => ini_get('upload_max_filesize'),
						'text' => _('This is the maximum size allowed for an uploaded file.') . '<p class="vartext">' . _('Variable') . ': upload_max_filesize</p>'
					),
					array(
						'name' => 'Max File Uploads',
						'title' => _('Max File Uploads'),
						'value' => ini_get('max_file_uploads'),
						'text' => _('This is the maximum number of files allowed to be uploaded simultaneously.') . '<p class="vartext">' . _('Variable') . ': max_file_uploads</p>'
					)
				)
			);
			
			$information['extensions'] = array(
				'name' => 'Extensions',
				'title' => _('Extensions'),
				'data' => array(
					array(
						'name' => 'GD2',
						'title' => 'GD2',
						'value' => (extension_loaded('gd')) ? _('Installed') : _('Missing'),
						'text' => _('This is a library used to dynamicly create images and thumbnails.')
					),
					array(
						'name' => 'ImageMagick',
						'title' => 'ImageMagick',
						'value' => (extension_loaded('imagick')) ? _('Installed') : _('Not Installed'),
						'text' => _('This is a library used to dynamicaly create, edit, compose or convert images.') . '<p class="vartext">' . _('Optional') . '</p>'
					)
				)
			);
			
			// Output Tables
			foreach ($information as $key => $item) {
				echo '<h5>' . $item['title'] . '</h5>';
				echo '<table class="zebra-striped fixed-table"><tbody>';
				foreach ($item['data'] as $subkey => $subitem) {
					$tooltip = (isset($subitem['text']) && $subitem['text'] != "") ? '<a rel="popover-right" href="#" data-content="' . htmlspecialchars($subitem['text']) . '" data-original-title="' . htmlspecialchars($subitem['title']) . '"><img src="' . icons(388, 16) . '" class="icon icon-small"></a>' : '';
					echo '<tr><td>' . $subitem['title'] . ' ' . $tooltip . '</td><td>' . $subitem['value'] . '</td></tr>';
				}
				echo '</tbody></table>';
			}
		?>
		
		<?php echo _('If you are asked to provide an output of your server information, please click the "Output Server Information" button at the top right and provide it to us via <a href="http://pastebin.com">Pastebin</a> or some similar service.'); ?>
	</div>
</div>

<div id="modal-for-information" class="modal hide fade" style="display: none">
	<div class="modal-header">
		<a class="close" href="#">&times;</a>
		<h3><?php echo _('System Information'); ?></h3>
	</div>
	<div class="modal-body" style="text-align: center">
		<textarea id="server-information-output" style="min-height: 300px; font-family: Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace !important" readonly="readonly"><?php
			foreach ($information as $key => $item) {
				echo $item['name'] . "\n";
				echo "------------------------------\n";
				foreach ($item['data'] as $subkey => $subitem) {
					echo $subitem['name'] . ' = ' . $subitem['value'] . "\n";
				}
				echo "\n";
			}
			echo 'Report Generated: ' . date(DATE_RFC822) . "\n";
		?></textarea>
	</div>
	<div class="modal-footer">

	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function() {
		var modalInfoContainer = jQuery("#modal-for-information").find("#server-information-output");
		modalInfoContainer.click(function() {
			modalInfoContainer.select();
			// Chrome Fix
			modalInfoContainer.mouseup(function() { modalInfoContainer.unbind('mouseup'); return false; });
		});	
	});
</script>