<?php
$this->buttoner[] = array(
	'text' => _('Delete chapter'),
	'href' => site_url('/admin/comics/delete/chapter/' . $chapter->id),
	'plug' => _('Do you really want to delete this chapter and its pages?')
);

$this->buttoner[] = array(
	'text' => _('Read chapter'),
	'href' => $chapter->href()
);

echo buttoner();

echo form_open();
echo $table;
echo form_close();
?>

<div class="section"><?php echo _("Pages") ?>:</div>


<?php
$session_name = $this->session->get_js_session(TRUE);
$session_data = $this->session->get_js_session();
?>

<div class="jquery-file-upload">
	<link href="<?php echo site_url(); ?>assets/jquery-file-upload/jquery-ui.css" rel="stylesheet" id="theme" />
	<link href="<?php echo site_url(); ?>assets/jquery-file-upload/jquery.fileupload-ui.css" rel="stylesheet" />
	<script type="text/javascript">
		
		function deleteImage(id)
		{
			jQuery.post('<?php echo site_url('/admin/comics/delete/page/') ?>', {id: id}, function(){
				jQuery('#image_' + id).hide();
			});
		}
	
		function deleteAllPages()
		{
			jQuery.post('<?php echo site_url('/admin/comics/delete/allpages/') ?>', {id: <?php echo $chapter->id ?>}, function(){
				location.reload();
			});
		}
	
		function updateSession()
		{
			jQuery.post('<?php echo site_url('/admin/comics/get_sess_id'); ?>', 
			function(result){
				
				jQuery('#file_upload').uploadifySettings( 'postData', {
					'ci_sessionz' : result.session, 
					'<?php echo $this->security->get_csrf_token_name(); ?>' : result.csrf, 
					'chapter_id' : <?php echo $chapter->id; ?>,
					'uploader' : 'uploadify',
					'overwrite' : '1'
				}, false );
				setTimeout('updateSession()', 6000);
			}, 'json');
		}

	</script>	
</div>

<?php echo form_open_multipart("", array('id' => 'file_upload')); ?>
	<input type="file" name="Filedata" multiple />
	<button><?php echo _('Upload ZIP and Images'); ?></button>
	<div><?php echo _('Upload Files'); ?></div>
<?php echo form_close(); ?>

<?php
$this->buttoner = array();
$this->buttoner[] = array(
	'text' => _('Delete all pages'),
	'href' => site_url('/admin/comics/delete/allpages/' . $chapter->id),
	'plug' => _('Do you really want to delete all the images in this chapter?'));
echo buttoner();
?>

<table id="files"></table>

<script src="<?php echo site_url(); ?>assets/js/jquery-ui.js"></script>
<script src="<?php echo site_url(); ?>assets/jquery-file-upload/jquery.fileupload.js"></script>
<script src="<?php echo site_url(); ?>assets/jquery-file-upload/jquery.fileupload-ui.js"></script>
<script>

	$(function () {
		$('#file_upload').fileUploadUI({
			url: '<?php echo site_url('/admin/comics/upload/compressed_chapter'); ?>',
			sequentialUploads: true,
			uploadTable: $('#files'),
			downloadTable: $('#files'),
			formData: [
				{
					name: 'chapter_id',
					value: <?php echo $chapter->id; ?>
				}, {
					name: 'uploader',
					value: 'jquery-file-upload'
				}, {
					name: 'overwrite',
					value: '1'
				}
			],
			buildUploadRow: function (files, index) {
				return $('<tr><td>' + files[index].name + '<\/td>' +
					'<td class="file_upload_progress"><div><\/div><\/td>' +
					'<td class="file_upload_cancel">' +
					'<button class="ui-state-default ui-corner-all" title="Cancel">' +
					'<span class="ui-icon ui-icon-cancel">Cancel<\/span>' +
					'<\/button><\/td><\/tr>');
			},
			buildDownloadRow: function (file) {
				return $('<tr><td>' + file.name + ' (' + file.size + ' KB) - <?php _('Uploaded'); ?><\/td><\/tr>');
			},
			onCompleteAll: function (result) {
				//window.location.reload(true);
			}
		});
	});
	
</script>

<div class="list pages">
	<?php
	$count = 0;
	foreach ($pages as $item) {
		$count++;
		echo '<div class="element">
                <div class="controls gbutton" onclick="deleteImage(' . $item['id'] . ')">' . _('Delete') . '</div>
                <img id="image_' . $item['id'] . '" src="' . $item["thumb_url"] . '" />
             </div>';
	}
	?> 
</div>